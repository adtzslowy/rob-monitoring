<div class="min-h-screen bg-slate-50 p-4 transition-colors duration-300 dark:bg-slate-950 sm:p-6 lg:p-8">
    <div class="mx-auto max-w-4xl">

        {{-- Header --}}
        <div class="mb-6 sm:mb-8">
            <h1 class="text-2xl font-bold tracking-tight text-slate-800 dark:text-slate-100 sm:text-3xl">
                Pengaturan
            </h1>
            <p class="mt-2 text-sm leading-6 text-slate-500 dark:text-slate-400">
                @if ($isAdmin)
                    Kelola threshold sensor dan notifikasi Telegram sistem ROB Monitoring.
                @else
                    Kelola preferensi notifikasi Telegram Anda.
                @endif
            </p>
        </div>

        {{-- Alert success --}}
        @if (session()->has('success'))
            <div class="mb-5 flex items-center gap-3 rounded-2xl border border-emerald-500/20 bg-emerald-50 px-4 py-3 dark:bg-emerald-500/10">
                <svg class="h-5 w-5 shrink-0 text-emerald-500" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <p class="text-sm font-medium text-emerald-700 dark:text-emerald-300">{{ session('success') }}</p>
            </div>
        @endif

        {{-- Alert error --}}
        @if (session()->has('error'))
            <div class="mb-5 flex items-center gap-3 rounded-2xl border border-rose-500/20 bg-rose-50 px-4 py-3 dark:bg-rose-500/10">
                <svg class="h-5 w-5 shrink-0 text-rose-500" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9 3.75h.008v.008H12v-.008z" />
                </svg>
                <p class="text-sm font-medium text-rose-700 dark:text-rose-300">{{ session('error') }}</p>
            </div>
        @endif

        <div class="space-y-6">

            {{-- ===== THRESHOLD SENSOR (Admin only) ===== --}}
            @if ($isAdmin)
                <div class="overflow-hidden rounded-3xl bg-white shadow-sm ring-1 ring-slate-200 transition-colors duration-300 dark:bg-slate-900 dark:ring-slate-800">

                    <div class="relative border-b border-slate-100 px-5 py-4 dark:border-slate-800 sm:px-6 sm:py-5">
                        <div class="absolute inset-x-0 top-0 h-px bg-gradient-to-r from-transparent via-orange-400/50 to-transparent"></div>
                        <div class="flex items-center gap-3">
                            <div class="flex h-9 w-9 items-center justify-center rounded-xl bg-orange-100 dark:bg-orange-500/10">
                                <svg class="h-5 w-5 text-orange-500" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z" />
                                </svg>
                            </div>
                            <div>
                                <h2 class="text-base font-semibold text-slate-800 dark:text-slate-100">Threshold Sensor</h2>
                                <p class="text-xs text-slate-500 dark:text-slate-400">Batas nilai sensor untuk menentukan status AMAN, WASPADA, SIAGA, dan BAHAYA</p>
                            </div>
                        </div>
                    </div>

                    <form wire:submit.prevent="saveThreshold" class="p-5 sm:p-6">
                        <div class="space-y-4">

                            @php
                                $sensors = [
                                    'ketinggian_air'  => ['label' => 'Ketinggian Air',  'unit' => 'cm',  'dot' => 'bg-sky-500'],
                                    'suhu'            => ['label' => 'Suhu',             'unit' => '°C',  'dot' => 'bg-orange-500'],
                                    'kelembapan'      => ['label' => 'Kelembapan',       'unit' => '%',   'dot' => 'bg-cyan-500'],
                                    'tekanan_udara'   => ['label' => 'Tekanan Udara',    'unit' => 'hPa', 'dot' => 'bg-emerald-500'],
                                    'kecepatan_angin' => ['label' => 'Kecepatan Angin',  'unit' => 'm/s', 'dot' => 'bg-amber-500'],
                                    'arah_angin'      => ['label' => 'Arah Angin',       'unit' => '°',   'dot' => 'bg-purple-500'],
                                ];
                            @endphp

                            @foreach ($sensors as $key => $sensor)
                                <div class="rounded-2xl border border-slate-100 bg-slate-50/60 p-4 dark:border-slate-800 dark:bg-slate-800/40 sm:p-5">
                                    <div class="mb-4 flex items-center gap-2">
                                        <span class="inline-flex h-2.5 w-2.5 rounded-full {{ $sensor['dot'] }}"></span>
                                        <h3 class="text-sm font-semibold text-slate-700 dark:text-slate-200">
                                            {{ $sensor['label'] }} ({{ $sensor['unit'] }})
                                        </h3>
                                    </div>
                                    <div class="grid grid-cols-1 gap-3 sm:grid-cols-2">
                                        <div>
                                            <label class="mb-1.5 block text-xs font-medium text-amber-600 dark:text-amber-400">Batas Waspada</label>
                                            <input type="number" step="0.01"
                                                wire:model.defer="threshold.{{ $key }}.waspada"
                                                class="h-10 w-full rounded-xl border border-slate-200 bg-white px-3 text-sm text-slate-900 outline-none transition
                                                       focus:border-amber-400 focus:ring-2 focus:ring-amber-400/20
                                                       dark:border-slate-700 dark:bg-slate-900 dark:text-white dark:focus:border-amber-500"
                                                placeholder="Nilai waspada">
                                            @error("threshold.{$key}.waspada")
                                                <p class="mt-1 text-xs text-rose-500">{{ $message }}</p>
                                            @enderror
                                        </div>
                                        <div>
                                            <label class="mb-1.5 block text-xs font-medium text-rose-600 dark:text-rose-400">Batas Bahaya</label>
                                            <input type="number" step="0.01"
                                                wire:model.defer="threshold.{{ $key }}.bahaya"
                                                class="h-10 w-full rounded-xl border border-slate-200 bg-white px-3 text-sm text-slate-900 outline-none transition
                                                       focus:border-rose-400 focus:ring-2 focus:ring-rose-400/20
                                                       dark:border-slate-700 dark:bg-slate-900 dark:text-white dark:focus:border-rose-500"
                                                placeholder="Nilai bahaya">
                                            @error("threshold.{$key}.bahaya")
                                                <p class="mt-1 text-xs text-rose-500">{{ $message }}</p>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                            @endforeach

                        </div>

                        {{-- Legend --}}
                        <div class="mt-4 flex flex-wrap items-center gap-4 rounded-2xl border border-dashed border-slate-200 px-4 py-3 dark:border-slate-700">
                            <div class="flex items-center gap-2">
                                <span class="inline-flex h-2 w-2 rounded-full bg-emerald-500"></span>
                                <span class="text-xs text-slate-500 dark:text-slate-400">Aman</span>
                            </div>
                            <div class="flex items-center gap-2">
                                <span class="inline-flex h-2 w-2 rounded-full bg-amber-500"></span>
                                <span class="text-xs text-slate-500 dark:text-slate-400">Waspada</span>
                            </div>
                            <div class="flex items-center gap-2">
                                <span class="inline-flex h-2 w-2 rounded-full bg-orange-500"></span>
                                <span class="text-xs text-slate-500 dark:text-slate-400">Siaga</span>
                            </div>
                            <div class="flex items-center gap-2">
                                <span class="inline-flex h-2 w-2 rounded-full bg-rose-500"></span>
                                <span class="text-xs text-slate-500 dark:text-slate-400">Bahaya</span>
                            </div>
                        </div>

                        <div class="mt-5 flex justify-end">
                            <button type="submit"
                                class="inline-flex h-11 items-center justify-center gap-2 rounded-2xl bg-gradient-to-r from-orange-500 to-rose-500 px-6 text-sm font-semibold text-white shadow-[0_8px_20px_rgba(249,115,22,0.25)] transition hover:scale-[1.01] hover:from-orange-600 hover:to-rose-600">
                                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" />
                                </svg>
                                Simpan Threshold
                            </button>
                        </div>
                    </form>
                </div>
            @endif

            {{-- ===== NOTIFIKASI TELEGRAM ===== --}}
            <div class="overflow-hidden rounded-3xl bg-white shadow-sm ring-1 ring-slate-200 transition-colors duration-300 dark:bg-slate-900 dark:ring-slate-800">

                <div class="relative border-b border-slate-100 px-5 py-4 dark:border-slate-800 sm:px-6 sm:py-5">
                    <div class="absolute inset-x-0 top-0 h-px bg-gradient-to-r from-transparent via-blue-400/50 to-transparent"></div>
                    <div class="flex items-center gap-3">
                        <div class="flex h-9 w-9 items-center justify-center rounded-xl bg-blue-100 dark:bg-blue-500/10">
                            <svg class="h-5 w-5 text-blue-500" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M11.944 0A12 12 0 0 0 0 12a12 12 0 0 0 12 12 12 12 0 0 0 12-12A12 12 0 0 0 12 0a12 12 0 0 0-.056 0zm4.962 7.224c.1-.002.321.023.465.14a.506.506 0 0 1 .171.325c.016.093.036.306.02.472-.18 1.898-.962 6.502-1.36 8.627-.168.9-.499 1.201-.82 1.23-.696.065-1.225-.46-1.9-.902-1.056-.693-1.653-1.124-2.678-1.8-1.185-.78-.417-1.21.258-1.91.177-.184 3.247-2.977 3.307-3.23.007-.032.014-.15-.056-.212s-.174-.041-.249-.024c-.106.024-1.793 1.14-5.061 3.345-.48.33-.913.49-1.302.48-.428-.008-1.252-.241-1.865-.44-.752-.245-1.349-.374-1.297-.789.027-.216.325-.437.893-.663 3.498-1.524 5.83-2.529 6.998-3.014 3.332-1.386 4.025-1.627 4.476-1.635z"/>
                            </svg>
                        </div>
                        <div>
                            <h2 class="text-base font-semibold text-slate-800 dark:text-slate-100">Notifikasi Telegram</h2>
                            <p class="text-xs text-slate-500 dark:text-slate-400">Terima peringatan otomatis via Telegram saat status sensor berubah</p>
                        </div>
                    </div>
                </div>

                <form wire:submit.prevent="saveNotifikasi" class="p-5 sm:p-6">
                    <div class="space-y-5">

                        {{-- Toggle aktif --}}
                        <div class="flex items-center justify-between rounded-2xl border border-slate-100 bg-slate-50/60 px-4 py-3.5 dark:border-slate-800 dark:bg-slate-800/40">
                            <div>
                                <p class="text-sm font-medium text-slate-700 dark:text-slate-200">Aktifkan Notifikasi</p>
                                <p class="text-xs text-slate-500 dark:text-slate-400">Kirim pesan Telegram saat status berubah</p>
                            </div>
                            <button type="button" wire:click="toggleNotifikasi"
                                class="relative inline-flex h-6 w-11 flex-shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none
                                       {{ $notifikasi_aktif ? 'bg-blue-500' : 'bg-slate-300 dark:bg-slate-600' }}">
                                <span class="inline-block h-5 w-5 transform rounded-full bg-white shadow ring-0 transition duration-200 ease-in-out
                                             {{ $notifikasi_aktif ? 'translate-x-5' : 'translate-x-0' }}"></span>
                            </button>
                        </div>

                        {{-- Chat ID --}}
                        <div>
                            <label class="mb-1.5 block text-sm font-semibold text-slate-700 dark:text-slate-200">
                                Telegram Chat ID
                            </label>
                            <p class="mb-2 text-xs text-slate-500 dark:text-slate-400">
                                Chat bot kamu lalu buka:
                                <code class="rounded bg-slate-100 px-1 py-0.5 text-xs dark:bg-slate-800">api.telegram.org/bot&lt;TOKEN&gt;/getUpdates</code>
                            </p>
                            <input type="text" wire:model.defer="telegram_chat_id"
                                class="h-11 w-full rounded-xl border border-slate-200 bg-white px-4 text-sm text-slate-900 outline-none transition
                                       placeholder:text-slate-400 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20
                                       dark:border-slate-700 dark:bg-slate-800/80 dark:text-white dark:placeholder:text-slate-500
                                       dark:focus:border-blue-500"
                                placeholder="Contoh: 123456789">
                            @error('telegram_chat_id')
                                <p class="mt-1.5 text-xs text-rose-500">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Trigger status --}}
                        <div>
                            <label class="mb-2 block text-sm font-semibold text-slate-700 dark:text-slate-200">
                                Kirim notifikasi saat status
                            </label>
                            <div class="grid grid-cols-1 gap-2 sm:grid-cols-3">

                                <label class="flex cursor-pointer items-center gap-3 rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 transition hover:bg-slate-100 dark:border-slate-700 dark:bg-slate-800/40 dark:hover:bg-slate-800">
                                    <input type="checkbox" wire:model="notifikasi_waspada"
                                        class="h-4 w-4 rounded border-slate-300 text-amber-500 focus:ring-amber-400 dark:border-slate-600">
                                    <div class="flex items-center gap-2">
                                        <span class="inline-flex h-2 w-2 rounded-full bg-amber-500"></span>
                                        <span class="text-sm font-medium text-slate-700 dark:text-slate-200">WASPADA</span>
                                    </div>
                                </label>

                                <label class="flex cursor-pointer items-center gap-3 rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 transition hover:bg-slate-100 dark:border-slate-700 dark:bg-slate-800/40 dark:hover:bg-slate-800">
                                    <input type="checkbox" wire:model="notifikasi_siaga"
                                        class="h-4 w-4 rounded border-slate-300 text-orange-500 focus:ring-orange-400 dark:border-slate-600">
                                    <div class="flex items-center gap-2">
                                        <span class="inline-flex h-2 w-2 rounded-full bg-orange-500"></span>
                                        <span class="text-sm font-medium text-slate-700 dark:text-slate-200">SIAGA</span>
                                    </div>
                                </label>

                                <label class="flex cursor-pointer items-center gap-3 rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 transition hover:bg-slate-100 dark:border-slate-700 dark:bg-slate-800/40 dark:hover:bg-slate-800">
                                    <input type="checkbox" wire:model="notifikasi_bahaya"
                                        class="h-4 w-4 rounded border-slate-300 text-rose-500 focus:ring-rose-400 dark:border-slate-600">
                                    <div class="flex items-center gap-2">
                                        <span class="inline-flex h-2 w-2 rounded-full bg-rose-500"></span>
                                        <span class="text-sm font-medium text-slate-700 dark:text-slate-200">BAHAYA</span>
                                    </div>
                                </label>

                            </div>
                        </div>

                        {{-- Test notifikasi --}}
                        <div class="rounded-2xl border border-dashed border-blue-200 bg-blue-50/50 px-4 py-4 dark:border-blue-500/20 dark:bg-blue-500/5">
                            <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                                <div>
                                    <p class="text-sm font-medium text-slate-700 dark:text-slate-200">Uji Notifikasi</p>
                                    <p class="text-xs text-slate-500 dark:text-slate-400">Kirim pesan test ke Chat ID yang sudah diisi</p>
                                </div>
                                <button type="button" wire:click="testNotifikasi" wire:loading.attr="disabled" wire:target="testNotifikasi"
                                    class="inline-flex h-9 items-center justify-center gap-2 rounded-xl border border-blue-300 bg-white px-4 text-sm font-medium text-blue-700 transition
                                           hover:bg-blue-50 disabled:opacity-50
                                           dark:border-blue-500/30 dark:bg-blue-500/10 dark:text-blue-400 dark:hover:bg-blue-500/20">
                                    <span wire:loading.remove wire:target="testNotifikasi">
                                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 12L3.269 3.126A59.768 59.768 0 0121.485 12 59.77 59.77 0 013.27 20.876L5.999 12zm0 0h7.5" />
                                        </svg>
                                    </span>
                                    <span wire:loading wire:target="testNotifikasi">
                                        <svg class="h-4 w-4 animate-spin" fill="none" viewBox="0 0 24 24">
                                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                                        </svg>
                                    </span>
                                    <span wire:loading.remove wire:target="testNotifikasi">Kirim Test</span>
                                    <span wire:loading wire:target="testNotifikasi">Mengirim...</span>
                                </button>
                            </div>
                        </div>

                    </div>

                    <div class="mt-5 flex justify-end">
                        <button type="submit"
                            class="inline-flex h-11 items-center justify-center gap-2 rounded-2xl bg-gradient-to-r from-blue-600 to-indigo-600 px-6 text-sm font-semibold text-white shadow-[0_8px_20px_rgba(37,99,235,0.25)] transition hover:scale-[1.01] hover:from-blue-700 hover:to-indigo-700">
                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" />
                            </svg>
                            Simpan Notifikasi
                        </button>
                    </div>
                </form>
            </div>

        </div>
    </div>
</div>