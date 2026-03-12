<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    {{-- Header --}}
    <div class="mb-8">
        <div class="flex items-start justify-between gap-4 flex-wrap">
            <div>
                <h1 class="text-3xl font-bold tracking-tight text-zinc-900 dark:text-zinc-100">
                    Profil Saya
                </h1>
                <p class="mt-2 text-sm text-zinc-600 dark:text-zinc-400">
                    Kelola informasi akun, foto profil, dan keamanan akun Anda.
                </p>
            </div>

            <div class="inline-flex items-center gap-2 rounded-full border border-emerald-500/20 bg-emerald-500/10 px-3 py-1.5 text-xs font-medium text-emerald-600 dark:text-emerald-400">
                <span class="h-2 w-2 rounded-full bg-emerald-500"></span>
                Akun Aktif
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 xl:grid-cols-12 gap-6">
        {{-- Left profile card --}}
        <div class="xl:col-span-4">
            <div class="relative overflow-hidden rounded-3xl border border-zinc-200 dark:border-zinc-800 bg-gradient-to-b from-white to-zinc-100 dark:from-zinc-950 dark:to-black shadow-xl">
                <div class="absolute inset-0 bg-[radial-gradient(circle_at_top,rgba(59,130,246,0.12),transparent_35%)] dark:bg-[radial-gradient(circle_at_top,rgba(59,130,246,0.18),transparent_35%)]"></div>

                <div class="relative p-8">
                    @php
                        $avatarUrl = !empty($user->foto_profil)
                            ? asset('storage/' . $user->foto_profil)
                            : 'https://ui-avatars.com/api/?name=' . urlencode($user->name ?? 'User') . '&background=111827&color=ffffff&size=256';
                    @endphp

                    <div class="flex flex-col items-center text-center">
                        <div class="relative">
                            @if ($foto_profil)
                                <img
                                    src="{{ $foto_profil->temporaryUrl() }}"
                                    alt="Preview Foto Profil"
                                    class="h-36 w-36 rounded-full object-cover border-4 border-white dark:border-zinc-900 ring-4 ring-blue-500/20 dark:ring-blue-500/30 shadow-[0_0_40px_rgba(59,130,246,0.18)]"
                                >
                            @else
                                <img
                                    src="{{ $avatarUrl }}"
                                    alt="Foto Profil"
                                    class="h-36 w-36 rounded-full object-cover border-4 border-white dark:border-zinc-900 ring-4 ring-blue-500/20 dark:ring-blue-500/30 shadow-[0_0_40px_rgba(59,130,246,0.18)]"
                                >
                            @endif

                            <div class="absolute bottom-2 right-2 h-4 w-4 rounded-full border-2 border-white dark:border-black bg-emerald-500"></div>
                        </div>

                        <h2 class="mt-5 text-2xl font-semibold text-zinc-900 dark:text-white">
                            {{ $name }}
                        </h2>

                        <p class="mt-1 text-sm text-zinc-600 dark:text-zinc-400">
                            {{ $email }}
                        </p>

                        <div class="mt-4 inline-flex items-center gap-2 rounded-full border border-blue-500/20 bg-blue-500/10 px-3 py-1.5 text-xs font-medium text-blue-700 dark:text-blue-300">
                            <span class="h-2 w-2 rounded-full bg-blue-500"></span>
                            ROB Monitoring User
                        </div>
                    </div>

                    <div class="mt-8 grid grid-cols-3 gap-3">
                        <div class="rounded-2xl border border-zinc-200 dark:border-zinc-800 bg-white/80 dark:bg-zinc-900/60 p-4 text-center">
                            <div class="text-xs text-zinc-500 dark:text-zinc-500">Status</div>
                            <div class="mt-1 text-sm font-semibold text-emerald-600 dark:text-emerald-400">Online</div>
                        </div>

                        <div class="rounded-2xl border border-zinc-200 dark:border-zinc-800 bg-white/80 dark:bg-zinc-900/60 p-4 text-center">
                            <div class="text-xs text-zinc-500 dark:text-zinc-500">Role</div>
                            <div class="mt-1 text-sm font-semibold text-zinc-900 dark:text-white">User</div>
                        </div>

                        <div class="rounded-2xl border border-zinc-200 dark:border-zinc-800 bg-white/80 dark:bg-zinc-900/60 p-4 text-center">
                            <div class="text-xs text-zinc-500 dark:text-zinc-500">Keamanan</div>
                            <div class="mt-1 text-sm font-semibold text-blue-600 dark:text-blue-400">Aktif</div>
                        </div>
                    </div>

                    <div class="mt-6 rounded-2xl border border-zinc-200 dark:border-zinc-800 bg-white/70 dark:bg-zinc-900/50 p-4">
                        <div class="text-xs uppercase tracking-[0.2em] text-zinc-500 dark:text-zinc-500">
                            Ringkasan
                        </div>
                        <p class="mt-2 text-sm leading-6 text-zinc-700 dark:text-zinc-300">
                            Pastikan informasi profil dan password selalu diperbarui agar akun ROB Monitoring tetap aman dan mudah dikenali.
                        </p>
                    </div>
                </div>
            </div>
        </div>

        {{-- Right area --}}
        <div class="xl:col-span-8 grid grid-cols-1 2xl:grid-cols-2 gap-6">
            {{-- Informasi Profil --}}
            <div class="rounded-3xl border border-zinc-200 dark:border-zinc-800 bg-white/90 dark:bg-black/80 shadow-xl backdrop-blur">
                <div class="border-b border-zinc-200 dark:border-zinc-800 px-6 py-5">
                    <h3 class="text-xl font-semibold text-zinc-900 dark:text-white">
                        Informasi Profil
                    </h3>
                    <p class="mt-1 text-sm text-zinc-600 dark:text-zinc-400">
                        Perbarui nama, email, dan foto profil Anda.
                    </p>
                </div>

                <div class="p-6">
                    @if (session()->has('success'))
                        <div class="mb-5 rounded-2xl border border-emerald-500/20 bg-emerald-500/10 px-4 py-3 text-sm text-emerald-700 dark:text-emerald-300">
                            {{ session('success') }}
                        </div>
                    @endif

                    <form wire:submit="updateProfile" class="space-y-5">
                        <div>
                            <label class="mb-2 block text-sm font-medium text-zinc-800 dark:text-zinc-200">
                                Foto Profil
                            </label>

                            <div class="rounded-2xl border border-dashed border-zinc-300 dark:border-zinc-700 bg-zinc-50 dark:bg-zinc-900/60 p-4">
                                <input
                                    type="file"
                                    wire:model="foto_profil"
                                    class="block w-full text-sm text-zinc-700 dark:text-zinc-300 file:mr-4 file:rounded-xl file:border-0 file:bg-blue-600 file:px-4 file:py-2 file:text-sm file:font-medium file:text-white hover:file:bg-blue-700"
                                >

                                <div wire:loading wire:target="foto_profil" class="mt-3 text-xs text-blue-600 dark:text-blue-400">
                                    Mengunggah foto...
                                </div>
                            </div>

                            @error('foto_profil')
                                <p class="mt-2 text-sm text-red-500 dark:text-red-400">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="mb-2 block text-sm font-medium text-zinc-800 dark:text-zinc-200">
                                Nama
                            </label>
                            <input
                                type="text"
                                wire:model.defer="name"
                                class="w-full rounded-2xl border border-zinc-300 dark:border-zinc-800 bg-white dark:bg-zinc-950 px-4 py-3 text-zinc-900 dark:text-zinc-100 outline-none transition focus:border-blue-500/50 focus:ring-4 focus:ring-blue-500/10"
                                placeholder="Masukkan nama"
                            >
                            @error('name')
                                <p class="mt-2 text-sm text-red-500 dark:text-red-400">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="mb-2 block text-sm font-medium text-zinc-800 dark:text-zinc-200">
                                Email
                            </label>
                            <input
                                type="email"
                                wire:model.defer="email"
                                class="w-full rounded-2xl border border-zinc-300 dark:border-zinc-800 bg-white dark:bg-zinc-950 px-4 py-3 text-zinc-900 dark:text-zinc-100 outline-none transition focus:border-blue-500/50 focus:ring-4 focus:ring-blue-500/10"
                                placeholder="Masukkan email"
                            >
                            @error('email')
                                <p class="mt-2 text-sm text-red-500 dark:text-red-400">{{ $message }}</p>
                            @enderror
                        </div>

                        <button
                            type="submit"
                            class="inline-flex w-full items-center justify-center rounded-2xl bg-zinc-900 dark:bg-white px-4 py-3 text-sm font-semibold text-white dark:text-black transition hover:bg-zinc-800 dark:hover:bg-zinc-200"
                        >
                            Simpan Profil
                        </button>
                    </form>
                </div>
            </div>

            {{-- Keamanan --}}
            <div class="rounded-3xl border border-zinc-200 dark:border-zinc-800 bg-white/90 dark:bg-black/80 shadow-xl backdrop-blur">
                <div class="border-b border-zinc-200 dark:border-zinc-800 px-6 py-5">
                    <h3 class="text-xl font-semibold text-zinc-900 dark:text-white">
                        Ubah Password
                    </h3>
                    <p class="mt-1 text-sm text-zinc-600 dark:text-zinc-400">
                        Gunakan password yang kuat untuk menjaga keamanan akun.
                    </p>
                </div>

                <div class="p-6">
                    @if (session()->has('success_password'))
                        <div class="mb-5 rounded-2xl border border-emerald-500/20 bg-emerald-500/10 px-4 py-3 text-sm text-emerald-700 dark:text-emerald-300">
                            {{ session('success_password') }}
                        </div>
                    @endif

                    <form wire:submit="updatePassword" class="space-y-5">
                        <div>
                            <label class="mb-2 block text-sm font-medium text-zinc-800 dark:text-zinc-200">
                                Password Saat Ini
                            </label>
                            <input
                                type="password"
                                wire:model.defer="current_password"
                                class="w-full rounded-2xl border border-zinc-300 dark:border-zinc-800 bg-white dark:bg-zinc-950 px-4 py-3 text-zinc-900 dark:text-zinc-100 outline-none transition focus:border-blue-500/50 focus:ring-4 focus:ring-blue-500/10"
                                placeholder="Masukkan password saat ini"
                            >
                            @error('current_password')
                                <p class="mt-2 text-sm text-red-500 dark:text-red-400">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="mb-2 block text-sm font-medium text-zinc-800 dark:text-zinc-200">
                                    Password Baru
                                </label>
                                <input
                                    type="password"
                                    wire:model.defer="new_password"
                                    class="w-full rounded-2xl border border-zinc-300 dark:border-zinc-800 bg-white dark:bg-zinc-950 px-4 py-3 text-zinc-900 dark:text-zinc-100 outline-none transition focus:border-blue-500/50 focus:ring-4 focus:ring-blue-500/10"
                                    placeholder="Password baru"
                                >
                                @error('new_password')
                                    <p class="mt-2 text-sm text-red-500 dark:text-red-400">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label class="mb-2 block text-sm font-medium text-zinc-800 dark:text-zinc-200">
                                    Konfirmasi Password
                                </label>
                                <input
                                    type="password"
                                    wire:model.defer="new_password_confirmation"
                                    class="w-full rounded-2xl border border-zinc-300 dark:border-zinc-800 bg-white dark:bg-zinc-950 px-4 py-3 text-zinc-900 dark:text-zinc-100 outline-none transition focus:border-blue-500/50 focus:ring-4 focus:ring-blue-500/10"
                                    placeholder="Ulangi password baru"
                                >
                            </div>
                        </div>

                        <div class="rounded-2xl border border-amber-500/20 bg-amber-500/10 px-4 py-3 text-xs leading-5 text-amber-700 dark:text-amber-300">
                            Gunakan kombinasi huruf besar, huruf kecil, angka, dan simbol agar password lebih aman.
                        </div>

                        <button
                            type="submit"
                            class="inline-flex w-full items-center justify-center rounded-2xl bg-blue-600 px-4 py-3 text-sm font-semibold text-white transition hover:bg-blue-700"
                        >
                            Update Password
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>