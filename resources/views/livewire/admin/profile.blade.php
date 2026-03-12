<div class="max-w-6xl mx-auto px-4 py-8">
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-zinc-900 dark:text-white">Profil Saya</h1>
        <p class="text-sm text-zinc-500 dark:text-zinc-400">
            Kelola informasi akun dan foto profil.
        </p>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        {{-- FOTO PROFIL --}}
        <div class="lg:col-span-1">
            <div class="bg-white dark:bg-zinc-950 border border-zinc-200 dark:border-zinc-800 rounded-2xl p-6 shadow-sm">
                <div class="flex flex-col items-center text-center">

                    @php
                        $avatarUrl = !empty($user->foto_profil)
                            ? asset('storage/' . $user->foto_profil)
                            : 'https://ui-avatars.com/api/?name=' . urlencode($user->name ?? 'User');
                    @endphp

                    @if ($foto_profil)
                        <img
                            src="{{ $foto_profil->temporaryUrl() }}"
                            class="w-28 h-28 rounded-full object-cover border border-zinc-200 dark:border-zinc-700"
                        >
                    @else
                        <img
                            src="{{ $avatarUrl }}"
                            class="w-28 h-28 rounded-full object-cover border border-zinc-200 dark:border-zinc-700"
                        >
                    @endif

                    <h2 class="mt-4 text-lg font-semibold text-zinc-900 dark:text-white">
                        {{ $name }}
                    </h2>

                    <p class="text-sm text-zinc-500 dark:text-zinc-400">
                        {{ $email }}
                    </p>

                </div>
            </div>
        </div>


        {{-- AREA FORM --}}
        <div class="lg:col-span-2 grid grid-cols-1 md:grid-cols-2 gap-6">

            {{-- INFORMASI PROFIL --}}
            <div class="bg-white dark:bg-zinc-950 border border-zinc-200 dark:border-zinc-800 rounded-2xl p-6 shadow-sm">

                <div class="mb-4">
                    <h3 class="text-lg font-semibold text-zinc-900 dark:text-white">
                        Informasi Profil
                    </h3>
                </div>

                @if (session()->has('success'))
                    <div class="mb-4 rounded-xl bg-emerald-50 border border-emerald-200 px-4 py-3 text-sm text-emerald-700">
                        {{ session('success') }}
                    </div>
                @endif

                <form wire:submit="updateProfile" class="space-y-4">

                    <div>
                        <label class="text-sm font-medium text-zinc-700 dark:text-zinc-200">
                            Foto Profil
                        </label>

                        <input
                            type="file"
                            wire:model="foto_profil"
                            class="mt-1 w-full text-sm border border-zinc-200 dark:border-zinc-800 rounded-xl px-3 py-2
                                   bg-white dark:bg-zinc-900 text-zinc-900 dark:text-white"
                        >

                        @error('foto_profil')
                            <p class="text-sm text-red-500 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="text-sm font-medium text-zinc-700 dark:text-zinc-200">
                            Nama
                        </label>

                        <input
                            type="text"
                            wire:model.defer="name"
                            class="mt-1 w-full border border-zinc-200 dark:border-zinc-800 rounded-xl px-3 py-2.5
                                   bg-white dark:bg-zinc-900 text-zinc-900 dark:text-white"
                        >

                        @error('name')
                            <p class="text-sm text-red-500 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="text-sm font-medium text-zinc-700 dark:text-zinc-200">
                            Email
                        </label>

                        <input
                            type="email"
                            wire:model.defer="email"
                            class="mt-1 w-full border border-zinc-200 dark:border-zinc-800 rounded-xl px-3 py-2.5
                                   bg-white dark:bg-zinc-900 text-zinc-900 dark:text-white"
                        >

                        @error('email')
                            <p class="text-sm text-red-500 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <button
                        type="submit"
                        class="w-full mt-2 rounded-xl bg-zinc-900 text-white py-2.5 text-sm font-medium hover:bg-zinc-800"
                    >
                        Simpan Profil
                    </button>

                </form>
            </div>


            {{-- UBAH PASSWORD --}}
            <div class="bg-white dark:bg-zinc-950 border border-zinc-200 dark:border-zinc-800 rounded-2xl p-6 shadow-sm">

                <div class="mb-4">
                    <h3 class="text-lg font-semibold text-zinc-900 dark:text-white">
                        Ubah Password
                    </h3>
                </div>

                @if (session()->has('success_password'))
                    <div class="mb-4 rounded-xl bg-emerald-50 border border-emerald-200 px-4 py-3 text-sm text-emerald-700">
                        {{ session('success_password') }}
                    </div>
                @endif

                <form wire:submit="updatePassword" class="space-y-4">

                    <div>
                        <label class="text-sm font-medium text-zinc-700 dark:text-zinc-200">
                            Password Saat Ini
                        </label>

                        <input
                            type="password"
                            wire:model.defer="current_password"
                            class="mt-1 w-full border border-zinc-200 dark:border-zinc-800 rounded-xl px-3 py-2.5
                                   bg-white dark:bg-zinc-900 text-zinc-900 dark:text-white"
                        >

                        @error('current_password')
                            <p class="text-sm text-red-500 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="text-sm font-medium text-zinc-700 dark:text-zinc-200">
                            Password Baru
                        </label>

                        <input
                            type="password"
                            wire:model.defer="new_password"
                            class="mt-1 w-full border border-zinc-200 dark:border-zinc-800 rounded-xl px-3 py-2.5
                                   bg-white dark:bg-zinc-900 text-zinc-900 dark:text-white"
                        >

                        @error('new_password')
                            <p class="text-sm text-red-500 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="text-sm font-medium text-zinc-700 dark:text-zinc-200">
                            Konfirmasi Password
                        </label>

                        <input
                            type="password"
                            wire:model.defer="new_password_confirmation"
                            class="mt-1 w-full border border-zinc-200 dark:border-zinc-800 rounded-xl px-3 py-2.5
                                   bg-white dark:bg-zinc-900 text-zinc-900 dark:text-white"
                        >
                    </div>

                    <button
                        type="submit"
                        class="w-full mt-2 rounded-xl bg-blue-600 text-white py-2.5 text-sm font-medium hover:bg-blue-700"
                    >
                        Update Password
                    </button>

                </form>
            </div>

        </div>
    </div>
</div>