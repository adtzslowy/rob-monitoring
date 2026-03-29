<div class="min-h-screen bg-slate-50 p-4 transition-colors duration-300 dark:bg-slate-950 sm:p-6 lg:p-8">
    <div class="mx-auto max-w-7xl">
        <!-- Header -->
        <div class="mb-6 sm:mb-8">
            <h1 class="text-2xl font-bold tracking-tight text-slate-800 dark:text-slate-100 sm:text-3xl">
                Profil Saya
            </h1>
            <p class="mt-2 max-w-2xl text-sm leading-6 text-slate-500 dark:text-slate-400">
                Kelola informasi akun, foto profil, dan password Anda dengan tampilan yang nyaman di desktop, tablet, dan handphone.
            </p>
        </div>

        <!-- Layout -->
        <div class="grid grid-cols-1 gap-5 lg:grid-cols-3 lg:gap-6">
            <!-- Sidebar / Profile Card -->
            <div class="lg:col-span-1">
                <div class="overflow-hidden rounded-3xl bg-white shadow-sm ring-1 ring-slate-200 transition-colors duration-300 dark:bg-slate-900 dark:ring-slate-800">
                    <!-- Header Banner -->
                    <div class="relative h-28 overflow-hidden bg-gradient-to-br from-blue-600 via-indigo-500 to-cyan-400 sm:h-32 lg:h-36">
                        <div class="absolute inset-0 bg-[radial-gradient(circle_at_top_right,rgba(255,255,255,0.25),transparent_35%)]"></div>
                        <div class="absolute -right-8 -top-8 h-24 w-24 rounded-full bg-white/10 sm:h-28 sm:w-28"></div>
                        <div class="absolute -left-10 bottom-0 h-20 w-20 rounded-full bg-black/10 sm:h-24 sm:w-24"></div>

                        <div class="absolute left-4 top-4 sm:left-5 sm:top-5">
                            <span class="inline-flex items-center rounded-full bg-white/20 px-3 py-1 text-[11px] font-semibold text-white backdrop-blur-sm sm:text-xs">
                                My Profile
                            </span>
                        </div>
                    </div>

                    <div class="relative px-4 pb-5 sm:px-6 sm:pb-6">
                        <!-- Avatar -->
                        <div class="-mt-12 mb-4 flex justify-center sm:-mt-14">
                            @if ($foto_profil)
                                <img src="{{ $foto_profil->temporaryUrl() }}" alt="Preview Foto Profil"
                                    class="h-24 w-24 rounded-full border-4 border-white object-cover shadow-lg dark:border-slate-900 sm:h-28 sm:w-28">
                            @elseif ($storedFotoProfil)
                                <img src="{{ asset('storage/' . $storedFotoProfil) }}" alt="Foto Profil"
                                    class="h-24 w-24 rounded-full border-4 border-white object-cover shadow-lg dark:border-slate-900 sm:h-28 sm:w-28">
                            @else
                                <div class="flex h-24 w-24 items-center justify-center rounded-full border-4 border-white bg-slate-100 text-2xl font-bold text-slate-500 shadow-lg dark:border-slate-900 dark:bg-slate-800 dark:text-slate-300 sm:h-28 sm:w-28 sm:text-3xl">
                                    {{ strtoupper(substr($name, 0, 1)) }}
                                </div>
                            @endif
                        </div>

                        <!-- User Info -->
                        <div class="text-center">
                            <h2 class="text-xl font-bold text-slate-800 dark:text-slate-100 sm:text-2xl">
                                {{ $name }}
                            </h2>
                            <p class="mt-1 break-all text-sm text-slate-500 dark:text-slate-400">
                                {{ $email }}
                            </p>
                        </div>

                        <!-- Mini Info -->
                        <div class="mt-5 grid grid-cols-2 gap-3">
                            <div class="rounded-2xl bg-slate-50 p-3 text-center transition-colors duration-300 dark:bg-slate-800 sm:p-4">
                                <p class="text-[10px] font-semibold uppercase tracking-wider text-slate-400 dark:text-slate-500 sm:text-[11px]">
                                    Status
                                </p>
                                <p class="mt-2 text-sm font-bold text-emerald-600 dark:text-emerald-400">
                                    Aktif
                                </p>
                            </div>

                            <div class="rounded-2xl bg-slate-50 p-3 text-center transition-colors duration-300 dark:bg-slate-800 sm:p-4">
                                <p class="text-[10px] font-semibold uppercase tracking-wider text-slate-400 dark:text-slate-500 sm:text-[11px]">
                                    Role
                                </p>
                                <p class="mt-2 line-clamp-2 text-sm font-bold text-slate-700 dark:text-slate-200">
                                    {{ $roleName ?? 'No Role' }}
                                </p>
                            </div>
                        </div>

                        <!-- QR Section -->
                        <div class="mt-4 rounded-3xl bg-gradient-to-br from-slate-50 to-slate-100 p-4 text-center ring-1 ring-slate-200 transition-colors duration-300 dark:from-slate-800 dark:to-slate-800/80 dark:ring-slate-700 sm:p-5">
                            <div class="mb-3 flex items-center justify-center gap-2">
                                <span class="h-2 w-2 rounded-full bg-emerald-500"></span>
                                <p class="text-[11px] font-semibold uppercase tracking-[0.2em] text-slate-500 dark:text-slate-400">
                                    QR Identitas
                                </p>
                            </div>

                            <div class="flex justify-center">
                                <div class="rounded-2xl bg-white p-3 shadow-sm ring-1 ring-slate-200 dark:bg-slate-900 dark:ring-slate-700 [&>svg]:h-32 [&>svg]:w-32 sm:p-4 sm:[&>svg]:h-40 sm:[&>svg]:w-40 lg:[&>svg]:h-44 lg:[&>svg]:w-44">
                                    {!! $qrCode !!}
                                </div>
                            </div>

                            <p class="mt-4 text-xs leading-5 text-slate-500 dark:text-slate-400">
                                Scan QR ini untuk identifikasi akun pengguna.
                            </p>
                        </div>

                        <!-- Footer -->
                        <div class="mt-4 rounded-2xl border border-dashed border-slate-200 px-4 py-3 text-center transition-colors duration-300 dark:border-slate-700">
                            <p class="text-xs leading-5 text-slate-500 dark:text-slate-400">
                                Pastikan data profil selalu terbarui agar identitas akun tetap valid.
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Main Content -->
            <div class="lg:col-span-2">
                <div class="overflow-hidden rounded-3xl bg-white shadow-sm ring-1 ring-slate-200 transition-colors duration-300 dark:bg-slate-900 dark:shadow-[0_20px_60px_rgba(0,0,0,0.25)] dark:ring-white/10">
                    <div class="relative p-4 sm:p-6 lg:p-8">
                        <!-- Top accent line -->
                        <div class="absolute inset-x-0 top-0 h-px bg-gradient-to-r from-transparent via-blue-500/20 to-transparent dark:via-blue-400/30"></div>

                        @if (session()->has('success'))
                            <div class="mb-5 rounded-2xl border border-emerald-500/20 bg-emerald-50 px-4 py-3 text-sm font-medium text-emerald-700 dark:bg-emerald-500/10 dark:text-emerald-300">
                                {{ session('success') }}
                            </div>
                        @endif

                        @if (session()->has('success_password'))
                            <div class="mb-5 rounded-2xl border border-emerald-500/20 bg-emerald-50 px-4 py-3 text-sm font-medium text-emerald-700 dark:bg-emerald-500/10 dark:text-emerald-300">
                                {{ session('success_password') }}
                            </div>
                        @endif

                        <!-- Informasi Profil -->
                        <section>
                            <div class="mb-6">
                                <h3 class="text-xl font-bold text-slate-800 dark:text-white sm:text-2xl">
                                    Informasi Profil
                                </h3>
                                <p class="mt-1 text-sm leading-6 text-slate-500 dark:text-slate-400">
                                    Perbarui data pribadi dan foto profil Anda.
                                </p>
                            </div>

                            <form wire:submit.prevent="updateProfile" class="space-y-5 sm:space-y-6">
                                <div class="grid grid-cols-1 gap-4 md:grid-cols-2 md:gap-5">
                                    <!-- Nama -->
                                    <div>
                                        <label for="name" class="mb-2 block text-sm font-semibold text-slate-700 dark:text-slate-200">
                                            <svg class="mr-2 inline h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                            </svg>
                                            Nama Lengkap
                                        </label>
                                        <input id="name" type="text" wire:model.defer="name"
                                            class="h-12 w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 text-sm text-slate-900 outline-none transition placeholder:text-slate-400
                                                   focus:border-blue-500 focus:bg-white focus:ring-4 focus:ring-blue-500/10
                                                   dark:border-white/10 dark:bg-slate-800/80 dark:text-white dark:placeholder:text-slate-500
                                                   dark:focus:border-blue-500/60 dark:focus:bg-slate-800 dark:focus:ring-blue-500/10"
                                            placeholder="Masukkan nama lengkap"
                                            aria-describedby="name-error">
                                        @error('name')
                                            <p id="name-error" class="mt-2 text-sm text-rose-500 dark:text-rose-400" role="alert">{{ $message }}</p>
                                        @enderror
                                    </div>

                                    <!-- Email -->
                                    <div>
                                        <label for="email" class="mb-2 block text-sm font-semibold text-slate-700 dark:text-slate-200">
                                            <svg class="mr-2 inline h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 4.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                                            </svg>
                                            Email
                                        </label>
                                        <input id="email" type="email" wire:model.defer="email"
                                            class="h-12 w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 text-sm text-slate-900 outline-none transition placeholder:text-slate-400
                                                   focus:border-blue-500 focus:bg-white focus:ring-4 focus:ring-blue-500/10
                                                   dark:border-white/10 dark:bg-slate-800/80 dark:text-white dark:placeholder:text-slate-500
                                                   dark:focus:border-blue-500/60 dark:focus:bg-slate-800 dark:focus:ring-blue-500/10"
                                            placeholder="Masukkan email"
                                            aria-describedby="email-error">
                                        @error('email')
                                            <p id="email-error" class="mt-2 text-sm text-rose-500 dark:text-rose-400" role="alert">{{ $message }}</p>
                                        @enderror
                                    </div>
                                </div>

                                <!-- Foto Profil -->
                                <div>
                                    <label for="foto_profil" class="mb-2 block text-sm font-semibold text-slate-700 dark:text-slate-200">
                                        <svg class="mr-2 inline h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                        </svg>
                                        Foto Profil
                                    </label>

                                    <div class="rounded-[24px] border border-dashed border-blue-300 bg-blue-50/50 p-4 ring-1 ring-blue-100 transition
                                                hover:border-blue-400 hover:bg-blue-50
                                                dark:border-blue-400/20 dark:bg-slate-800/50 dark:ring-white/5
                                                dark:hover:border-blue-400/30 dark:hover:bg-slate-800/70
                                                sm:rounded-[28px] sm:p-5">
                                        <input id="foto_profil" type="file" wire:model="foto_profil"
                                            class="block w-full text-sm text-slate-600 dark:text-slate-300
                                                   file:mr-3 file:mb-3 file:rounded-xl file:border-0
                                                   file:bg-gradient-to-r file:from-blue-600 file:to-indigo-600
                                                   file:px-4 file:py-2.5 file:text-sm file:font-semibold file:text-white
                                                   hover:file:from-blue-700 hover:file:to-indigo-700
                                                   sm:file:mr-4 sm:file:mb-0"
                                            aria-describedby="foto-error">

                                        <div wire:loading wire:target="foto_profil" class="mt-3 text-sm text-blue-500 dark:text-blue-400">
                                            <svg class="mr-2 inline h-4 w-4 animate-spin" fill="none" viewBox="0 0 24 24">
                                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                            </svg>
                                            Mengunggah foto...
                                        </div>

                                        <p class="mt-3 text-xs leading-5 text-slate-400 dark:text-slate-400">
                                            Format gambar: JPG, PNG, JPEG. Maksimal 1MB.
                                        </p>
                                    </div>

                                    @error('foto_profil')
                                        <p id="foto-error" class="mt-2 text-sm text-rose-500 dark:text-rose-400" role="alert">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div class="flex flex-col gap-3 sm:flex-row sm:justify-end">
                                    <button type="submit"
                                        class="inline-flex h-12 items-center justify-center rounded-2xl bg-gradient-to-r from-blue-600 to-indigo-600 px-6 text-sm font-semibold text-white shadow-[0_12px_30px_rgba(59,130,246,0.20)] transition hover:scale-[1.01] hover:from-blue-700 hover:to-indigo-700 disabled:opacity-50 disabled:cursor-not-allowed dark:shadow-[0_12px_30px_rgba(59,130,246,0.25)]"
                                        wire:loading.attr="disabled"
                                        wire:target="updateProfile">
                                        <svg wire:loading.remove wire:target="updateProfile" class="mr-2 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                        </svg>
                                        <svg wire:loading wire:target="updateProfile" class="mr-2 h-4 w-4 animate-spin" fill="none" viewBox="0 0 24 24">
                                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                        </svg>
                                        <span wire:loading.remove wire:target="updateProfile">Simpan Perubahan</span>
                                        <span wire:loading wire:target="updateProfile">Menyimpan...</span>
                                    </button>
                                </div>
                            </form>
                        </section>

                        <!-- Divider -->
                        <div class="my-8 h-px bg-gradient-to-r from-transparent via-slate-200 to-transparent dark:via-white/10 sm:my-10"></div>

                        <!-- Ubah Password -->
                        <section>
                            <div class="mb-6">
                                <h3 class="text-xl font-bold text-slate-800 dark:text-white sm:text-2xl">
                                    Ubah Password
                                </h3>
                                <p class="mt-1 text-sm leading-6 text-slate-500 dark:text-slate-400">
                                    Gunakan password yang kuat untuk menjaga keamanan akun Anda.
                                </p>
                            </div>

                            <form wire:submit.prevent="updatePassword" class="space-y-5 sm:space-y-6">
                                <!-- Password Saat Ini -->
                                <div>
                                    <label for="current_password" class="mb-2 block text-sm font-semibold text-slate-700 dark:text-slate-200">
                                        <svg class="mr-2 inline h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                                        </svg>
                                        Password Saat Ini
                                    </label>
                                    <input id="current_password" type="password" wire:model.defer="current_password"
                                        class="h-12 w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 text-sm text-slate-900 outline-none transition placeholder:text-slate-400
                                               focus:border-blue-500 focus:bg-white focus:ring-4 focus:ring-blue-500/10
                                               dark:border-white/10 dark:bg-slate-800/80 dark:text-white dark:placeholder:text-slate-500
                                               dark:focus:border-blue-500/60 dark:focus:bg-slate-800 dark:focus:ring-blue-500/10"
                                        placeholder="Masukkan password saat ini"
                                        aria-describedby="current-password-error">
                                    @error('current_password')
                                        <p id="current-password-error" class="mt-2 text-sm text-rose-500 dark:text-rose-400" role="alert">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div class="grid grid-cols-1 gap-4 md:grid-cols-2 md:gap-5">
                                    <!-- Password Baru -->
                                    <div>
                                        <label for="new_password" class="mb-2 block text-sm font-semibold text-slate-700 dark:text-slate-200">
                                            <svg class="mr-2 inline h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"></path>
                                            </svg>
                                            Password Baru
                                        </label>
                                        <input id="new_password" type="password" wire:model.defer="new_password"
                                            class="h-12 w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 text-sm text-slate-900 outline-none transition placeholder:text-slate-400
                                                   focus:border-blue-500 focus:bg-white focus:ring-4 focus:ring-blue-500/10
                                                   dark:border-white/10 dark:bg-slate-800/80 dark:text-white dark:placeholder:text-slate-500
                                                   dark:focus:border-blue-500/60 dark:focus:bg-slate-800 dark:focus:ring-blue-500/10"
                                            placeholder="Masukkan password baru"
                                            aria-describedby="new-password-error">
                                        @error('new_password')
                                            <p id="new-password-error" class="mt-2 text-sm text-rose-500 dark:text-rose-400" role="alert">{{ $message }}</p>
                                        @enderror
                                    </div>

                                    <!-- Konfirmasi Password -->
                                    <div>
                                        <label for="new_password_confirmation" class="mb-2 block text-sm font-semibold text-slate-700 dark:text-slate-200">
                                            <svg class="mr-2 inline h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                            </svg>
                                            Konfirmasi Password Baru
                                        </label>
                                        <input id="new_password_confirmation" type="password" wire:model.defer="new_password_confirmation"
                                            class="h-12 w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 text-sm text-slate-900 outline-none transition placeholder:text-slate-400
                                                   focus:border-blue-500 focus:bg-white focus:ring-4 focus:ring-blue-500/10
                                                   dark:border-white/10 dark:bg-slate-800/80 dark:text-white dark:placeholder:text-slate-500
                                                   dark:focus:border-blue-500/60 dark:focus:bg-slate-800 dark:focus:ring-blue-500/10"
                                            placeholder="Ulangi password baru">
                                    </div>
                                </div>

                                <div class="flex flex-col gap-3 sm:flex-row sm:justify-end">
                                    <button type="submit"
                                        class="inline-flex h-12 items-center justify-center rounded-2xl px-6 text-sm font-semibold transition hover:scale-[1.01]
                                               border border-slate-200 bg-slate-800 text-white shadow-sm hover:bg-slate-700 disabled:opacity-50 disabled:cursor-not-allowed
                                               dark:border-white/10 dark:bg-white dark:text-slate-900 dark:shadow-[0_10px_25px_rgba(255,255,255,0.12)] dark:hover:bg-slate-100"
                                        wire:loading.attr="disabled"
                                        wire:target="updatePassword">
                                        <svg wire:loading.remove wire:target="updatePassword" class="mr-2 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"></path>
                                        </svg>
                                        <svg wire:loading wire:target="updatePassword" class="mr-2 h-4 w-4 animate-spin" fill="none" viewBox="0 0 24 24">
                                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                        </svg>
                                        <span wire:loading.remove wire:target="updatePassword">Update Password</span>
                                        <span wire:loading wire:target="updatePassword">Mengupdate...</span>
                                    </button>
                                </div>
                            </form>
                        </section>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>