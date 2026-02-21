<div class="space-y-6 max-w-[1400px] mx-auto">

    <div>
        <h1 class="text-xl font-semibold text-zinc-900 dark:text-white">
            Peta Monitoring
        </h1>
        <p class="text-sm dark:text-white text-zinc-900">
            Klik pada peta untuk menambahkan lokasi sensor
        </p>
    </div>

    {{-- MAP --}}
    <div class="relative w-full h-[500px] rounded-xl overflow-hidden">
        <div wire:ignore id="map" class="absolute inset-0"></div>
    </div>
</div>


@push('scripts')
<script>
document.addEventListener('livewire:navigated', initMap);
document.addEventListener('DOMContentLoaded', initMap);

function initMap() {

    const mapEl = document.getElementById('map');
    if (!mapEl) return;

    if (window.mapInstance) {
        setTimeout(() => {
            window.mapInstance.invalidateSize();
        }, 500);
        return;
    }

    setTimeout(() => {

        const map = L.map('map', {
            zoomControl: true
        }).setView([-6.2, 106.8], 11);

        window.mapInstance = map;

        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '© OpenStreetMap contributors'
        }).addTo(map);

        map.invalidateSize();

        let markers = [];

        map.on('click', function (e) {
            Livewire.dispatch('setCoordinates', e.latlng.lat, e.latlng.lng);
        });

        Livewire.on('renderMarkers', (data) => {

            markers.forEach(marker => map.removeLayer(marker));
            markers = [];

            data.locations.forEach(loc => {
                const marker = L.marker([loc.latitude, loc.longitude])
                    .addTo(map)
                    .bindPopup(`<b>${loc.name}</b>`);

                markers.push(marker);
            });

            setTimeout(() => {
                map.invalidateSize();
            }, 200);

        });

    }, 300);
}
</script>
@endpush
