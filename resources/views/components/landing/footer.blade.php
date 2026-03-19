<footer class="bg-slate-900 dark:bg-slate-950 border-t border-slate-800 py-12">
    <div class="mx-auto max-w-2xl px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8 mb-8">
            <div>
                <div class="flex items-center gap-3 mb-4">
                    <div class="flex h-9 w-9 items-center justify-center rounded-xl bg-blue-600">
                        <svg class="h-5 w-5 text-white" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M14.857 17.082a23.848 23.848 0 005.454-1.31A8.967 8.967 0 0118 9.75v-.7V9A6 6 0 006 9v.75a8.967 8.967 0 01-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 01-5.714 0m5.714 0a3 3 0 11-5.714 0" />
                        </svg>
                    </div>
                    <div>
                        <p class="text-sm font-bold text-white leading-none">ROB Monitoring</p>
                        <p class="text-xs text-slate-400">Early Warning System</p>
                    </div>
                </div>
                <p class="text-sm text-slate-400 leading-relaxed">Sistem peringatan dini banjir rob berbasis IoT untuk wilayah pesisir Ketapang, Kalimantan Barat.</p>
            </div>

            <div>
                <h4 class="text-sm font-bold text-white mb-4">Navigasi</h4>
                <ul class="space-y-2">
                    <li><a href="#tentang"   class="text-sm text-slate-400 hover:text-white transition">Tentang Sistem</a></li>
                    <li><a href="#fitur"     class="text-sm text-slate-400 hover:text-white transition">Fitur Unggulan</a></li>
                    <li><a href="#cara-kerja"class="text-sm text-slate-400 hover:text-white transition">Cara Kerja</a></li>
                    <li><a href="#status"    class="text-sm text-slate-400 hover:text-white transition">Tingkat Status</a></li>
                    <li><a href="{{ route('login') }}" class="text-sm text-slate-400 hover:text-white transition">Masuk Dashboard</a></li>
                </ul>
            </div>

            <div>
                <h4 class="text-sm font-bold text-white mb-4">Wilayah Pemantauan</h4>
                <p class="text-sm text-slate-400 leading-relaxed">
                    Kabupaten Ketapang<br>
                    Kalimantan Barat, Indonesia<br>
                    <span class="text-xs text-slate-500 mt-1 block">Koordinat: -1.8367°, 110.0167°</span>
                </p>
                <p class="text-sm text-slate-400 mt-3 leading-relaxed">Data bersumber dari sensor IoT yang terpasang di titik-titik strategis wilayah pesisir.</p>
            </div>
        </div>

        <div class="border-t border-slate-800 pt-8 flex flex-col sm:flex-row items-center justify-between gap-4">
            <p class="text-xs text-slate-500">© {{ date('Y') }} ROB Monitoring — Early Warning System.</p>
            <p class="text-xs text-slate-500">Kabupaten Ketapang, Kalimantan Barat</p>
        </div>
    </div>
</footer>