<div class="min-h-screen bg-slate-50 p-4 dark:bg-slate-950 sm:p-6 lg:p-8">
    <div class="mx-auto max-w-4xl">

        <!-- Header -->
        <div class="mb-6">
            <h1 class="text-2xl font-bold text-slate-800 dark:text-white sm:text-3xl">
                Profil Saya
            </h1>
            <p class="mt-2 text-sm text-slate-500 dark:text-slate-400">
                Kelola informasi akun, foto profil, dan password Anda.
            </p>
        </div>

        <!-- SINGLE CARD -->
        <div class="overflow-hidden rounded-3xl bg-white shadow-sm ring-1 ring-slate-200 dark:bg-slate-900 dark:ring-white/10">

            <!-- ===== PROFILE HEADER ===== -->
            <div class="relative">

                <!-- Banner -->
                <div class="h-28 bg-gradient-to-br from-blue-600 via-indigo-500 to-cyan-400"></div>

                <!-- Avatar -->
                <div class="-mt-12 flex justify-center">
                    @if ($foto_profil)
                        <img src="{{ $foto_profil->temporaryUrl() }}"
                            class="h-24 w-24 rounded-full border-4 border-white object-cover shadow-lg dark:border-slate-900">
                    @elseif ($storedFotoProfil)
                        <img src="{{ asset('storage/' . $storedFotoProfil) }}"
                            class="h-24 w-24 rounded-full border-4 border-white object-cover shadow-lg dark:border-slate-900">
                    @else
                        <div class="flex h-24 w-24 items-center justify-center rounded-full border-4 border-white bg-slate-100 text-2xl font-bold text-slate-500 shadow-lg dark:border-slate-900 dark:bg-slate-800">
                            {{ strtoupper(substr($name, 0, 1)) }}
                        </div>
                    @endif
                </div>

                <!-- User Info -->
                <div class="mt-3 text-center px-4">
                    <h2 class="text-xl font-bold text-slate-800 dark:text-white">
                        {{ $name }}
                    </h2>
                    <p class="text-sm text-slate-500 dark:text-slate-400 break-all">
                        {{ $email }}
                    </p>
                </div>

                <!-- Mini Info -->
                <div class="mt-5 grid grid-cols-2 gap-3 px-4 pb-6">
                    <div class="rounded-2xl bg-slate-50 p-3 text-center dark:bg-slate-800">
                        <p class="text-xs text-slate-400">Status</p>
                        <p class="mt-1 font-bold text-emerald-600">Aktif</p>
                    </div>
                    <div class="rounded-2xl bg-slate-50 p-3 text-center dark:bg-slate-800">
                        <p class="text-xs text-slate-400">Role</p>
                        <p class="mt-1 font-bold text-slate-700 dark:text-slate-200">
                            {{ $roleName ?? 'No Role' }}
                        </p>
                    </div>
                </div>
            </div>

            <!-- Divider -->
            <div class="border-t border-slate-200 dark:border-slate-800"></div>

            <!-- ===== FORM SECTION ===== -->
            <div class="p-4 sm:p-6 lg:p-8">

                @if (session()->has('success'))
                    <div class="mb-5 rounded-2xl bg-emerald-50 px-4 py-3 text-sm text-emerald-700 dark:bg-emerald-500/10 dark:text-emerald-300">
                        {{ session('success') }}
                    </div>
                @endif

                <!-- FORM PROFILE -->
                <form wire:submit.prevent="updateProfile" class="space-y-5">

                    <div class="grid grid-cols-1 gap-4 md:grid-cols-2">

                        <!-- Nama -->
                        <div>
                            <label class="mb-2 block text-sm font-semibold">Nama</label>
                            <input type="text" wire:model.defer="name"
                                class="h-12 w-full rounded-2xl border px-4 text-sm dark:bg-slate-800">
                        </div>

                        <!-- Email -->
                        <div>
                            <label class="mb-2 block text-sm font-semibold">Email</label>
                            <input type="email" wire:model.defer="email"
                                class="h-12 w-full rounded-2xl border px-4 text-sm dark:bg-slate-800">
                        </div>

                    </div>

                    <!-- Foto -->
                    <div>
                        <label class="mb-2 block text-sm font-semibold">Foto Profil</label>
                        <input type="file" wire:model="foto_profil"
                            class="block w-full text-sm">
                    </div>

                    <button type="submit"
                        class="h-12 w-full rounded-2xl bg-blue-600 text-white font-semibold hover:bg-blue-700">
                        Simpan Perubahan
                    </button>
                </form>

                <!-- ===== PASSWORD ===== -->
                <div class="mt-10">
                    <h4 class="text-lg font-semibold mb-4">Ubah Password</h4>

                    <form wire:submit.prevent="updatePassword" class="space-y-5">

                        <input type="password" wire:model.defer="current_password"
                            placeholder="Password saat ini"
                            class="h-12 w-full rounded-2xl border px-4 dark:bg-slate-800">

                        <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                            <input type="password" wire:model.defer="new_password"
                                placeholder="Password baru"
                                class="h-12 w-full rounded-2xl border px-4 dark:bg-slate-800">

                            <input type="password" wire:model.defer="new_password_confirmation"
                                placeholder="Konfirmasi password"
                                class="h-12 w-full rounded-2xl border px-4 dark:bg-slate-800">
                        </div>

                        <button type="submit"
                            class="h-12 w-full rounded-2xl bg-slate-800 text-white hover:bg-slate-700 dark:bg-white dark:text-slate-900">
                            Update Password
                        </button>
                    </form>
                </div>

                <!-- ===== QR (OPTIONAL DI BAWAH) ===== -->
                <div class="mt-10 text-center">
                    <p class="text-xs text-slate-400 mb-3">QR Identitas</p>
                    <div class="flex justify-center">
                        <div class="bg-white p-3 rounded-xl">
                            {!! $qrCode !!}
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>