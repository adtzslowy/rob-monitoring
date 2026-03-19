<section id="cara-kerja" class="py-20 bg-white dark:bg-slate-900 border-t border-slate-100 dark:border-slate-800">
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
        <div class="text-center max-w-3xl mx-auto mb-12">
            <span class="inline-flex items-center rounded-full border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 px-3 py-1 text-xs font-semibold text-slate-600 dark:text-slate-400 mb-4">
                Cara Kerja
            </span>
            <h2 class="text-3xl sm:text-4xl font-extrabold text-slate-900 dark:text-white">
                Bagaimana Sistem Bekerja?
            </h2>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 relative">
            <div class="hidden lg:block absolute top-8 left-[15%] right-[15%] h-px bg-gradient-to-r from-blue-200 via-purple-300 to-blue-200 dark:from-blue-900 dark:via-purple-800 dark:to-blue-900"></div>

            @foreach ([
                ['no' => '01', 'title' => 'Sensor IoT',   'desc' => 'Alat sensor mengukur ketinggian air, suhu, angin, tekanan, dan kelembapan secara real-time di lapangan.',                      'color' => 'from-blue-500 to-blue-600'],
                ['no' => '02', 'title' => 'Kirim Data',   'desc' => 'Data dikirim ke server melalui API IoT Kabupaten Ketapang setiap beberapa detik sekali.',                                       'color' => 'from-cyan-500 to-cyan-600'],
                ['no' => '03', 'title' => 'Fuzzy Logic',  'desc' => 'Server menganalisis data dengan algoritma fuzzy logic untuk menentukan tingkat risiko secara akurat.',                          'color' => 'from-purple-500 to-purple-600'],
                ['no' => '04', 'title' => 'Alert & Notif','desc' => 'Status ditampilkan di dashboard dan notifikasi dikirim ke Telegram jika kondisi berbahaya.',                                   'color' => 'from-green-500 to-green-600'],
            ] as $step)
                <div class="text-center relative">
                    <div class="flex justify-center mb-4">
                        <div class="relative flex h-16 w-16 items-center justify-center rounded-2xl bg-gradient-to-br {{ $step['color'] }} shadow-lg">
                            <span class="text-2xl font-extrabold text-white">{{ $step['no'] }}</span>
                        </div>
                    </div>
                    <h3 class="font-bold text-slate-900 dark:text-white mb-2">{{ $step['title'] }}</h3>
                    <p class="text-sm text-slate-500 dark:text-slate-400 leading-relaxed">{{ $step['desc'] }}</p>
                </div>
            @endforeach
        </div>
    </div>
</section>