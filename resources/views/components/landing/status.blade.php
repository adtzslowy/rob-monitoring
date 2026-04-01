<section id="status" class="py-20 bg-slate-50 dark:bg-slate-950 border-t border-slate-100 dark:border-slate-800">
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
        <div class="text-center max-w-3xl mx-auto mb-12">
            <span
                class="inline-flex items-center rounded-full border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 px-3 py-1 text-xs font-semibold text-slate-600 dark:text-slate-400 mb-4">
                Tingkat Status
            </span>
            <h2 class="text-3xl sm:text-4xl font-extrabold text-slate-900 dark:text-white">4 Tingkat Status Risiko</h2>
            <p class="mt-4 text-slate-600 dark:text-slate-400">Sistem mengklasifikasikan kondisi menjadi 4 tingkat status
                berdasarkan skor defuzzifikasi fuzzy logic.</p>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            {{-- AMAN --}}
            <div
                class="group rounded-2xl border border-emerald-200 dark:border-emerald-800 bg-emerald-50 dark:bg-emerald-900/20 p-6 text-center hover:-translate-y-1 transition-transform duration-300">
                <div class="text-4xl mb-3">🟢</div>
                <h3 class="text-xl font-extrabold text-emerald-700 dark:text-emerald-400 mb-2">AMAN</h3>
                <p class="text-xs text-emerald-600/80 dark:text-emerald-400/70 leading-relaxed">Kondisi normal, tidak
                    ada ancaman banjir rob yang signifikan. Skor fuzzy di bawah 40.</p>
            </div>

            {{-- WASPADA --}}
            <div
                class="group rounded-2xl border border-amber-200 dark:border-amber-800 bg-amber-50 dark:bg-amber-900/20 p-6 text-center hover:-translate-y-1 transition-transform duration-300">
                <div class="text-4xl mb-3">🟡</div>
                <h3 class="text-xl font-extrabold text-amber-700 dark:text-amber-400 mb-2">WASPADA</h3>
                <p class="text-xs text-amber-600/80 dark:text-amber-400/70 leading-relaxed">Kondisi mulai meningkat,
                    perlu perhatian dan pemantauan lebih ketat. Skor 40–64.</p>
            </div>

            {{-- SIAGA --}}
            <div
                class="group rounded-2xl border border-orange-200 dark:border-orange-800 bg-orange-50 dark:bg-orange-900/20 p-6 text-center hover:-translate-y-1 transition-transform duration-300">
                <div class="text-4xl mb-3">🟠</div>
                <h3 class="text-xl font-extrabold text-orange-700 dark:text-orange-400 mb-2">SIAGA</h3>
                <p class="text-xs text-orange-600/80 dark:text-orange-400/70 leading-relaxed">Kondisi berbahaya, bersiap
                    untuk tindakan evakuasi jika diperlukan. Skor 65–84.</p>
            </div>

            {{-- BAHAYA --}}
            <div
                class="group rounded-2xl border border-red-200 dark:border-red-800 bg-red-50 dark:bg-red-900/20 p-6 text-center hover:-translate-y-1 transition-transform duration-300">
                <div class="text-4xl mb-3">🔴</div>
                <h3 class="text-xl font-extrabold text-red-700 dark:text-red-400 mb-2">BAHAYA</h3>
                <p class="text-xs text-red-600/80 dark:text-red-400/70 leading-relaxed">Kondisi kritis, banjir rob
                    diprediksi terjadi. Segera lakukan evakuasi. Skor ≥ 85.</p>
            </div>
        </div>
    </div>
</section>
