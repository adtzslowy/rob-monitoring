<?php

namespace App\Livewire\Admin;

use App\Models\DashSetting;
use App\Models\Device;
use App\Models\SensorReading;
use Illuminate\Support\Carbon;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Title("Dashboard")]
class Dashboard extends Component
{
    // ===== Realtime state =====
    public array $data = [];
    public string $risk = "AMAN";
    public float|int $riskScore = 1;

    // ===== RBAC =====
    public bool $canManageDevices = false;

    /** @var array<int, array{id:int,name:string}> */
    public array $devices = [];

    /** @var array<int, array{online:bool,last:?string,status:?string}> */
    public array $deviceStatus = [];

    // ===== Settings =====
    public bool $showSettings = false;

    /** theme: 'dark'|'light' */
    public string $theme = "dark";

    /** device aktif */
    public ?int $selectedDeviceId = null;

    /** sensor untuk chart utama */
    public string $chartMetric = "ketinggian_air";

    /** range chart utama */
    public string $selectedTimeRange = "5m";

    /** sensor yang tampil sebagai kartu (operator/admin boleh pilih) */
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

        // load / create setting
        $setting = DashSetting::query()->where("user_id", auth()->id())->first();

        if (!$setting) {
            $setting = DashSetting::query()->create([
                "user_id" => auth()->id(),
                "theme" => "dark",
                "selected_device_id" => null, // nanti kita set setelah tahu allowed devices
                "selected_sensor" => "ketinggian_air",
                "selected_time_range" => "5m",
                "visible_sensors" => json_encode(["suhu","kelembapan","tekanan_udara","kecepatan_angin","arah_angin","ketinggian_air"]),
            ]);
        }

        // ✅ devices allowed (admin: all, operator: only owned devices)
        $this->devices = $this->loadAllowedDevices($setting);

        // kalau operator belum dikasih alat sama admin
        if (empty($this->devices)) {
            // Kamu bisa tampilkan empty state di blade kalau mau
            // (jangan abort biar halaman tetap render)
            $this->theme = $setting->theme ?? "dark";
            $this->dispatch('theme-sync', theme: $this->theme);
            return;
        }

        // apply state
        $this->theme = $setting->theme ?? "dark";

        // resolve selectedDeviceId agar selalu valid
        $requestedDeviceId = (int) ($setting->selected_device_id ?? $this->devices[0]["id"]);
        $resolved = $this->resolveAllowedDeviceId($requestedDeviceId);

        // kalau settingnya invalid, kita betulin + simpan
        $this->selectedDeviceId = $resolved;
        if ((int)($setting->selected_device_id ?? 0) !== $resolved) {
            $setting->update(["selected_device_id" => $resolved]);
        }

        // chart metric: pakai selected_sensor kolom existing
        $this->chartMetric = $setting->selected_sensor ?: "ketinggian_air";

        $this->selectedTimeRange = $setting->selected_time_range ?: "5m";
        $this->modalTimeRange = $this->selectedTimeRange;

        // visible sensors
        $decoded = [];
        if (is_string($setting->visible_sensors)) {
            $decoded = json_decode($setting->visible_sensors, true) ?: [];
        } elseif (is_array($setting->visible_sensors)) {
            $decoded = $setting->visible_sensors;
        }
        $this->visibleSensors = !empty($decoded) ? array_values($decoded) : ["suhu","kelembapan","ketinggian_air"];

        // status cache
        $this->refreshDeviceStatus();

        // fetch awal
        $this->fetchData();

        // sync tema ke frontend
        $this->dispatch('theme-sync', theme: $this->theme);
    }

    /**
     * Admin: semua device
     * Operator: device berdasarkan pivot user->devices()
     */
    private function loadAllowedDevices(DashSetting $setting): array
    {
        $user = auth()->user();

        if ($this->canManageDevices) {
            return Device::query()
                ->orderBy("id")
                ->get(["id", "name"])
                ->map(fn($d) => ["id" => (int)$d->id, "name" => $d->name ?? ("ROB ".$d->id)])
                ->toArray();
        }

        // ✅ operator: ambil dari relasi many-to-many
        // pastikan di User model ada devices() belongsToMany
        $owned = $user?->devices()
            ->orderBy("devices.id")
            ->get(["devices.id", "devices.name"]);

        $list = $owned?->map(fn($d) => [
            "id" => (int)$d->id,
            "name" => $d->name ?? ("ROB ".$d->id),
        ])->toArray() ?? [];

        // kalau selected_device_id belum ada, set ke first owned
        if (!empty($list) && empty($setting->selected_device_id)) {
            DashSetting::query()
                ->where("user_id", auth()->id())
                ->update(["selected_device_id" => (int)$list[0]["id"]]);
        }

        return $list;
    }

    private function resolveAllowedDeviceId(int $requestedId): int
    {
        $allowed = array_map(fn($d) => (int)$d["id"], $this->devices);
        return in_array($requestedId, $allowed, true) ? $requestedId : (int)$allowed[0];
    }

    private function assertCanAccessSelectedDevice(): void
    {
        if ($this->canManageDevices) return;

        $allowed = array_map(fn($d) => (int)$d["id"], $this->devices);
        if (!$this->selectedDeviceId || !in_array((int)$this->selectedDeviceId, $allowed, true)) {
            abort(403, "Unauthorized device access");
        }
    }

    private function refreshDeviceStatus(): void
    {
        $ids = array_map(fn($d) => (int)$d["id"], $this->devices);

        $rows = Device::query()
            ->whereIn("id", $ids)
            ->get(["id","status","last_seen"])
            ->keyBy("id");

        $now = now();
        $out = [];

        foreach ($ids as $id) {
            $r = $rows->get($id);
            $status = $r?->status;
            $last = $r?->last_seen;

            $online = false;
            if ($status === 'online') {
                $online = true;
            } elseif ($last) {
                $diffSec = $now->diffInSeconds(Carbon::parse($last), false);
                $online = abs($diffSec) <= 120;
            }

            $out[$id] = [
                "online" => (bool)$online,
                "status" => $status,
                "last"   => $last,
            ];
        }

        $this->deviceStatus = $out;
    }

    // ==========================
    // SETTINGS modal
    // ==========================
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
        // ✅ ketika user ganti device, pastikan allowed lalu fetch
        if ($property === "selectedDeviceId") {
            $this->selectedDeviceId = $this->resolveAllowedDeviceId((int)$this->selectedDeviceId);
            $this->assertCanAccessSelectedDevice();

            // simpan device aktif ke dashSetting
            DashSetting::query()->updateOrCreate(
                ["user_id" => auth()->id()],
                ["selected_device_id" => $this->selectedDeviceId]
            );

            $this->fetchData();
        }

        if ($property === "visibleSensors") {
            $this->visibleSensors = array_values(array_filter(
                $this->visibleSensors,
                fn($k) => array_key_exists($k, $this->metricLabels)
            ));
        }

        if (in_array($property, ["theme","chartMetric","selectedTimeRange","visibleSensors"], true)) {
            if (!$this->selectedDeviceId && !empty($this->devices)) {
                $this->selectedDeviceId = (int)$this->devices[0]["id"];
            }
            if (!$this->selectedDeviceId) return;

            DashSetting::query()->updateOrCreate(
                ["user_id" => auth()->id()],
                [
                    "theme" => $this->theme,
                    "selected_device_id" => $this->selectedDeviceId,
                    "selected_sensor" => $this->chartMetric,
                    "selected_time_range" => $this->selectedTimeRange,
                    "visible_sensors" => json_encode($this->visibleSensors),
                ]
            );

            if ($property === "theme") {
                $this->dispatch('theme-sync', theme: $this->theme);
            }

            $this->fetchData();
        }

        if ($property === "selectedTimeRange") {
            $this->dispatchMainChart();
        }

        if ($property === "modalTimeRange" && $this->modalOpen) {
            $this->dispatchMetricChart();
        }
    }

    // ==========================
    // MAIN realtime fetch
    // ==========================
    public function fetchData(): void
    {
        if (!$this->selectedDeviceId) return;
        $this->assertCanAccessSelectedDevice();

        $this->refreshDeviceStatus();

        $latest = SensorReading::query()
            ->where("device_id", $this->selectedDeviceId)
            ->latest("timestamp")
            ->first();

        if (!$latest) return;

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

        if ($this->modalOpen) $this->dispatchMetricChart();
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
        if (!$this->selectedDeviceId) return;
        $this->assertCanAccessSelectedDevice();

        $metric = array_key_exists($this->chartMetric, $this->metricLabels)
            ? $this->chartMetric
            : "ketinggian_air";

        $base = SensorReading::query()->where("device_id", $this->selectedDeviceId);
        $query = $this->applyTimeRangeUtc(clone $base, $this->selectedTimeRange);
        $take  = $this->takePointsForRange($this->selectedTimeRange);

        $records = $query->latest("timestamp")->take($take)->get(["timestamp", $metric]);

        if ($records->count() === 0) {
            $records = $base->latest("timestamp")->take(300)->get(["timestamp", $metric]);
        }

        $records = $records->reverse()->values();
        $tz = config("app.timezone", "Asia/Jakarta");

        $labels = $records->pluck("timestamp")->map(fn($t) =>
            Carbon::parse($t)->setTimezone($tz)->format("d M H:i")
        )->toArray();

        $values = $records->pluck($metric)->map(fn($v) => (float)($v ?? 0))->toArray();

        $this->dispatch("refreshChart", labels: $labels, values: $values);
    }

    private function calculateRisk(): void
    {
        $water = (float) ($this->data["ketinggian_air"] ?? 0);
        $wind  = (float) ($this->data["kecepatan_angin"] ?? 0);

        $score = ($water * 0.5) + ($wind * 0.3);
        $this->riskScore = round($score, 2);

        if ($score > 200) $this->risk = "BAHAYA";
        elseif ($score > 150) $this->risk = "SIAGA";
        elseif ($score > 100) $this->risk = "WASPADA";
        else $this->risk = "AMAN";
    }

    public function getRiskStylesProperty(): array
    {
        return match ($this->risk) {
            "BAHAYA" => ["bg"=>"bg-red-500/10","border"=>"border-red-500/30","text"=>"text-red-500"],
            "SIAGA"  => ["bg"=>"bg-yellow-500/10","border"=>"border-yellow-500/30","text"=>"text-yellow-600"],
            "WASPADA"=> ["bg"=>"bg-orange-500/10","border"=>"border-orange-500/30","text"=>"text-orange-600"],
            default  => ["bg"=>"bg-emerald-500/10","border"=>"border-emerald-500/30","text"=>"text-emerald-600"],
        };
    }

    private function getWindDirectionLabel($degree): string
    {
        if ($degree === null) return "-";
        $dirs = ["Utara","Timur Laut","Timur","Tenggara","Selatan","Barat Daya","Barat","Barat Laut"];
        $idx = (int) floor(((float)$degree + 22.5) / 45) % 8;
        return $dirs[$idx];
    }

    // ==========================
    // MODAL metric chart
    // ==========================
    public function openMetric(string $metric): void
    {
        if (!array_key_exists($metric, $this->metricLabels)) return;

        $this->selectedMetric = $metric;
        $this->modalOpen = true;
        $this->modalTimeRange = $this->selectedTimeRange ?: "5m";

        $this->dispatchMetricChart();
    }

    public function closeModal(): void
    {
        $this->modalOpen = false;
    }

    public function pollMetric(): void
    {
        if (!$this->modalOpen) return;
        $this->dispatchMetricChart();
    }

    protected function dispatchMetricChart(): void
    {
        if (!$this->selectedDeviceId) return;
        $this->assertCanAccessSelectedDevice();

        $metric = array_key_exists($this->selectedMetric, $this->metricLabels)
            ? $this->selectedMetric
            : "ketinggian_air";

        $base = SensorReading::query()->where("device_id", $this->selectedDeviceId);
        $query = $this->applyTimeRangeUtc(clone $base, $this->modalTimeRange);
        $take  = $this->takePointsForRange($this->modalTimeRange);

        $rows = $query->latest("timestamp")->take($take)->get(["timestamp", $metric]);

        if ($rows->count() === 0) {
            $rows = $base->latest("timestamp")->take(300)->get(["timestamp", $metric]);
        }

        $rows = $rows->reverse()->values();
        $tz = config("app.timezone", "Asia/Jakarta");

        $labels = $rows->map(fn($r) => Carbon::parse($r->timestamp)->setTimezone($tz)->format("d M H:i"))->all();
        $values = $rows->map(fn($r) => (float)($r->{$metric} ?? 0))->all();

        $this->dispatch("modalChart",
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
