<div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="mb-8">
        <h1 class="text-3xl font-semibold text-zinc-900 dark:text-white">Profile</h1>
        <p class="mt-2 text-sm text-zinc-600 dark:text-zinc-400">
            Kelola informasi akun Anda.
        </p>
    </div>

    @php
        $avatarUrl = !empty($storedFotoProfil)
            ? asset('storage/' . $storedFotoProfil)
            : null;
    @endphp

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
        {{-- Left summary --}}
        <aside class="lg:col-span-4">
            <div class="rounded-2xl border border-zinc-200 bg-white p-6 shadow-sm dark:border-zinc-800 dark:bg-zinc-950">
                <div class="flex flex-col items-center text-center">
                    @if ($foto_profil)
                        <img
                            src="{{ $foto_profil->temporaryUrl() }}"
                            alt="Preview Foto Profil"
                            class="h-24 w-24 rounded-full object-cover border border-zinc-200 dark:border-zinc-700"
                        >
                    @elseif ($avatarUrl)
                        <img
                            src="{{ $avatarUrl }}"
                            alt="Foto Profil"
                            class="h-24 w-24 rounded-full object-cover border border-zinc-200 dark:border-zinc-700"
                            loading="lazy"
                        >
                    @else
                        <div class="flex h-24 w-24 items-center justify-center rounded-full bg-blue-600 text-2xl font-semibold text-white">
                            {{ strtoupper(substr($name ?: 'U', 0, 1)) }}
                        </div>
                    @endif

                    <h2 class="mt-4 text-xl font-semibold text-zinc-900 dark:text-white">
                        {{ $name }}
                    </h2>

                    <p class="mt-1 text-sm text-zinc-500 dark:text-zinc-400">
                        {{ $email }}
                    </p>

                    <div class="mt-4 inline-flex rounded-full bg-blue-50 px-3 py-1 text-xs font-medium text-blue-700 dark:bg-blue-500/10 dark:text-blue-300">
                        {{ ucfirst($roleName ?? 'No Role') }}
                    </div>
                </div>

                <div class="mt-6 border-t border-zinc-200 pt-6 dark:border-zinc-800">
                    <div class="text-xs uppercase tracking-wide text-zinc-500 dark:text-zinc-400">
                        Ringkasan
                    </div>
                    <p class="mt-2 text-sm leading-6 text-zinc-700 dark:text-zinc-300">
                        Pastikan nama, email, dan foto profil Anda selalu terbarui agar akun mudah dikenali.
                    </p>
                </div>
            </div>
        </aside>

        {{-- Right form --}}
        <section class="lg:col-span-8">
            <div class="rounded-2xl border border-zinc-200 bg-white shadow-sm dark:border-zinc-800 dark:bg-zinc-950">
                <div class="border-b border-zinc-200 px-6 py-5 dark:border-zinc-800">
                    <h3 class="text-xl font-semibold text-zinc-900 dark:text-white">
                        Informasi Profil
                    </h3>
                    <p class="mt-1 text-sm text-zinc-600 dark:text-zinc-400">
                        Perbarui nama, email, dan foto profil.
                    </p>
                </div>

                <div class="p-6">
                    @if (session()->has('success'))
                        <div class="mb-5 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-700 dark:border-emerald-500/20 dark:bg-emerald-500/10 dark:text-emerald-300">
                            {{ session('success') }}
                        </div>
                    @endif

                    <form wire:submit="updateProfile" class="space-y-6">
                        <div>
                            <label class="mb-2 block text-sm font-medium text-zinc-800 dark:text-zinc-200">
                                Foto Profil
                            </label>

                            <div class="rounded-xl border border-dashed border-zinc-300 bg-zinc-50 p-4 dark:border-zinc-700 dark:bg-zinc-900">
                                <input
                                    type="file"
                                    wire:model="foto_profil"
                                    class="block w-full text-sm text-zinc-700 file:mr-4 file:rounded-lg file:border-0 file:bg-blue-600 file:px-4 file:py-2 file:text-sm file:font-medium file:text-white hover:file:bg-blue-700 dark:text-zinc-300"
                                >
                            </div>

                            <div wire:loading wire:target="foto_profil" class="mt-2 text-xs text-blue-600 dark:text-blue-400">
                                Mengunggah foto...
                            </div>

                            @error('foto_profil')
                                <p class="mt-2 text-sm text-red-500 dark:text-red-400">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                            <div>
                                <label class="mb-2 block text-sm font-medium text-zinc-800 dark:text-zinc-200">
                                    Nama
                                </label>
                                <input
                                    type="text"
                                    wire:model.defer="name"
                                    class="w-full rounded-xl border border-zinc-300 bg-white px-4 py-3 text-zinc-900 outline-none transition focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 dark:border-zinc-700 dark:bg-zinc-900 dark:text-zinc-100"
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
                                    class="w-full rounded-xl border border-zinc-300 bg-white px-4 py-3 text-zinc-900 outline-none transition focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 dark:border-zinc-700 dark:bg-zinc-900 dark:text-zinc-100"
                                    placeholder="Masukkan email"
                                >
                                @error('email')
                                    <p class="mt-2 text-sm text-red-500 dark:text-red-400">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <div class="flex justify-end">
                            <button
                                type="submit"
                                class="inline-flex items-center rounded-xl bg-zinc-900 px-5 py-3 text-sm font-semibold text-white transition hover:bg-zinc-800 dark:bg-white dark:text-black dark:hover:bg-zinc-200"
                            >
                                Simpan Profil
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            {{-- Password tetap ada, tapi tidak kita fokuskan dulu --}}
            <div class="mt-6 rounded-2xl border border-zinc-200 bg-white shadow-sm dark:border-zinc-800 dark:bg-zinc-950">
                <div class="border-b border-zinc-200 px-6 py-5 dark:border-zinc-800">
                    <h3 class="text-xl font-semibold text-zinc-900 dark:text-white">
                        Password
                    </h3>
                    <p class="mt-1 text-sm text-zinc-600 dark:text-zinc-400">
                        Ubah password akun Anda.
                    </p>
                </div>

                <div class="p-6">
                    @if (session()->has('success_password'))
                        <div class="mb-5 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-700 dark:border-emerald-500/20 dark:bg-emerald-500/10 dark:text-emerald-300">
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
                                class="w-full rounded-xl border border-zinc-300 bg-white px-4 py-3 text-zinc-900 outline-none transition focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 dark:border-zinc-700 dark:bg-zinc-900 dark:text-zinc-100"
                            >
                            @error('current_password')
                                <p class="mt-2 text-sm text-red-500 dark:text-red-400">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                            <div>
                                <label class="mb-2 block text-sm font-medium text-zinc-800 dark:text-zinc-200">
                                    Password Baru
                                </label>
                                <input
                                    type="password"
                                    wire:model.defer="new_password"
                                    class="w-full rounded-xl border border-zinc-300 bg-white px-4 py-3 text-zinc-900 outline-none transition focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 dark:border-zinc-700 dark:bg-zinc-900 dark:text-zinc-100"
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
                                    class="w-full rounded-xl border border-zinc-300 bg-white px-4 py-3 text-zinc-900 outline-none transition focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 dark:border-zinc-700 dark:bg-zinc-900 dark:text-zinc-100"
                                >
                            </div>
                        </div>

                        <div class="flex justify-end">
                            <button
                                type="submit"
                                class="inline-flex items-center rounded-xl bg-blue-600 px-5 py-3 text-sm font-semibold text-white transition hover:bg-blue-700"
                            >
                                Update Password
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </section>
    </div>
</div>