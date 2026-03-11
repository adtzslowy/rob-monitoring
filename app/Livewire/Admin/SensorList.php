<?php

namespace App\Livewire\Admin;

use App\Models\Device;
use App\Models\SensorReading;
use Carbon\Carbon;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Title('Manajemen Sensor')]
class SensorList extends Component
{
    use WithPagination;

    public bool $canManageDevices = false;

    public string $search = '';
    public int $perPage = 10;

    public bool $modalOpen = false;
    public ?int $detailDeviceId = null;

    public string $detailRange = '1h';
    public int $detailPerPage = 10;

    /** @var array<int> */
    public array $allowedIds = [];

    /** @var array<int, array{online:bool,last:?string,status:?string}> */
    public array $deviceStatus = [];

    public ?Device $detailDeviceData = null;
    public ?SensorReading $detailReadingData = null;

    public array $sensorMeta = [
        'suhu' => [
            'label' => 'Temperature',
            'unit' => '°C',
        ],
        'kelembapan' => [
            'label' => 'Kelembapan',
            'unit' => '%',
        ],
        'tekanan_udara' => [
            'label' => 'Tekanan Udara',
            'unit' => 'hPa',
        ],
        'kecepatan_angin' => [
            'label' => 'Kecepatan Angin',
            'unit' => 'm/s',
        ],
        'arah_angin' => [
            'label' => 'Arah Angin',
            'unit' => '°',
        ],
        'ketinggian_air' => [
            'label' => 'Ketinggian Air',
            'unit' => 'cm',
        ],
    ];

    public function mount(): void
    {
        $user = auth()->user();

        $this->canManageDevices = $user?->can('manage devices') ?? false;
        $this->allowedIds = $this->resolveAllowedDeviceIds();
        $this->refreshDeviceStatus();
    }

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function updatedPerPage(): void
    {
        $this->resetPage();
    }

    public function updatedDetailRange(): void
    {
        $this->resetPage('detailPage');
    }

    public function updatedDetailPerPage(): void
    {
        $this->resetPage('detailPage');
    }

    private function resolveAllowedDeviceIds(): array
    {
        $user = auth()->user();

        if ($this->canManageDevices) {
            return Device::query()
                ->orderBy('id')
                ->pluck('id')
                ->map(fn ($id) => (int) $id)
                ->toArray();
        }

        return $user?->devices()
            ->orderBy('devices.id')
            ->pluck('devices.id')
            ->map(fn ($id) => (int) $id)
            ->toArray() ?? [];
    }

    private function refreshDeviceStatus(): void
    {
        if (empty($this->allowedIds)) {
            $this->deviceStatus = [];
            return;
        }

        $rows = Device::query()
            ->whereIn('id', $this->allowedIds)
            ->get(['id', 'status', 'last_seen'])
            ->keyBy('id');

        $now = now();
        $out = [];

        foreach ($this->allowedIds as $id) {
            $row = $rows->get($id);
            $status = $row?->status;
            $last = $row?->last_seen;

            $online = false;

            if ($status === 'online') {
                $online = true;
            } elseif ($last) {
                $diffSec = $now->diffInSeconds(Carbon::parse($last), false);
                $online = abs($diffSec) <= 120;
            }

            $out[$id] = [
                'online' => (bool) $online,
                'status' => $status,
                'last' => $last,
            ];
        }

        $this->deviceStatus = $out;
    }

    public function fetchData(): void
    {
        $this->refreshDeviceStatus();

        if ($this->modalOpen && $this->detailDeviceId) {
            $this->loadDetailData($this->detailDeviceId);
        }
    }

    public function openDetail(int $deviceId): void
    {
        if (!in_array($deviceId, $this->allowedIds, true)) {
            abort(403, 'Unauthorized device access');
        }

        $this->detailDeviceId = $deviceId;
        $this->detailRange = '1h';
        $this->detailPerPage = 10;
        $this->resetPage('detailPage');

        $this->loadDetailData($deviceId);
        $this->modalOpen = true;
    }

    public function closeModal(): void
    {
        $this->modalOpen = false;
        $this->detailDeviceId = null;
        $this->detailDeviceData = null;
        $this->detailReadingData = null;
        $this->resetPage('detailPage');
    }

    private function loadDetailData(int $deviceId): void
    {
        $this->detailDeviceData = Device::query()->find($deviceId);

        $this->detailReadingData = SensorReading::query()
            ->where('device_id', $deviceId)
            ->latest('timestamp')
            ->first();
    }

    private function getDetailRangeStart(): Carbon
    {
        return match ($this->detailRange) {
            '1m' => now()->subMinute(),
            '1h' => now()->subHour(),
            '1d' => now()->subDay(),
            '1w' => now()->subWeek(),
            '1mo' => now()->subMonth(),
            '1y' => now()->subYear(),
            default => now()->subHour(),
        };
    }

    public function getDetailRangeLabelProperty(): string
    {
        return match ($this->detailRange) {
            '1m' => '1 Menit',
            '1h' => '1 Jam',
            '1d' => '1 Hari',
            '1w' => '1 Minggu',
            '1mo' => '1 Bulan',
            '1y' => '1 Tahun',
            default => '1 Jam',
        };
    }

    public function getDetailDeviceProperty(): ?Device
    {
        return $this->detailDeviceData;
    }

    public function getDetailReadingProperty(): ?SensorReading
    {
        return $this->detailReadingData;
    }

    public function getDetailLastUpdateTextProperty(): string
    {
        $reading = $this->detailReadingData;

        if (!$reading?->timestamp) {
            return '-';
        }

        return Carbon::parse($reading->timestamp, 'UTC')
            ->setTimezone('Asia/Jakarta')
            ->format('d M Y H:i');
    }

    public function getDetailHistoryProperty(): LengthAwarePaginator
    {
        if (!$this->detailDeviceId) {
            return SensorReading::query()
                ->whereRaw('1 = 0')
                ->paginate(
                    perPage: $this->detailPerPage,
                    pageName: 'detailPage'
                );
        }

        $start = $this->getDetailRangeStart();

        $rows = SensorReading::query()
            ->where('device_id', $this->detailDeviceId)
            ->where('timestamp', '>=', $start)
            ->orderByDesc('timestamp')
            ->paginate(
                perPage: $this->detailPerPage,
                columns: [
                    'timestamp',
                    'suhu',
                    'kelembapan',
                    'tekanan_udara',
                    'kecepatan_angin',
                    'arah_angin',
                    'ketinggian_air',
                ],
                pageName: 'detailPage'
            );

        $rows->through(function ($row) {
            return [
                'timestamp' => $row->timestamp
                    ? Carbon::parse($row->timestamp, 'UTC')
                        ->setTimezone('Asia/Jakarta')
                        ->format('d M Y H:i:s')
                    : '-',
                'suhu' => $row->suhu,
                'kelembapan' => $row->kelembapan,
                'tekanan_udara' => $row->tekanan_udara,
                'kecepatan_angin' => $row->kecepatan_angin,
                'arah_angin' => $row->arah_angin,
                'arah_angin_label' => $this->getWindDirectionLabel($row->arah_angin),
                'ketinggian_air' => $row->ketinggian_air,
            ];
        });

        return $rows;
    }

    public function getWindDirectionLabel($degree): string
    {
        if ($degree === null) {
            return '-';
        }

        $dirs = ['Utara', 'Timur Laut', 'Timur', 'Tenggara', 'Selatan', 'Barat Daya', 'Barat', 'Barat Laut'];
        $idx = (int) floor((((float) $degree) + 22.5) / 45) % 8;

        return $dirs[$idx];
    }

    private function getLatestReadingsForDeviceIds(array $deviceIds): Collection
    {
        if (empty($deviceIds)) {
            return collect();
        }

        $latestPerDevice = SensorReading::query()
            ->selectRaw('device_id, MAX(timestamp) as max_timestamp')
            ->whereIn('device_id', $deviceIds)
            ->groupBy('device_id');

        return SensorReading::query()
            ->joinSub($latestPerDevice, 'latest', function ($join) {
                $join->on('sensor_readings.device_id', '=', 'latest.device_id')
                    ->on('sensor_readings.timestamp', '=', 'latest.max_timestamp');
            })
            ->select('sensor_readings.*')
            ->get()
            ->keyBy('device_id');
    }

    public function render()
    {
        if (empty($this->allowedIds)) {
            return view('livewire.admin.sensor-list', [
                'devices' => Device::query()->whereRaw('1 = 0')->paginate($this->perPage),
            ]);
        }

        $devicesQuery = Device::query()
            ->whereIn('id', $this->allowedIds)
            ->when($this->search, function ($q) {
                $search = trim($this->search);

                $q->where(function ($qq) use ($search) {
                    $qq->where('name', 'like', '%' . $search . '%')
                        ->orWhere('alias', 'like', '%' . $search . '%')
                        ->orWhere('id', 'like', '%' . $search . '%');
                });
            })
            ->orderBy('id');

        $devices = $devicesQuery->paginate($this->perPage);

        $deviceIds = $devices->getCollection()
            ->pluck('id')
            ->map(fn ($id) => (int) $id)
            ->toArray();

        $latestReadings = $this->getLatestReadingsForDeviceIds($deviceIds);

        $rows = $devices->getCollection()->map(function ($device) use ($latestReadings) {
            $reading = $latestReadings->get($device->id);

            $status = $this->deviceStatus[$device->id] ?? [
                'online' => false,
                'status' => null,
                'last' => null,
            ];

            return [
                'id' => (int) $device->id,
                'name' => $device->name ?? ('ROB ' . $device->id),
                'alias' => $device->alias ?? null,
                'label' => $device->alias ?: ($device->name ?? ('ROB ' . $device->id)),
                'online' => (bool) ($status['online'] ?? false),
                'status' => $status['status'] ?? null,
                'last_seen' => $status['last'] ?? null,

                'suhu' => $reading?->suhu,
                'kelembapan' => $reading?->kelembapan,
                'tekanan_udara' => $reading?->tekanan_udara,
                'kecepatan_angin' => $reading?->kecepatan_angin,
                'arah_angin' => $reading?->arah_angin,
                'arah_angin_label' => $this->getWindDirectionLabel($reading?->arah_angin),
                'ketinggian_air' => $reading?->ketinggian_air,
                'timestamp' => $reading?->timestamp,
            ];
        });

        $devices->setCollection($rows);

        return view('livewire.admin.sensor-list', [
            'devices' => $devices,
        ]);
    }
}