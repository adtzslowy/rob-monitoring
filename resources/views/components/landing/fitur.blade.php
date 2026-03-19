<section id="fitur" class="py-20 bg-slate-50 dark:bg-slate-950 border-t border-slate-100 dark:border-slate-800">
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
        <div class="text-center max-w-3xl mx-auto mb-12">
            <span class="inline-flex items-center rounded-full border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 px-3 py-1 text-xs font-semibold text-slate-600 dark:text-slate-400 mb-4">
                Fitur Unggulan
            </span>
            <h2 class="text-3xl sm:text-4xl font-extrabold text-slate-900 dark:text-white">
                Lengkap untuk Pemantauan Pesisir
            </h2>
        </div>

        @php
            $features = [
                ['icon' => 'chart-bar', 'title' => 'Dashboard Real-time', 'desc' => 'Tampilan data sensor langsung dengan grafik tren dan status risiko terkini yang diperbarui setiap detik.', 'color' => 'blue'],
                ['icon' => 'map',       'title' => 'Peta Monitoring',     'desc' => 'Visualisasi lokasi alat sensor di peta interaktif Windy dengan lapisan cuaca dan status online/offline.', 'color' => 'cyan'],
                ['icon' => 'bell',      'title' => 'Alert Digest',        'desc' => 'Notifikasi Telegram otomatis dalam format digest — satu pesan untuk semua device yang berubah status.', 'color' => 'green'],
                ['icon' => 'cpu',       'title' => 'Fuzzy Logic',         'desc' => 'Penentuan status risiko menggunakan logika fuzzy Sugeno dengan input 4 sensor secara bersamaan.', 'color' => 'purple'],
                ['icon' => 'shield',    'title' => 'Multi-level Access',  'desc' => 'Manajemen pengguna dengan role Admin dan Operator. Admin kelola semua device, operator hanya device miliknya.', 'color' => 'orange'],
                ['icon' => 'clock',     'title' => 'Riwayat Data',        'desc' => 'Akses data historis sensor dari 1 menit hingga 1 tahun ke belakang dengan grafik yang bisa di-drill down.', 'color' => 'rose'],
            ];
            $colors = [
                'blue'   => 'bg-blue-100 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400',
                'cyan'   => 'bg-cyan-100 dark:bg-cyan-900/30 text-cyan-600 dark:text-cyan-400',
                'green'  => 'bg-green-100 dark:bg-green-900/30 text-green-600 dark:text-green-400',
                'purple' => 'bg-purple-100 dark:bg-purple-900/30 text-purple-600 dark:text-purple-400',
                'orange' => 'bg-orange-100 dark:bg-orange-900/30 text-orange-600 dark:text-orange-400',
                'rose'   => 'bg-rose-100 dark:bg-rose-900/30 text-rose-600 dark:text-rose-400',
            ];
            $icons = [
                'chart-bar' => '<path stroke-linecap="round" stroke-linejoin="round" d="M3 13.125C3 12.504 3.504 12 4.125 12h2.25c.621 0 1.125.504 1.125 1.125v6.75C7.5 20.496 6.996 21 6.375 21h-2.25A1.125 1.125 0 013 19.875v-6.75zM9.75 8.625c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125v11.25c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V8.625zM16.5 4.125c0-.621.504-1.125 1.125-1.125h2.25C20.496 3 21 3.504 21 4.125v15.75c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V4.125z" />',
                'map'       => '<path stroke-linecap="round" stroke-linejoin="round" d="M9 6.75V15m6-6v8.25m.503 3.498l4.875-2.437c.381-.19.622-.58.622-1.006V4.82c0-.836-.88-1.38-1.628-1.006l-3.869 1.934c-.317.159-.69.159-1.006 0L9.503 3.252a1.125 1.125 0 00-1.006 0L3.622 5.689C3.24 5.88 3 6.27 3 6.695V19.18c0 .836.88 1.38 1.628 1.006l3.869-1.934c.317-.159.69-.159 1.006 0l4.994 2.497c.317.158.69.158 1.006 0z" />',
                'bell'      => '<path stroke-linecap="round" stroke-linejoin="round" d="M14.857 17.082a23.848 23.848 0 005.454-1.31A8.967 8.967 0 0118 9.75v-.7V9A6 6 0 006 9v.75a8.967 8.967 0 01-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 01-5.714 0m5.714 0a3 3 0 11-5.714 0" />',
                'cpu'       => '<path stroke-linecap="round" stroke-linejoin="round" d="M8.25 3v1.5M4.5 8.25H3m18 0h-1.5M4.5 12H3m18 0h-1.5m-15 3.75H3m18 0h-1.5M8.25 19.5V21M12 3v1.5m0 15V21m3.75-18v1.5m0 15V21m-9-1.5h10.5a2.25 2.25 0 002.25-2.25V6.75a2.25 2.25 0 00-2.25-2.25H6.75A2.25 2.25 0 004.5 6.75v10.5a2.25 2.25 0 002.25 2.25zm.75-12h9v9h-9v-9z" />',
                'shield'    => '<path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75m-3-7.036A11.959 11.959 0 013.598 6 11.99 11.99 0 003 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285z" />',
                'clock'     => '<path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z" />',
            ];
        @endphp

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
            @foreach ($features as $f)
                <div class="group rounded-2xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 p-5 hover:-translate-y-1 transition-transform duration-300">
                    <div class="flex h-10 w-10 items-center justify-center rounded-xl {{ $colors[$f['color']] }} mb-3">
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            {!! $icons[$f['icon']] !!}
                        </svg>
                    </div>
                    <h3 class="font-bold text-slate-900 dark:text-white mb-1">{{ $f['title'] }}</h3>
                    <p class="text-sm text-slate-500 dark:text-slate-400 leading-relaxed">{{ $f['desc'] }}</p>
                </div>
            @endforeach
        </div>
    </div>
</section>