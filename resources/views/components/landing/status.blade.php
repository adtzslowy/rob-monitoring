<section id="status" class="py-20 bg-slate-50 dark:bg-slate-950 border-t border-slate-100 dark:border-slate-800">
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
        <div class="text-center max-w-3xl mx-auto mb-12">
            <span class="inline-flex items-center rounded-full border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 px-3 py-1 text-xs font-semibold text-slate-600 dark:text-slate-400 mb-4">
                Tingkat Status
            </span>
            <h2 class="text-3xl sm:text-4xl font-extrabold text-slate-900 dark:text-white">4 Tingkat Status Risiko</h2>
            <p class="mt-4 text-slate-600 dark:text-slate-400">Sistem mengklasifikasikan kondisi menjadi 4 tingkat status berdasarkan skor defuzzifikasi fuzzy logic.</p>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            @foreach ([
                ['emoji' => '🟢', 'label' => 'AMAN',     'color' => 'emerald', 'desc' => 'Kondisi normal, tidak ada ancaman banjir rob yang signifikan. Skor fuzzy di bawah 40.'],
                ['emoji' => '🟡', 'label' => 'WASPADA',  'color' => 'amber',   'desc' => 'Kondisi mulai meningkat, perlu perhatian dan pemantauan lebih ketat. Skor 40–64.'],
                ['emoji' => '🟠', 'label' => 'SIAGA',    'color' => 'orange',  'desc' => 'Kondisi berbahaya, bersiap untuk tindakan evakuasi jika diperlukan. Skor 65–84.'],
                ['emoji' => '🔴', 'label' => 'BAHAYA',   'color' => 'red',     'desc' => 'Kondisi kritis, banjir rob diprediksi terjadi. Segera lakukan evakuasi. Skor ≥ 85.'],
            ] as $s)
                <div class="group rounded-2xl border border-{{ $s['color'] }}-200 dark:border-{{ $s['color'] }}-800 bg-{{ $s['color'] }}-50 dark:bg-{{ $s['color'] }}-900/20 p-6 text-center hover:-translate-y-1 transition-transform duration-300">
                    <div class="text-4xl mb-3">{{ $s['emoji'] }}</div>
                    <h3 class="text-xl font-extrabold text-{{ $s['color'] }}-700 dark:text-{{ $s['color'] }}-400 mb-2">{{ $s['label'] }}</h3>
                    <p class="text-xs text-{{ $s['color'] }}-600/80 dark:text-{{ $s['color'] }}-400/70 leading-relaxed">{{ $s['desc'] }}</p>
                </div>
            @endforeach
        </div>
    </div>
</section>