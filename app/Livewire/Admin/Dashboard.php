<?php

namespace App\Livewire\Admin;

use App\Models\Device;
use App\Models\SensorReading;
use App\Models\DashSetting;
use Illuminate\Support\Carbon;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Title("Dashboard")]
class Dashboard extends Component
{
    // ===== Dashboard state =====
    public array $data = [];
    public string $risk = "AMAN";
    public float|int $riskScore = 1;

    // ===== RBAC & Device list =====
    public bool $canManageDevices = false;

    /** @var array<int, array{id:int,name:string}> */
    public array $devices = [];

    // ===== Settings =====
    public bool $showSettings = false;
    public string $theme = "dark";
    public ?int $selectedDeviceId = null;
    public string $selectedSensor = "ketinggian_air";
    public string $selectedTimeRange = "30m";

    // ✅ visible sensors (buat card yang muncul)
    public array $visibleSensors = [];

    // ===== Time ranges =====
    public array $timeRanges = [
        "1m" => "1 Menit",
        "30m" => "30 Menit",
        "1h" => "1 Jam",
        "12h" => "12 Jam",
        "24h" => "24 Jam",
        "1w" => "1 Minggu",
        "1mo" => "1 Bulan",
        "1y" => "1 Tahun",
    ];

    // ===== Modal metric chart =====
    public bool $modalOpen = false;
    public string $selectedMetric = "ketinggian_air";
    public string $modalTimeRange = "30m";
    public int $limit = 300;

    public array $metricLabels = [
        "suhu" => "Temperature (°C)",
        "kelembapan" => "Kelembapan (%)",
        "tekanan_udara" => "Tekanan Udara (hPa)",
        "kecepatan_angin" => "Kecepatan Angin (m/s)",
        "arah_angin" => "Arah Angin (°)",
        "ketinggian_air" => "Ketinggian Air (cm)",
    ];

    protected $listeners = [
        "open-dashboard-settings" => "openSettings",
    ];

    public function mount(): void
    {
        $user = auth()->user();

        // kamu pakai permission "manage devices" utk admin
        $this->canManageDevices = $user?->can("manage devices") ?? false;

        // ambil setting user dulu (ini penting!)
        $setting = DashSetting::query()
            ->where("user_id", auth()->id())
            ->first();

        // kalau belum ada record setting, buat
        if (!$setting) {
            $firstDevice = Device::query()
                ->orderBy("id")
                ->first(["id", "name"]);
            $setting = DashSetting::query()->create([
                "user_id" => auth()->id(),
                "theme" => "dark",
                "selected_device_id" => $firstDevice?->id,
                "selected_sensor" => "ketinggian_air",
                "selected_time_range" => "30m",
                "visible_sensors" => json_encode(
                    array_keys($this->metricLabels),
                ),
            ]);
        }

        // ===== load devices (admin vs operator) =====
        $this->devices = $this->loadAllowedDevicesBySetting($setting);

        if (empty($this->devices)) {
            // gak ada device sama sekali
            return;
        }

        // ===== set state dari DB =====
        $this->theme = $setting->theme ?? "dark";

        // enforce device utk operator
        $requestedDeviceId =
            (int) ($setting->selected_device_id ?? $this->devices[0]["id"]);
        $this->selectedDeviceId = $this->resolveAllowedDeviceId(
            $requestedDeviceId,
        );

        $this->selectedSensor = $setting->selected_sensor ?? "ketinggian_air";
        $this->selectedTimeRange = $setting->selected_time_range ?? "30m";

        // visible_sensors: kalau kosong, default semua metric
        $vs = $setting->visible_sensors;
        if (is_string($vs)) {
            $decoded = json_decode($vs, true);
            $this->visibleSensors = is_array($decoded)
                ? $decoded
                : array_keys($this->metricLabels);
        } elseif (is_array($vs)) {
            $this->visibleSensors = $vs;
        } else {
            $this->visibleSensors = array_keys($this->metricLabels);
        }

        $this->modalTimeRange = $this->selectedTimeRange;

        // fetch awal
        $this->fetchData();
    }

    /**
     * ✅ Allowed devices:
     * - Admin: semua device
     * - Operator: hanya device yang ada di dashboard_setting.selected_device_id
     */
    private function loadAllowedDevicesBySetting(DashSetting $setting): array
    {
        if ($this->canManageDevices) {
            return Device::query()
                ->orderBy("id")
                ->get(["id", "name"])
                ->map(
                    fn($d) => [
                        "id" => (int) $d->id,
                        "name" => $d->name ?? "ROB " . $d->id,
                    ],
                )
                ->toArray();
        }

        // operator: harusnya terkunci dari setting
        $did = (int) ($setting->selected_device_id ?? 0);

        if ($did > 0) {
            $d = Device::query()
                ->where("id", $did)
                ->first(["id", "name"]);
            if ($d) {
                return [
                    [
                        "id" => (int) $d->id,
                        "name" => $d->name ?? "ROB " . $d->id,
                    ],
                ];
            }
        }

        // fallback terakhir kalau setting kosong/invalid
        $first = Device::query()
            ->orderBy("id")
            ->first(["id", "name"]);
        if ($first) {
            // sekalian update setting biar konsisten
            DashSetting::query()
                ->where("user_id", auth()->id())
                ->update([
                    "selected_device_id" => (int) $first->id,
                ]);
            return [
                [
                    "id" => (int) $first->id,
                    "name" => $first->name ?? "ROB " . $first->id,
                ],
            ];
        }

        return [];
    }

    private function resolveAllowedDeviceId(int $requestedId): int
    {
        $allowedIds = array_map(fn($d) => (int) $d["id"], $this->devices);
        if (in_array($requestedId, $allowedIds, true)) {
            return $requestedId;
        }
        return (int) $allowedIds[0];
    }

    private function assertCanAccessSelectedDevice(): void
    {
        if ($this->canManageDevices) {
            return;
        }

        $allowedIds = array_map(fn($d) => (int) $d["id"], $this->devices);
        if (
            !$this->selectedDeviceId ||
            !in_array((int) $this->selectedDeviceId, $allowedIds, true)
        ) {
            abort(403, "Unauthorized device access");
        }
    }

    // ==========================
    // MAIN DASHBOARD (Realtime)
    // ==========================
    public function fetchData(): void
    {
        if (!$this->selectedDeviceId) {
            return;
        }
        $this->assertCanAccessSelectedDevice();

        $latest = SensorReading::query()
            ->where("device_id", $this->selectedDeviceId)
            ->latest("timestamp")
            ->first();

        if (!$latest) {
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
            "arah_angin_label" => $this->getWindDirectionLabel(
                $latest->arah_angin,
            ),
        ];

        $this->calculateRisk();
        $this->dispatchMainChart();

        $this->dispatch(
            "dashboard-updated",
            data: $this->data,
            risk: $this->risk,
            riskScore: $this->riskScore,
            riskStyles: $this->riskStyles,
        );

        if ($this->modalOpen) {
            $this->dispatchMetricChart();
        }
    }

    // ==========================
    // SETTINGS
    // ==========================
    public function openSettings(): void
    {
        $this->showSettings = true;
    }

    public function updated($property): void
    {
        if ($property === "selectedDeviceId") {
            $this->selectedDeviceId = $this->resolveAllowedDeviceId(
                (int) $this->selectedDeviceId,
            );
            $this->assertCanAccessSelectedDevice();
        }

        if (
            in_array(
                $property,
                [
                    "theme",
                    "selectedDeviceId",
                    "selectedSensor",
                    "selectedTimeRange",
                    "visibleSensors",
                ],
                true,
            )
        ) {
            if (!$this->selectedDeviceId) {
                return;
            }

            DashSetting::query()->updateOrCreate(
                ["user_id" => auth()->id()],
                [
                    "theme" => $this->theme,
                    "selected_device_id" => $this->selectedDeviceId,
                    "selected_sensor" => $this->selectedSensor,
                    "selected_time_range" => $this->selectedTimeRange,
                    "visible_sensors" => json_encode(
                        array_values($this->visibleSensors),
                    ),
                ],
            );

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
    // TIME RANGE HELPERS
    // ==========================
    private function rangeStartUtc(string $range): ?Carbon
    {
        $now = now("UTC");

        return match ($range) {
            "1m" => $now->copy()->subMinute(),
            "30m" => $now->copy()->subMinutes(30),
            "1h" => $now->copy()->subHour(),
            "12h" => $now->copy()->subHours(12),
            "24h" => $now->copy()->subDay(),
            "1w" => $now->copy()->subWeek(),
            "1mo" => $now->copy()->subMonth(),
            "1y" => $now->copy()->subYear(),
            default => null,
        };
    }

    private function applyTimeRangeUtc($query, string $range)
    {
        $start = $this->rangeStartUtc($range);
        if (!$start) {
            return $query;
        }

        return $query->where("timestamp", ">=", $start);
    }

    private function takePointsForRange(string $range): int
    {
        return match ($range) {
            "1m" => 120,
            "30m" => 300,
            "1h" => 600,
            "12h" => 1200,
            "24h" => 1800,
            "1w" => 2500,
            "1mo" => 4000,
            "1y" => 6000,
            default => 300,
        };
    }

    // ==========================
    // MAIN CHART
    // ==========================
    private function dispatchMainChart(): void
    {
        if (!$this->selectedDeviceId) {
            return;
        }
        $this->assertCanAccessSelectedDevice();

        $base = SensorReading::query()->where(
            "device_id",
            $this->selectedDeviceId,
        );

        $query = $this->applyTimeRangeUtc(
            clone $base,
            $this->selectedTimeRange,
        );
        $take = $this->takePointsForRange($this->selectedTimeRange);

        $records = $query->latest("timestamp")->take($take)->get();

        if ($records->count() === 0) {
            $records = $base->latest("timestamp")->take(300)->get();
        }

        $records = $records->reverse()->values();

        $tzDisplay = config("app.timezone", "Asia/Jakarta");

        $labels = $records
            ->pluck("timestamp")
            ->map(
                fn($t) => Carbon::parse($t)
                    ->setTimezone($tzDisplay)
                    ->format("d M H:i"),
            )
            ->toArray();

        $metric = $this->selectedSensor ?: "ketinggian_air";

        $values = $records
            ->pluck($metric)
            ->map(fn($v) => (float) ($v ?? 0))
            ->toArray();

        $this->dispatch("refreshChart", labels: $labels, values: $values);
    }

    // ==========================
    // RISK
    // ==========================
    private function calculateRisk(): void
    {
        $water = (float) ($this->data["ketinggian_air"] ?? 0);
        $wind = (float) ($this->data["kecepatan_angin"] ?? 0);

        $score = $water * 0.5 + $wind * 0.3;
        $this->riskScore = round($score, 2);

        if ($score > 200) {
            $this->risk = "BAHAYA";
        } elseif ($score > 150) {
            $this->risk = "SIAGA";
        } elseif ($score > 100) {
            $this->risk = "WASPADA";
        } else {
            $this->risk = "AMAN";
        }
    }

    public function getRiskStylesProperty(): array
    {
        return match ($this->risk) {
            "BAHAYA" => [
                "bg" => "bg-red-500/10",
                "border" => "border-red-500/30",
                "text" => "text-red-400",
            ],
            "SIAGA" => [
                "bg" => "bg-yellow-500/10",
                "border" => "border-yellow-500/30",
                "text" => "text-yellow-400",
            ],
            "WASPADA" => [
                "bg" => "bg-orange-500/10",
                "border" => "border-orange-500/30",
                "text" => "text-orange-400",
            ],
            default => [
                "bg" => "bg-emerald-500/10",
                "border" => "border-emerald-500/30",
                "text" => "text-emerald-400",
            ],
        };
    }

    private function getWindDirectionLabel($degree): string
    {
        if ($degree === null) {
            return "-";
        }

        $directions = [
            "Utara",
            "Timur Laut",
            "Timur",
            "Tenggara",
            "Selatan",
            "Barat Daya",
            "Barat",
            "Barat Laut",
        ];
        $index = (int) floor(((float) $degree + 22.5) / 45) % 8;

        return $directions[$index];
    }

    // ==========================
    // MODAL METRIC CHART
    // ==========================
    public function openMetric(string $metric): void
    {
        if (!array_key_exists($metric, $this->metricLabels)) {
            return;
        }

        $this->selectedMetric = $metric;
        $this->modalOpen = true;

        if (!$this->modalTimeRange) {
            $this->modalTimeRange = $this->selectedTimeRange;
        }

        $this->dispatchMetricChart();
    }

    public function closeModal(): void
    {
        $this->modalOpen = false;
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

        $base = SensorReading::query()->where(
            "device_id",
            $this->selectedDeviceId,
        );

        $query = $this->applyTimeRangeUtc(clone $base, $this->modalTimeRange);
        $take = $this->takePointsForRange($this->modalTimeRange);

        $rows = $query
            ->latest("timestamp")
            ->take($take)
            ->get(["timestamp", $this->selectedMetric]);

        if ($rows->count() === 0) {
            $rows = $base
                ->latest("timestamp")
                ->take(300)
                ->get(["timestamp", $this->selectedMetric]);
        }

        $rows = $rows->reverse()->values();

        $tzDisplay = config("app.timezone", "Asia/Jakarta");

        $labels = $rows
            ->map(
                fn($r) => Carbon::parse($r->timestamp)
                    ->setTimezone($tzDisplay)
                    ->format("d M H:i"),
            )
            ->all();

        $values = $rows
            ->map(fn($r) => (float) ($r->{$this->selectedMetric} ?? 0))
            ->all();

        $this->dispatch(
            "modalChart",
            title: $this->metricLabels[$this->selectedMetric] ??
                $this->selectedMetric,
            labels: $labels,
            values: $values,
        );
    }

    public function render()
    {
        return view("livewire.admin.dashboard");
    }
}
