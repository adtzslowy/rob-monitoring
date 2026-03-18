<section id="tentang" class="py-20 bg-white dark:bg-slate-900 border-t border-slate-100 dark:border-slate-800">
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
        <div class="text-center max-w-3xl mx-auto mb-12">
            <span class="inline-flex items-center rounded-full border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 px-3 py-1 text-xs font-semibold text-slate-600 dark:text-slate-400 mb-4">
                Tentang Sistem
            </span>
            <h2 class="text-3xl sm:text-4xl font-extrabold text-slate-900 dark:text-white">Apa itu ROB Monitoring?</h2>
            <p class="mt-4 text-slate-600 dark:text-slate-400 leading-relaxed">
                ROB Monitoring adalah sistem peringatan dini banjir rob berbasis IoT yang memantau kondisi air laut dan cuaca secara real-time di wilayah pesisir Kabupaten Ketapang, Kalimantan Barat. Sistem ini menggunakan algoritma fuzzy logic untuk menganalisis data multi-sensor secara cerdas.
            </p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            @foreach ([
                ['bg' => 'blue',   'title' => 'Pemantauan Real-time', 'desc' => 'Data sensor dikirim setiap detik dan ditampilkan langsung di dashboard dengan grafik tren yang akurat dan terus diperbarui.',
                 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" d="M3.75 3v11.25A2.25 2.25 0 006 16.5h2.25M3.75 3h-1.5m1.5 0h16.5m0 0h1.5m-1.5 0v11.25A2.25 2.25 0 0118 16.5h-2.25m-7.5 0h7.5m-7.5 0l-1 3m8.5-3l1 3m0 0l.5 1.5m-.5-1.5h-9.5m0 0l-.5 1.5M9 11.25v1.5M12 9v3.75m3-6v6" />'],
                ['bg' => 'purple', 'title' => 'Fuzzy Logic AI', 'desc' => 'Menggunakan metode fuzzy logic untuk menentukan tingkat risiko banjir rob berdasarkan kombinasi data multi-sensor secara cerdas.',
                 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" d="M9.813 15.904L9 18.75l-.813-2.846a4.5 4.5 0 00-3.09-3.09L2.25 12l2.846-.813a4.5 4.5 0 003.09-3.09L9 5.25l.813 2.846a4.5 4.5 0 003.09 3.09L15.75 12l-2.846.813a4.5 4.5 0 00-3.09 3.09z" />'],
                ['bg' => 'green',  'title' => 'Notifikasi Telegram', 'desc' => 'Peringatan otomatis dikirim ke Telegram saat status berubah ke WASPADA, SIAGA, atau BAHAYA — meski tidak sedang membuka aplikasi.',
                 'icon' => '<path d="M11.944 0A12 12 0 0 0 0 12a12 12 0 0 0 12 12 12 12 0 0 0 12-12A12 12 0 0 0 12 0a12 12 0 0 0-.056 0zm4.962 7.224c.1-.002.321.023.465.14a.506.506 0 0 1 .171.325c.016.093.036.306.02.472-.18 1.898-.962 6.502-1.36 8.627-.168.9-.499 1.201-.82 1.23-.696.065-1.225-.46-1.9-.902-1.056-.693-1.653-1.124-2.678-1.8-1.185-.78-.417-1.21.258-1.91.177-.184 3.247-2.977 3.307-3.23.007-.032.014-.15-.056-.212s-.174-.041-.249-.024c-.106.024-1.793 1.14-5.061 3.345-.48.33-.913.49-1.302.48-.428-.008-1.252-.241-1.865-.44-.752-.245-1.349-.374-1.297-.789.027-.216.325-.437.893-.663 3.498-1.524 5.83-2.529 6.998-3.014 3.332-1.386 4.025-1.627 4.476-1.635z"/>',
                 'fill' => true],
            ] as $card)
                <div class="group rounded-3xl border border-slate-100 dark:border-slate-800 bg-slate-50 dark:bg-slate-800/50 p-6 hover:-translate-y-1 transition-transform duration-300">
                    <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-{{ $card['bg'] }}-100 dark:bg-{{ $card['bg'] }}-900/40 mb-4 transition">
                        <svg class="h-6 w-6 text-{{ $card['bg'] }}-600 dark:text-{{ $card['bg'] }}-400" fill="{{ $card['fill'] ?? 'none' }}" viewBox="0 0 24 24" stroke-width="{{ isset($card['fill']) ? '' : '1.5' }}" stroke="{{ isset($card['fill']) ? '' : 'currentColor' }}">
                            {!! $card['icon'] !!}
                        </svg>
                    </div>
                    <h3 class="text-lg font-bold text-slate-900 dark:text-white mb-2">{{ $card['title'] }}</h3>
                    <p class="text-sm text-slate-600 dark:text-slate-400 leading-relaxed">{{ $card['desc'] }}</p>
                </div>
            @endforeach
        </div>
    </div>
</section>