<div class="min-h-screen bg-slate-50 p-4 dark:bg-gradient-to-br dark:from-slate-900 dark:via-slate-800 dark:to-slate-900 sm:p-6 lg:p-8"
     x-data="{ showPasswordSection: false, showToast: false, toastMessage: '', toastType: 'success' }"
     x-init="@if(session()->has('success')) showToast = true; toastMessage = '{{ session('success') }}'; toastType = 'success'; setTimeout(() => showToast = false, 4000) @endif
            @if(session()->has('error')) showToast = true; toastMessage = '{{ session('error') }}'; toastType = 'error'; setTimeout(() => showToast = false, 4000) @endif">

    <!-- Toast Notification -->
    <div x-show="showToast"
         x-transition:enter="transition ease-out duration-500"
         x-transition:enter-start="opacity-0 translate-y-4"
         x-transition:enter-end="opacity-100 translate-y-0"
         x-transition:leave="transition ease-in duration-300"
         x-transition:leave-start="opacity-100 translate-y-0"
         x-transition:leave-end="opacity-0 translate-y-4"
         class="fixed top-6 right-6 z-50 flex items-center gap-3 rounded-2xl px-5 py-4 shadow-2xl backdrop-blur-xl"
         :class="toastType === 'success' ? 'bg-emerald-500/90 text-white' : 'bg-red-500/90 text-white'">
        <template x-if="toastType === 'success'">
            <svg class="h-5 w-5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
        </template>
        <template x-if="toastType === 'error'">
            <svg class="h-5 w-5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
        </template>
        <span class="text-sm font-semibold" x-text="toastMessage"></span>
    </div>

    <div class="mx-auto max-w-4xl">

        <!-- Header -->
        <div class="mb-8">
            <h1 class="text-3xl font-extrabold tracking-tight text-slate-900 dark:text-white sm:text-4xl">
                Profil Saya
            </h1>
            <p class="mt-2 text-slate-500 dark:text-slate-400">
                Kelola informasi akun, foto profil, dan keamanan akun Anda.
            </p>
        </div>

        <!-- Profile Card -->
        <div class="overflow-hidden rounded-3xl bg-white shadow-xl ring-1 ring-slate-200 dark:bg-white/5 dark:shadow-2xl dark:ring-white/10 dark:backdrop-blur-xl">

            <!-- ===== PROFILE HEADER ===== -->
            <div class="relative overflow-hidden">

                <!-- Animated Banner -->
                <div class="relative h-40 sm:h-48">
                    <div class="absolute inset-0 bg-gradient-to-br from-violet-600 via-blue-500 to-cyan-400"></div>
                    <div class="absolute inset-0 bg-[radial-gradient(circle_at_30%_20%,rgba(255,255,255,0.15),transparent_50%)]"></div>
                    <div class="absolute inset-0 bg-[radial-gradient(circle_at_70%_80%,rgba(255,255,255,0.1),transparent_50%)]"></div>
                    <div class="absolute -top-10 -right-10 h-40 w-40 rounded-full bg-white/5 blur-xl"></div>
                    <div class="absolute -bottom-10 -left-10 h-32 w-32 rounded-full bg-white/5 blur-xl"></div>
                </div>

                <!-- Avatar with hover overlay -->
                <div class="-mt-16 flex justify-center sm:-mt-20">
                    <div class="group relative cursor-pointer" onclick="document.querySelector('[x-ref=avatarInput]').click()">
                        @if ($foto_profil)
                            <img src="{{ $foto_profil->temporaryUrl() }}"
                                class="h-28 w-28 rounded-full border-4 border-white object-cover shadow-2xl ring-4 ring-violet-500/30 transition-all duration-300 group-hover:ring-violet-500/60 dark:border-slate-900 sm:h-32 sm:w-32">
                        @elseif ($storedFotoProfil)
                            <img src="{{ asset('storage/' . $storedFotoProfil) }}"
                                class="h-28 w-28 rounded-full border-4 border-white object-cover shadow-2xl ring-4 ring-violet-500/30 transition-all duration-300 group-hover:ring-violet-500/60 dark:border-slate-900 sm:h-32 sm:w-32">
                        @else
                            <div class="flex h-28 w-28 items-center justify-center rounded-full border-4 border-white bg-gradient-to-br from-violet-500 to-blue-500 text-4xl font-bold text-white shadow-2xl ring-4 ring-violet-500/30 transition-all duration-300 group-hover:ring-violet-500/60 dark:border-slate-900 sm:h-32 sm:w-32">
                                {{ strtoupper(substr($name, 0, 1)) }}
                            </div>
                        @endif
                        <!-- Hover overlay -->
                        <div class="absolute inset-0 flex items-center justify-center rounded-full bg-black/50 opacity-0 backdrop-blur-sm transition-all duration-300 group-hover:opacity-100">
                            <div class="text-center">
                                <svg class="mx-auto h-6 w-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"/>
                                </svg>
                                <span class="mt-1 block text-xs font-medium text-white">Ubah Foto</span>
                            </div>
                        </div>
                    </div>
                </div>
                <input type="file" wire:model="foto_profil" x-ref="avatarInput" class="hidden" accept="image/*">

                <!-- User Info -->
                <div class="mt-4 px-6 text-center">
                    <h2 class="text-2xl font-bold text-slate-900 dark:text-white">
                        {{ $name }}
                    </h2>
                    <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">
                        {{ $email }}
                    </p>
                </div>

                <!-- Stats Row -->
                <div class="mt-6 grid grid-cols-3 gap-4 px-6 pb-8">
                    <div class="rounded-2xl bg-slate-100 p-4 text-center ring-1 ring-slate-200 transition-all duration-300 hover:bg-slate-200 hover:ring-slate-300 dark:bg-white/5 dark:ring-white/10 dark:hover:bg-white/10 dark:hover:ring-white/20">
                        <div class="mx-auto mb-2 flex h-10 w-10 items-center justify-center rounded-xl bg-emerald-100 dark:bg-emerald-500/20">
                            <svg class="h-5 w-5 text-emerald-600 dark:text-emerald-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                        <p class="text-xs text-slate-400">Status</p>
                        <p class="mt-0.5 text-sm font-bold text-emerald-600 dark:text-emerald-400">Aktif</p>
                    </div>
                    <div class="rounded-2xl bg-slate-100 p-4 text-center ring-1 ring-slate-200 transition-all duration-300 hover:bg-slate-200 hover:ring-slate-300 dark:bg-white/5 dark:ring-white/10 dark:hover:bg-white/10 dark:hover:ring-white/20">
                        <div class="mx-auto mb-2 flex h-10 w-10 items-center justify-center rounded-xl bg-violet-100 dark:bg-violet-500/20">
                            <svg class="h-5 w-5 text-violet-600 dark:text-violet-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                            </svg>
                        </div>
                        <p class="text-xs text-slate-400">Role</p>
                        <p class="mt-0.5 text-sm font-bold text-slate-700 dark:text-white">{{ $roleName ?? 'No Role' }}</p>
                    </div>
                    <div class="rounded-2xl bg-slate-100 p-4 text-center ring-1 ring-slate-200 transition-all duration-300 hover:bg-slate-200 hover:ring-slate-300 dark:bg-white/5 dark:ring-white/10 dark:hover:bg-white/10 dark:hover:ring-white/20">
                        <div class="mx-auto mb-2 flex h-10 w-10 items-center justify-center rounded-xl bg-blue-100 dark:bg-blue-500/20">
                            <svg class="h-5 w-5 text-blue-600 dark:text-blue-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"/>
                            </svg>
                        </div>
                        <p class="text-xs text-slate-400">QR</p>
                        <p class="mt-0.5 text-sm font-bold text-slate-700 dark:text-white">Tersedia</p>
                    </div>
                </div>
            </div>

            <!-- Divider -->
            <div class="border-t border-slate-200 dark:border-white/10"></div>

            <!-- ===== FORM SECTION ===== -->
            <div class="p-6 sm:p-8 lg:p-10">

                <!-- FORM PROFILE -->
                <div class="mb-10">
                    <div class="mb-6 flex items-center gap-3">
                        <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-violet-100 dark:bg-violet-500/20">
                            <svg class="h-5 w-5 text-violet-600 dark:text-violet-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-lg font-bold text-slate-900 dark:text-white">Informasi Profil</h3>
                            <p class="text-xs text-slate-500 dark:text-slate-500">Perbarui data diri Anda</p>
                        </div>
                    </div>

                    <form wire:submit.prevent="updateProfile" class="space-y-6">
                        <div class="grid grid-cols-1 gap-6 md:grid-cols-2">

                            <!-- Nama -->
                            <div class="group relative">
                                <label class="mb-2 block text-xs font-semibold uppercase tracking-wider text-slate-500 transition-colors group-focus-within:text-violet-600 dark:text-slate-400 dark:group-focus-within:text-violet-400">Nama Lengkap</label>
                                <div class="relative">
                                    <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-4">
                                        <svg class="h-5 w-5 text-slate-400 transition-colors group-focus-within:text-violet-500 dark:text-slate-500 dark:group-focus-within:text-violet-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                        </svg>
                                    </div>
                                    <input type="text" wire:model.defer="name"
                                        class="h-12 w-full rounded-2xl border border-slate-200 bg-slate-50 pl-12 pr-4 text-sm text-slate-900 placeholder-slate-400 transition-all duration-300 focus:border-violet-500/50 focus:bg-white focus:ring-2 focus:ring-violet-500/20 focus:outline-none dark:border-white/10 dark:bg-white/5 dark:text-white dark:placeholder-slate-500 dark:backdrop-blur-sm dark:focus:border-violet-500/50 dark:focus:bg-white/10"
                                        placeholder="Masukkan nama lengkap">
                                </div>
                                @error('name')
                                    <p class="mt-2 text-xs text-red-500 dark:text-red-400">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Email -->
                            <div class="group relative">
                                <label class="mb-2 block text-xs font-semibold uppercase tracking-wider text-slate-500 transition-colors group-focus-within:text-violet-600 dark:text-slate-400 dark:group-focus-within:text-violet-400">Alamat Email</label>
                                <div class="relative">
                                    <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-4">
                                        <svg class="h-5 w-5 text-slate-400 transition-colors group-focus-within:text-violet-500 dark:text-slate-500 dark:group-focus-within:text-violet-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                                        </svg>
                                    </div>
                                    <input type="email" wire:model.defer="email"
                                        class="h-12 w-full rounded-2xl border border-slate-200 bg-slate-50 pl-12 pr-4 text-sm text-slate-900 placeholder-slate-400 transition-all duration-300 focus:border-violet-500/50 focus:bg-white focus:ring-2 focus:ring-violet-500/20 focus:outline-none dark:border-white/10 dark:bg-white/5 dark:text-white dark:placeholder-slate-500 dark:backdrop-blur-sm dark:focus:border-violet-500/50 dark:focus:bg-white/10"
                                        placeholder="email@contoh.com">
                                </div>
                                @error('email')
                                    <p class="mt-2 text-xs text-red-500 dark:text-red-400">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <!-- File Upload -->
                        <div class="group relative">
                            <label class="mb-2 block text-xs font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400">Foto Profil</label>
                            <div class="relative overflow-hidden rounded-2xl border-2 border-dashed border-slate-300 bg-slate-50 p-6 text-center transition-all duration-300 hover:border-violet-400 hover:bg-violet-50 dark:border-white/10 dark:bg-white/5 dark:hover:border-violet-500/30 dark:hover:bg-white/10"
                                 x-data="{ dragging: false }"
                                 @dragover.prevent="dragging = true"
                                 @dragleave.prevent="dragging = false"
                                 @drop.prevent="dragging = false"
                                 :class="dragging && 'border-violet-500 bg-violet-50 dark:bg-violet-500/10'">
                                <div class="pointer-events-none">
                                    <svg class="mx-auto h-10 w-10 text-slate-400 dark:text-slate-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                    </svg>
                                    <p class="mt-3 text-sm text-slate-500 dark:text-slate-400">
                                        <span class="font-semibold text-violet-600 dark:text-violet-400">Klik untuk upload</span> atau drag & drop
                                    </p>
                                    <p class="mt-1 text-xs text-slate-400 dark:text-slate-500">PNG, JPG, WEBP hingga 2MB</p>
                                </div>
                                @if ($foto_profil)
                                    <div class="mt-3 flex items-center justify-center gap-2 text-sm text-emerald-600 dark:text-emerald-400">
                                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                                        </svg>
                                        {{ $foto_profil->getClientOriginalName() }}
                                    </div>
                                @endif
                            </div>
                            @error('foto_profil')
                                <p class="mt-2 text-xs text-red-500 dark:text-red-400">{{ $message }}</p>
                            @enderror
                        </div>

                        <button type="submit"
                            class="group relative h-14 w-full overflow-hidden rounded-2xl bg-gradient-to-r from-violet-600 to-blue-500 text-sm font-bold text-white shadow-lg shadow-violet-500/25 transition-all duration-300 hover:shadow-xl hover:shadow-violet-500/30 hover:scale-[1.02] active:scale-[0.98]"
                            wire:loading.attr="disabled"
                            wire:target="updateProfile">
                            <span class="relative z-10 flex items-center justify-center gap-2">
                                <svg class="h-5 w-5" wire:loading.remove wire:target="updateProfile" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                                </svg>
                                <span wire:loading.remove wire:target="updateProfile">Simpan Perubahan</span>
                                <span wire:loading wire:target="updateProfile" class="flex items-center gap-2">
                                    <svg class="h-5 w-5 animate-spin" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                    </svg>
                                    Menyimpan...
                                </span>
                            </span>
                            <div class="absolute inset-0 bg-gradient-to-r from-violet-500 to-blue-400 opacity-0 transition-opacity duration-300 group-hover:opacity-100"></div>
                        </button>
                    </form>
                </div>

                <!-- ===== PASSWORD SECTION ===== -->
                <div class="border-t border-slate-200 pt-10 dark:border-white/10">
                    <button @click="showPasswordSection = !showPasswordSection"
                        class="group flex w-full items-center justify-between rounded-2xl bg-slate-100 p-4 ring-1 ring-slate-200 transition-all duration-300 hover:bg-slate-200 hover:ring-slate-300 dark:bg-white/5 dark:ring-white/10 dark:hover:bg-white/10 dark:hover:ring-white/20">
                        <div class="flex items-center gap-3">
                            <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-amber-100 dark:bg-amber-500/20">
                                <svg class="h-5 w-5 text-amber-600 dark:text-amber-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                                </svg>
                            </div>
                            <div class="text-left">
                                <h4 class="text-sm font-bold text-slate-900 dark:text-white">Ubah Password</h4>
                                <p class="text-xs text-slate-500 dark:text-slate-500">Perbarui password untuk keamanan akun</p>
                            </div>
                        </div>
                        <svg class="h-5 w-5 text-slate-400 transition-transform duration-300" :class="showPasswordSection && 'rotate-180'" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/>
                        </svg>
                    </button>

                    <div x-show="showPasswordSection"
                         x-transition:enter="transition ease-out duration-300"
                         x-transition:enter-start="opacity-0 -translate-y-4"
                         x-transition:enter-end="opacity-100 translate-y-0"
                         x-transition:leave="transition ease-in duration-200"
                         x-transition:leave-start="opacity-100 translate-y-0"
                         x-transition:leave-end="opacity-0 -translate-y-4"
                         class="mt-4">
                        <form wire:submit.prevent="updatePassword" class="space-y-5">

                            <!-- Current Password -->
                            <div class="group relative" x-data="{ show: false }">
                                <label class="mb-2 block text-xs font-semibold uppercase tracking-wider text-slate-500 transition-colors group-focus-within:text-amber-600 dark:text-slate-400 dark:group-focus-within:text-amber-400">Password Saat Ini</label>
                                <div class="relative">
                                    <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-4">
                                        <svg class="h-5 w-5 text-slate-400 transition-colors group-focus-within:text-amber-500 dark:text-slate-500 dark:group-focus-within:text-amber-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                                        </svg>
                                    </div>
                                    <input :type="show ? 'text' : 'password'" wire:model.defer="current_password"
                                        class="h-12 w-full rounded-2xl border border-slate-200 bg-slate-50 pl-12 pr-12 text-sm text-slate-900 placeholder-slate-400 transition-all duration-300 focus:border-amber-500/50 focus:bg-white focus:ring-2 focus:ring-amber-500/20 focus:outline-none dark:border-white/10 dark:bg-white/5 dark:text-white dark:placeholder-slate-500 dark:backdrop-blur-sm dark:focus:border-amber-500/50 dark:focus:bg-white/10"
                                        placeholder="Masukkan password saat ini">
                                    <button type="button" @click="show = !show" class="absolute inset-y-0 right-0 flex items-center pr-4 text-slate-400 transition-colors hover:text-slate-700 dark:text-slate-500 dark:hover:text-white">
                                        <svg x-show="!show" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                        </svg>
                                        <svg x-show="show" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/>
                                        </svg>
                                    </button>
                                </div>
                                @error('current_password')
                                    <p class="mt-2 text-xs text-red-500 dark:text-red-400">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="grid grid-cols-1 gap-5 md:grid-cols-2">

                                <!-- New Password -->
                                <div class="group relative" x-data="{ show: false }">
                                    <label class="mb-2 block text-xs font-semibold uppercase tracking-wider text-slate-500 transition-colors group-focus-within:text-amber-600 dark:text-slate-400 dark:group-focus-within:text-amber-400">Password Baru</label>
                                    <div class="relative">
                                        <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-4">
                                            <svg class="h-5 w-5 text-slate-400 transition-colors group-focus-within:text-amber-500 dark:text-slate-500 dark:group-focus-within:text-amber-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"/>
                                            </svg>
                                        </div>
                                        <input :type="show ? 'text' : 'password'" wire:model.defer="new_password"
                                            class="h-12 w-full rounded-2xl border border-slate-200 bg-slate-50 pl-12 pr-12 text-sm text-slate-900 placeholder-slate-400 transition-all duration-300 focus:border-amber-500/50 focus:bg-white focus:ring-2 focus:ring-amber-500/20 focus:outline-none dark:border-white/10 dark:bg-white/5 dark:text-white dark:placeholder-slate-500 dark:backdrop-blur-sm dark:focus:border-amber-500/50 dark:focus:bg-white/10"
                                            placeholder="Password baru">
                                        <button type="button" @click="show = !show" class="absolute inset-y-0 right-0 flex items-center pr-4 text-slate-400 transition-colors hover:text-slate-700 dark:text-slate-500 dark:hover:text-white">
                                            <svg x-show="!show" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                            </svg>
                                            <svg x-show="show" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/>
                                            </svg>
                                        </button>
                                    </div>
                                    @error('new_password')
                                        <p class="mt-2 text-xs text-red-500 dark:text-red-400">{{ $message }}</p>
                                    @enderror
                                </div>

                                <!-- Confirm Password -->
                                <div class="group relative" x-data="{ show: false }">
                                    <label class="mb-2 block text-xs font-semibold uppercase tracking-wider text-slate-500 transition-colors group-focus-within:text-amber-600 dark:text-slate-400 dark:group-focus-within:text-amber-400">Konfirmasi Password</label>
                                    <div class="relative">
                                        <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-4">
                                            <svg class="h-5 w-5 text-slate-400 transition-colors group-focus-within:text-amber-500 dark:text-slate-500 dark:group-focus-within:text-amber-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                                            </svg>
                                        </div>
                                        <input :type="show ? 'text' : 'password'" wire:model.defer="new_password_confirmation"
                                            class="h-12 w-full rounded-2xl border border-slate-200 bg-slate-50 pl-12 pr-12 text-sm text-slate-900 placeholder-slate-400 transition-all duration-300 focus:border-amber-500/50 focus:bg-white focus:ring-2 focus:ring-amber-500/20 focus:outline-none dark:border-white/10 dark:bg-white/5 dark:text-white dark:placeholder-slate-500 dark:backdrop-blur-sm dark:focus:border-amber-500/50 dark:focus:bg-white/10"
                                            placeholder="Konfirmasi password baru">
                                        <button type="button" @click="show = !show" class="absolute inset-y-0 right-0 flex items-center pr-4 text-slate-400 transition-colors hover:text-slate-700 dark:text-slate-500 dark:hover:text-white">
                                            <svg x-show="!show" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                            </svg>
                                            <svg x-show="show" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/>
                                            </svg>
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <!-- Password Requirements -->
                            <div class="rounded-2xl bg-slate-100 p-4 ring-1 ring-slate-200 dark:bg-white/5 dark:ring-white/10">
                                <p class="mb-3 text-xs font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400">Persyaratan Password</p>
                                <div class="grid grid-cols-1 gap-2 sm:grid-cols-2">
                                    <div class="flex items-center gap-2 text-xs text-slate-500 dark:text-slate-500">
                                        <div class="flex h-4 w-4 items-center justify-center rounded-full bg-slate-200 dark:bg-slate-700">
                                            <svg class="h-2.5 w-2.5 text-slate-500 dark:text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                                            </svg>
                                        </div>
                                        Minimal 8 karakter
                                    </div>
                                    <div class="flex items-center gap-2 text-xs text-slate-500 dark:text-slate-500">
                                        <div class="flex h-4 w-4 items-center justify-center rounded-full bg-slate-200 dark:bg-slate-700">
                                            <svg class="h-2.5 w-2.5 text-slate-500 dark:text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                                            </svg>
                                        </div>
                                        Mengandung huruf besar
                                    </div>
                                    <div class="flex items-center gap-2 text-xs text-slate-500 dark:text-slate-500">
                                        <div class="flex h-4 w-4 items-center justify-center rounded-full bg-slate-200 dark:bg-slate-700">
                                            <svg class="h-2.5 w-2.5 text-slate-500 dark:text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                                            </svg>
                                        </div>
                                        Mengandung angka
                                    </div>
                                    <div class="flex items-center gap-2 text-xs text-slate-500 dark:text-slate-500">
                                        <div class="flex h-4 w-4 items-center justify-center rounded-full bg-slate-200 dark:bg-slate-700">
                                            <svg class="h-2.5 w-2.5 text-slate-500 dark:text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                                            </svg>
                                        </div>
                                        Mengandung karakter khusus
                                    </div>
                                </div>
                            </div>

                            <button type="submit"
                                class="group relative h-14 w-full overflow-hidden rounded-2xl bg-gradient-to-r from-amber-500 to-orange-500 text-sm font-bold text-white shadow-lg shadow-amber-500/25 transition-all duration-300 hover:shadow-xl hover:shadow-amber-500/30 hover:scale-[1.02] active:scale-[0.98]"
                                wire:loading.attr="disabled"
                                wire:target="updatePassword">
                                <span class="relative z-10 flex items-center justify-center gap-2">
                                    <svg class="h-5 w-5" wire:loading.remove wire:target="updatePassword" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                                    </svg>
                                    <span wire:loading.remove wire:target="updatePassword">Update Password</span>
                                    <span wire:loading wire:target="updatePassword" class="flex items-center gap-2">
                                        <svg class="h-5 w-5 animate-spin" fill="none" viewBox="0 0 24 24">
                                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                        </svg>
                                        Memproses...
                                    </span>
                                </span>
                                <div class="absolute inset-0 bg-gradient-to-r from-amber-400 to-orange-400 opacity-0 transition-opacity duration-300 group-hover:opacity-100"></div>
                            </button>
                        </form>
                    </div>
                </div>

                <!-- ===== QR CODE SECTION ===== -->
                <div class="mt-10 border-t border-slate-200 pt-10 dark:border-white/10">
                    <div class="mb-6 flex items-center gap-3">
                        <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-cyan-100 dark:bg-cyan-500/20">
                            <svg class="h-5 w-5 text-cyan-600 dark:text-cyan-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"/>
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-lg font-bold text-slate-900 dark:text-white">QR Identitas</h3>
                            <p class="text-xs text-slate-500 dark:text-slate-500">Scan untuk verifikasi identitas</p>
                        </div>
                    </div>
                    <div class="flex justify-center">
                        <div class="group relative">
                            <div class="absolute -inset-1 rounded-3xl bg-gradient-to-r from-cyan-500 to-blue-500 opacity-0 blur-lg transition-opacity duration-500 group-hover:opacity-30 dark:opacity-20 dark:group-hover:opacity-40"></div>
                            <div class="relative rounded-2xl bg-white p-5 shadow-lg ring-1 ring-slate-200 transition-transform duration-300 group-hover:scale-105 dark:shadow-2xl dark:ring-white/20">
                                {!! $qrCode !!}
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>

        <!-- Footer -->
        <div class="mt-6 text-center">
            <p class="text-xs text-slate-400 dark:text-slate-600">
                ROB Monitoring System &copy; {{ date('Y') }}
            </p>
        </div>
    </div>
</div>
