<?php

namespace App\Livewire\Admin;

use App\Models\DashSetting;
use App\Models\Device;
use App\Models\SensorReading;
use App\Services\FuzzyRiskServices;
use Illuminate\Support\Carbon;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Title("Dashboard")]
class Dashboard extends Component
{
    // ===== Realtime state =====
    public array $data = [];
    public string $risk = "AMAN";
    public float|int $riskScore = 0;

    // ===== RBAC =====
    public bool $canManageDevices = false;

    /** @var array<int, array{id:int,name:string}> */
    public array $devices = [];

    /** @var array<int, array{online:bool,last:?string,status:?string}> */
    public array $deviceStatus = [];

    // ===== Settings =====
    public bool $showSettings = false;
    public string $theme = "dark";
    public ?int $selectedDeviceId = null;
    public string $chartMetric = "ketinggian_air";
    public string $selectedTimeRange = "5m";
    public array $visibleSensors = [];

    // ===== Time ranges =====
    public array $timeRanges = [
        "1m"  => "1 Menit",
        "5m"  => "5 Menit",
        "30m" => "30 Menit",
        "1h"  => "1 Jam",
        "12h" => "12 Jam",
        "24h" => "24 Jam",
        "1w"  => "1 Minggu",
        "1mo" => "1 Bulan",
        "1y"  => "1 Tahun",
    ];

    public array $metricLabels = [
        "suhu"            => "Temperature (°C)",
        "kelembapan"      => "Kelembapan (%)",
        "tekanan_udara"   => "Tekanan Udara (hPa)",
        "kecepatan_angin" => "Kecepatan Angin (m/s)",
        "arah_angin"      => "Arah Angin (°)",
        "ketinggian_air"  => "Ketinggian Air (cm)",
    ];

    // ===== Modal metric chart =====
    public bool $modalOpen = false;
    public string $selectedMetric = "ketinggian_air";
    public string $modalTimeRange = "5m";

    protected $listeners = [
        "open-dashboard-settings" => "openSettings",
    ];

    public function mount(): void
    {
        $user = auth()->user();
        $this->canManageDevices = $user?->can("manage devices") ?? false;

        $setting = DashSetting::query()
            ->where("user_id", auth()->id())
            ->first();

        if (!$setting) {
            $setting = DashSetting::query()->create([
                "user_id" => auth()->id(),
                "theme" => "dark",
                "selected_device_id" => null,
                "selected_sensor" => "ketinggian_air",
                "selected_time_range" => "5m",
                "visible_sensors" => json_encode([
                    "suhu",
                    "kelembapan",
                    "tekanan_udara",
                    "kecepatan_angin",
                    "arah_angin",
                    "ketinggian_air",
                ]),
            ]);
        }

        $this->devices = $this->loadAllowedDevices($setting);

        if (empty($this->devices)) {
            $this->theme = $setting->theme ?? "dark";
            return;
        }

        $this->theme = $setting->theme ?? "dark";

        $requestedDeviceId = (int) ($setting->selected_device_id ?? $this->devices[0]["id"]);
        $resolvedDeviceId = $this->resolveAllowedDeviceId($requestedDeviceId);

        $this->selectedDeviceId = $resolvedDeviceId;

        if ((int) ($setting->selected_device_id ?? 0) !== $resolvedDeviceId) {
            $setting->update([
                "selected_device_id" => $resolvedDeviceId,
            ]);
        }

        $this->chartMetric = array_key_exists($setting->selected_sensor, $this->metricLabels)
            ? $setting->selected_sensor
            : "ketinggian_air";

        $this->selectedTimeRange = array_key_exists($setting->selected_time_range, $this->timeRanges)
            ? $setting->selected_time_range
            : "5m";

        $this->modalTimeRange = $this->selectedTimeRange;

        $decoded = [];
        if (is_string($setting->visible_sensors)) {
            $decoded = json_decode($setting->visible_sensors, true) ?: [];
        } elseif (is_array($setting->visible_sensors)) {
            $decoded = $setting->visible_sensors;
        }

        $filteredVisible = array_values(array_filter(
            $decoded,
            fn($key) => array_key_exists($key, $this->metricLabels)
        ));

        $this->visibleSensors = !empty($filteredVisible)
            ? $filteredVisible
            : ["suhu", "kelembapan", "ketinggian_air"];

        $this->refreshDeviceStatus();
        $this->fetchData();
    }

    private function loadAllowedDevices(DashSetting $setting): array
    {
        $user = auth()->user();

        if ($this->canManageDevices) {
            return Device::query()
                ->orderBy("id")
                ->get(["id", "name", "alias"])
                ->map(fn($d) => [
                    "id" => (int) $d->id,
                    "name" => $d->name ?? ("ROB " . $d->id),
                    "alias" => $d->alias ?? null,
                    "label" => $d->alias ?: ($d->name ?? ("ROB " . $d->id)),
                ])
                ->toArray();
        }

        $owned = $user?->devices()
            ->orderBy("devices.id")
            ->get(["devices.id", "devices.name", "devices.alias"]);

        $list = $owned?->map(fn($d) => [
            "id" => (int) $d->id,
            "name" => $d->name ?? ("ROB " . $d->id),
            "alias" => $d->alias ?? null,
            "label" => $d->alias ?: ($d->name ?? ("ROB " . $d->id)),
        ])->toArray() ?? [];

        if (!empty($list) && empty($setting->selected_device_id)) {
            DashSetting::query()
                ->where("user_id", auth()->id())
                ->update([
                    "selected_device_id" => (int) $list[0]["id"],
                ]);
        }

        return $list;
    }

    private function resolveAllowedDeviceId(int $requestedId): int
    {
        $allowed = array_map(fn($d) => (int) $d["id"], $this->devices);

        return in_array($requestedId, $allowed, true)
            ? $requestedId
            : (int) $allowed[0];
    }

    private function assertCanAccessSelectedDevice(): void
    {
        if ($this->canManageDevices) {
            return;
        }

        $allowed = array_map(fn($d) => (int) $d["id"], $this->devices);

        if (!$this->selectedDeviceId || !in_array((int) $this->selectedDeviceId, $allowed, true)) {
            abort(403, "Unauthorized device access");
        }
    }

    private function refreshDeviceStatus(): void
    {
        $ids = array_map(fn($d) => (int) $d["id"], $this->devices);

        $rows = Device::query()
            ->whereIn("id", $ids)
            ->get(["id", "status", "last_seen"])
            ->keyBy("id");

        $now = now();
        $out = [];

        foreach ($ids as $id) {
            $r = $rows->get($id);
            $status = $r?->status;
            $last = $r?->last_seen;

            $online = false;

            if ($status === "online") {
                $online = true;
            } elseif ($last) {
                $diffSec = $now->diffInSeconds(Carbon::parse($last, 'UTC'), false);
                $online = abs($diffSec) <= 120;
            }

            $out[$id] = [
                "online" => (bool) $online,
                "status" => $status,
                "last" => $last,
            ];
        }

        $this->deviceStatus = $out;
    }

    public function openSettings(): void
    {
        $this->showSettings = true;
    }

    public function closeSettings(): void
    {
        $this->showSettings = false;
    }

    public function updated($property): void
    {
        if ($property === "selectedDeviceId") {
            if (empty($this->devices)) {
                return;
            }

            $this->selectedDeviceId = $this->resolveAllowedDeviceId((int) $this->selectedDeviceId);
            $this->assertCanAccessSelectedDevice();

            $this->persistSettings();
            $this->fetchData();
            return;
        }

        if ($property === "visibleSensors") {
            $this->visibleSensors = array_values(array_filter(
                $this->visibleSensors,
                fn($key) => array_key_exists($key, $this->metricLabels)
            ));
        }

        if (in_array($property, ["theme", "chartMetric", "selectedTimeRange", "visibleSensors"], true)) {
            if (!$this->selectedDeviceId && !empty($this->devices)) {
                $this->selectedDeviceId = (int) $this->devices[0]["id"];
            }

            if (!$this->selectedDeviceId) {
                return;
            }

            if (!array_key_exists($this->chartMetric, $this->metricLabels)) {
                $this->chartMetric = "ketinggian_air";
            }

            if (!array_key_exists($this->selectedTimeRange, $this->timeRanges)) {
                $this->selectedTimeRange = "5m";
            }

            $this->persistSettings();
            $this->fetchData();

            if ($property === "selectedTimeRange") {
                $this->dispatchMainChart();
            }

            return;
        }

        if ($property === "modalTimeRange" && $this->modalOpen) {
            if (!array_key_exists($this->modalTimeRange, $this->timeRanges)) {
                $this->modalTimeRange = "5m";
            }

            $this->dispatchMetricChart();
        }
    }

    private function persistSettings(): void
    {
        DashSetting::query()->updateOrCreate(
            ["user_id" => auth()->id()],
            [
                "theme" => $this->theme,
                "selected_device_id" => $this->selectedDeviceId,
                "selected_sensor" => $this->chartMetric,
                "selected_time_range" => $this->selectedTimeRange,
                "visible_sensors" => json_encode(array_values($this->visibleSensors)),
            ]
        );
    }

    public function fetchData(): void
    {
        if (!$this->selectedDeviceId) {
            return;
        }

        $this->assertCanAccessSelectedDevice();
        $this->refreshDeviceStatus();

        $latest = SensorReading::query()
            ->where("device_id", $this->selectedDeviceId)
            ->latest("timestamp")
            ->first();

        if (!$latest) {
            $this->data = [];
            $this->risk = 'AMAN';
            $this->riskScore = 0;

            $this->dispatch(
                "dashboard-updated",
                data: $this->data,
                risk: $this->risk,
                riskScore: $this->riskScore,
                riskStyles: $this->riskStyles,
            );

            return;
        }

        $this->data = [
            "project" => $latest->project ?? "ROB Monitoring",
            "timestamp" => $latest->timestamp,
            "suhu" => $latest->suhu,
            "tekanan_udara" => $latest->tekanan_udara,
            "kelembapan" => $latest->kelembapan,
            "ketinggian_air" => $latest->ketinggian_air,
            "arah_angin" => $latest->arah_angin,
            "kecepatan_angin" => $latest->kecepatan_angin,
            "arah_angin_label" => $this->getWindDirectionLabel($latest->arah_angin),
        ];

        $this->calculateRisk();
        $this->dispatchMainChart();

        $this->dispatch(
            "dashboard-updated",
            data: $this->data,
            risk: $this->risk,
            riskScore: $this->riskScore,
            riskStyles: $this->riskStyles
        );

        if ($this->modalOpen) {
            $this->dispatchMetricChart();
        }
    }

    private function rangeStartUtc(string $range): ?Carbon
    {
        $now = now("UTC");

        return match ($range) {
            "1m"  => $now->copy()->subMinute(),
            "5m"  => $now->copy()->subMinutes(5),
            "30m" => $now->copy()->subMinutes(30),
            "1h"  => $now->copy()->subHour(),
            "12h" => $now->copy()->subHours(12),
            "24h" => $now->copy()->subDay(),
            "1w"  => $now->copy()->subWeek(),
            "1mo" => $now->copy()->subMonth(),
            "1y"  => $now->copy()->subYear(),
            default => null,
        };
    }

    private function applyTimeRangeUtc($query, string $range)
    {
        $start = $this->rangeStartUtc($range);
        return $start ? $query->where("timestamp", ">=", $start) : $query;
    }

    private function takePointsForRange(string $range): int
    {
        return match ($range) {
            "1m"  => 120,
            "5m"  => 240,
            "30m" => 300,
            "1h"  => 600,
            "12h" => 1200,
            "24h" => 1800,
            "1w"  => 2500,
            "1mo" => 4000,
            "1y"  => 6000,
            default => 300,
        };
    }

    private function dispatchMainChart(): void
    {
        if (!$this->selectedDeviceId) {
            return;
        }

        $this->assertCanAccessSelectedDevice();

        $metric = array_key_exists($this->chartMetric, $this->metricLabels)
            ? $this->chartMetric
            : "ketinggian_air";

        $base = SensorReading::query()
            ->where("device_id", $this->selectedDeviceId);

        $query = $this->applyTimeRangeUtc(clone $base, $this->selectedTimeRange);
        $take = $this->takePointsForRange($this->selectedTimeRange);

        $records = $query->latest("timestamp")
            ->take($take)
            ->get(["timestamp", $metric]);

        if ($records->count() === 0) {
            $records = $base->latest("timestamp")
                ->take(300)
                ->get(["timestamp", $metric]);
        }

        $records = $records->reverse()->values();
        $tz = config("app.timezone", "Asia/Jakarta");

        $labels = $records->pluck("timestamp")
            ->map(fn($t) => Carbon::parse($t, 'UTC')->setTimezone($tz)->format('d M H:i'))
            ->toArray();

        $values = $records->pluck($metric)
            ->map(fn($v) => (float) ($v ?? 0))
            ->toArray();

        $this->dispatch(
            "refreshChart",
            labels: $labels,
            values: $values,
            title: $this->metricLabels[$metric] ?? $metric
        );
    }

    private function calculateRisk(): void
    {
        $result = app(FuzzyRiskServices::class)->evaluate([
            'ketinggian_air' => (float) ($this->data['ketinggian_air'] ?? 0),
            'tekanan_udara' => (float) ($this->data['tekanan_udara'] ?? 0),
            'kecepatan_angin' => (float) ($this->data['kecepatan_angin'] ?? 0),
            'arah_angin' => (float) ($this->data['arah_angin'] ?? 0),
        ]);

        $this->riskScore = $result['score'];
        $this->risk = $result['label'];
    }

    public function getRiskStylesProperty(): array
    {
        return match ($this->risk) {
            "BAHAYA" => [
                "bg" => "bg-red-500/10",
                "border" => "border-red-500/30",
                "text" => "text-red-500",
            ],
            "SIAGA" => [
                "bg" => "bg-yellow-500/10",
                "border" => "border-yellow-500/30",
                "text" => "text-yellow-600",
            ],
            "WASPADA" => [
                "bg" => "bg-orange-500/10",
                "border" => "border-orange-500/30",
                "text" => "text-orange-600",
            ],
            default => [
                "bg" => "bg-emerald-500/10",
                "border" => "border-emerald-500/30",
                "text" => "text-emerald-600",
            ],
        };
    }

    private function getWindDirectionLabel($degree): string
    {
        if ($degree === null) {
            return "-";
        }

        $dirs = ["Utara", "Timur Laut", "Timur", "Tenggara", "Selatan", "Barat Daya", "Barat", "Barat Laut"];
        $idx = (int) floor(((float) $degree + 22.5) / 45) % 8;

        return $dirs[$idx];
    }

    public function openMetric(string $metric): void
    {
        if (!array_key_exists($metric, $this->metricLabels)) {
            return;
        }

        $this->selectedMetric = $metric;
        $this->modalOpen = true;
        $this->modalTimeRange = $this->selectedTimeRange ?: "5m";

        $this->dispatchMetricChart();
    }

    public function closeModal(): void
    {
        $this->modalOpen = false;
        $this->dispatch("destroyModalChart");
    }

    public function pollMetric(): void
    {
        if (!$this->modalOpen) {
            return;
        }

        $this->dispatchMetricChart();
    }

    protected function dispatchMetricChart(): void
    {
        if (!$this->selectedDeviceId) {
            return;
        }

        $this->assertCanAccessSelectedDevice();

        $metric = array_key_exists($this->selectedMetric, $this->metricLabels)
            ? $this->selectedMetric
            : "ketinggian_air";

        $base = SensorReading::query()
            ->where("device_id", $this->selectedDeviceId);

        $query = $this->applyTimeRangeUtc(clone $base, $this->modalTimeRange);
        $take = $this->takePointsForRange($this->modalTimeRange);

        $rows = $query->latest("timestamp")
            ->take($take)
            ->get(["timestamp", $metric]);

        if ($rows->count() === 0) {
            $rows = $base->latest("timestamp")
                ->take(300)
                ->get(["timestamp", $metric]);
        }

        $rows = $rows->reverse()->values();
        $tz = config("app.timezone", "Asia/Jakarta");

        $labels = $rows->map(
            fn($r) => Carbon::parse($r->timestamp, 'UTC')->setTimezone($tz)->format('d M H:i')
        )->all();

        $values = $rows->map(
            fn($r) => (float) ($r->{$metric} ?? 0)
        )->all();

        $this->dispatch(
            "modalChart",
            title: $this->metricLabels[$metric] ?? $metric,
            labels: $labels,
            values: $values
        );
    }

    public function render()
    {
        return view("livewire.admin.dashboard");
    }
}