<section class="py-20 bg-white dark:bg-slate-900 border-t border-slate-100 dark:border-slate-800">
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
        <div class="relative rounded-3xl overflow-hidden bg-gradient-to-br from-blue-600 via-blue-700 to-cyan-600 p-8 sm:p-12 lg:p-16 text-center">
            <div class="absolute inset-0 opacity-10"
                style="background-image: linear-gradient(white 1px, transparent 1px), linear-gradient(to right, white 1px, transparent 1px); background-size: 32px 32px;"></div>
            <div class="absolute top-0 right-0 w-64 h-64 bg-white/5 rounded-full -translate-y-1/2 translate-x-1/2"></div>
            <div class="absolute bottom-0 left-0 w-48 h-48 bg-black/5 rounded-full translate-y-1/2 -translate-x-1/2"></div>
            <div class="relative">
                <h2 class="text-3xl sm:text-4xl font-extrabold text-white mb-4">Siap Memantau Kondisi Pesisir?</h2>
                <p class="text-blue-100 mb-8 max-w-2xl mx-auto text-lg">
                    Masuk ke dashboard untuk melihat data sensor real-time dan status risiko banjir rob terkini di wilayah Ketapang.
                </p>
                <div class="flex flex-col sm:flex-row gap-3 justify-center">
                    <a href="{{ route('login') }}"
                        class="inline-flex items-center justify-center gap-2 rounded-2xl bg-white hover:bg-blue-50 px-8 py-4 text-sm font-bold text-blue-600 shadow-lg transition">
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0013.5 3h-6a2.25 2.25 0 00-2.25 2.25v13.5A2.25 2.25 0 007.5 21h6a2.25 2.25 0 002.25-2.25V15M12 9l-3 3m0 0l3 3m-3-3h12.75" />
                        </svg>
                        Masuk ke Dashboard
                    </a>
                    <a href="{{ route('peta') }}"
                        class="inline-flex items-center justify-center gap-2 rounded-2xl border border-white/30 bg-white/10 hover:bg-white/20 px-8 py-4 text-sm font-bold text-white transition backdrop-blur">
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 6.75V15m6-6v8.25m.503 3.498l4.875-2.437c.381-.19.622-.58.622-1.006V4.82c0-.836-.88-1.38-1.628-1.006l-3.869 1.934c-.317.159-.69.159-1.006 0L9.503 3.252a1.125 1.125 0 00-1.006 0L3.622 5.689C3.24 5.88 3 6.27 3 6.695V19.18c0 .836.88 1.38 1.628 1.006l3.869-1.934c.317-.159.69-.159 1.006 0l4.994 2.497c.317.158.69.158 1.006 0z" />
                        </svg>
                        Lihat Peta
                    </a>
                </div>
            </div>
        </div>
    </div>
</section>