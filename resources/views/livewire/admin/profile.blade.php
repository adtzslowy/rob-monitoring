<div class="min-h-screen bg-slate-50 p-4 transition-colors duration-300 dark:bg-slate-950 md:p-8">
    <div class="mx-auto max-w-6xl">
        <div class="mb-8">
            <h1 class="text-3xl font-bold tracking-tight text-slate-800 dark:text-slate-100">
                Profil Saya
            </h1>
            <p class="mt-2 text-sm text-slate-500 dark:text-slate-400">
                Kelola informasi akun, foto profil, dan password Anda.
            </p>
        </div>

        <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
            <!-- Card kiri -->
            <!-- Card kiri -->
            <div class="lg:col-span-1">
                <div
                    class="overflow-hidden rounded-3xl bg-white shadow-md ring-1 ring-slate-200 transition-colors duration-300 dark:bg-slate-900 dark:ring-slate-800">
                    <!-- Header -->
                    <div
                        class="relative h-36 overflow-hidden bg-gradient-to-br from-blue-600 via-indigo-500 to-cyan-400">
                        <div
                            class="absolute inset-0 bg-[radial-gradient(circle_at_top_right,rgba(255,255,255,0.25),transparent_35%)]">
                        </div>
                        <div class="absolute -right-8 -top-8 h-28 w-28 rounded-full bg-white/10"></div>
                        <div class="absolute -left-10 bottom-0 h-24 w-24 rounded-full bg-black/10"></div>

                        <div class="absolute left-5 top-5">
                            <span
                                class="inline-flex items-center rounded-full bg-white/20 px-3 py-1 text-xs font-semibold text-white backdrop-blur-sm">
                                My Profile
                            </span>
                        </div>
                    </div>

                    <div class="relative px-6 pb-6">
                        <!-- Avatar -->
                        <div class="-mt-14 mb-4 flex justify-center">
                            @if ($foto_profil)
                                <img src="{{ $foto_profil->temporaryUrl() }}" alt="Preview Foto Profil"
                                    class="h-28 w-28 rounded-full border-4 border-white object-cover shadow-xl dark:border-slate-900">
                            @elseif ($storedFotoProfil)
                                <img src="{{ asset('storage/' . $storedFotoProfil) }}" alt="Foto Profil"
                                    class="h-28 w-28 rounded-full border-4 border-white object-cover shadow-xl dark:border-slate-900">
                            @else
                                <div
                                    class="flex h-28 w-28 items-center justify-center rounded-full border-4 border-white bg-slate-100 text-3xl font-bold text-slate-500 shadow-xl dark:border-slate-900 dark:bg-slate-800 dark:text-slate-300">
                                    {{ strtoupper(substr($name, 0, 1)) }}
                                </div>
                            @endif
                        </div>

                        <!-- User Info -->
                        <div class="text-center">
                            <h2 class="text-2xl font-bold text-slate-800 dark:text-slate-100">
                                {{ $name }}
                            </h2>

                            <p class="mt-1 text-sm text-slate-500 dark:text-slate-400 break-all">
                                {{ $email }}
                            </p>

                            <span
                                class="mt-4 inline-flex rounded-full bg-blue-50 px-4 py-1.5 text-sm font-semibold text-blue-700 ring-1 ring-blue-100 dark:bg-blue-500/10 dark:text-blue-300 dark:ring-blue-400/20">
                                {{ $roleName ?? 'No Role' }}
                            </span>
                        </div>

                        <!-- Mini Info -->
                        <div class="mt-6 grid grid-cols-2 gap-3">
                            <div
                                class="rounded-2xl bg-slate-50 p-4 text-center transition-colors duration-300 dark:bg-slate-800">
                                <p
                                    class="text-[11px] font-semibold uppercase tracking-wider text-slate-400 dark:text-slate-500">
                                    Status
                                </p>
                                <p class="mt-2 text-sm font-bold text-emerald-600 dark:text-emerald-400">
                                    Aktif
                                </p>
                            </div>

                            <div
                                class="rounded-2xl bg-slate-50 p-4 text-center transition-colors duration-300 dark:bg-slate-800">
                                <p
                                    class="text-[11px] font-semibold uppercase tracking-wider text-slate-400 dark:text-slate-500">
                                    Role
                                </p>
                                <p class="mt-2 text-sm font-bold text-slate-700 dark:text-slate-200">
                                    {{ $roleName ?? 'No Role' }}
                                </p>
                            </div>
                        </div>

                        <!-- QR Section -->
                        <div
                            class="mt-4 rounded-3xl bg-gradient-to-br from-slate-50 to-slate-100 p-5 text-center ring-1 ring-slate-200 transition-colors duration-300 dark:from-slate-800 dark:to-slate-800/80 dark:ring-slate-700">
                            <div class="mb-3 flex items-center justify-center gap-2">
                                <span class="h-2 w-2 rounded-full bg-emerald-500"></span>
                                <p
                                    class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-500 dark:text-slate-400">
                                    QR Identitas
                                </p>
                            </div>

                            <div class="flex justify-center">
                                <div
                                    class="rounded-2xl bg-white p-4 shadow-sm ring-1 ring-slate-200 dark:bg-slate-900 dark:ring-slate-700">
                                    {!! $qrCode !!}
                                </div>
                            </div>

                            <p class="mt-4 text-xs leading-5 text-slate-500 dark:text-slate-400">
                                Scan QR ini untuk identifikasi akun pengguna.
                            </p>
                        </div>

                        <!-- Footer Info -->
                        <div
                            class="mt-4 rounded-2xl border border-dashed border-slate-200 px-4 py-3 text-center transition-colors duration-300 dark:border-slate-700">
                            <p class="text-xs text-slate-500 dark:text-slate-400">
                                Pastikan data profil selalu terbarui agar identitas akun tetap valid.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Card kanan -->
        <div class="lg:col-span-2">
            <div
                class="rounded-3xl bg-white p-6 shadow-md ring-1 ring-slate-200 transition-colors duration-300 dark:bg-slate-900 dark:ring-slate-800 md:p-8">
                @if (session()->has('success'))
                    <div
                        class="mb-5 rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-medium text-emerald-700 dark:border-emerald-500/20 dark:bg-emerald-500/10 dark:text-emerald-300">
                        {{ session('success') }}
                    </div>
                @endif

                @if (session()->has('success_password'))
                    <div
                        class="mb-5 rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-medium text-emerald-700 dark:border-emerald-500/20 dark:bg-emerald-500/10 dark:text-emerald-300">
                        {{ session('success_password') }}
                    </div>
                @endif

                <div>
                    <h3 class="text-2xl font-bold text-slate-800 dark:text-slate-100">
                        Informasi Profil
                    </h3>
                    <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">
                        Perbarui data pribadi dan foto profil Anda.
                    </p>

                    <form wire:submit.prevent="updateProfile" class="mt-6 space-y-5">
                        <div class="grid grid-cols-1 gap-5 md:grid-cols-2">
                            <div>
                                <label class="mb-2 block text-sm font-semibold text-slate-700 dark:text-slate-200">
                                    Nama Lengkap
                                </label>
                                <input type="text" wire:model.defer="name"
                                    class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm text-slate-800 outline-none transition focus:border-blue-500 focus:ring-4 focus:ring-blue-100 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-100 dark:placeholder:text-slate-500 dark:focus:border-blue-400 dark:focus:ring-blue-500/20"
                                    placeholder="Masukkan nama lengkap">
                                @error('name')
                                    <p class="mt-2 text-sm text-rose-500 dark:text-rose-400">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label class="mb-2 block text-sm font-semibold text-slate-700 dark:text-slate-200">
                                    Email
                                </label>
                                <input type="email" wire:model.defer="email"
                                    class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm text-slate-800 outline-none transition focus:border-blue-500 focus:ring-4 focus:ring-blue-100 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-100 dark:placeholder:text-slate-500 dark:focus:border-blue-400 dark:focus:ring-blue-500/20"
                                    placeholder="Masukkan email">
                                @error('email')
                                    <p class="mt-2 text-sm text-rose-500 dark:text-rose-400">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <div>
                            <label class="mb-2 block text-sm font-semibold text-slate-700 dark:text-slate-200">
                                Foto Profil
                            </label>
                            <div
                                class="rounded-2xl border-2 border-dashed border-slate-200 bg-slate-50 p-5 transition-colors duration-300 dark:border-slate-700 dark:bg-slate-800/60">
                                <input type="file" wire:model="foto_profil"
                                    class="block w-full text-sm text-slate-600 file:mr-4 file:rounded-xl file:border-0 file:bg-blue-600 file:px-4 file:py-2.5 file:text-sm file:font-medium file:text-white hover:file:bg-blue-700 dark:text-slate-300">

                                <div wire:loading wire:target="foto_profil"
                                    class="mt-3 text-sm text-blue-600 dark:text-blue-400">
                                    Mengunggah foto...
                                </div>

                                <p class="mt-3 text-xs text-slate-500 dark:text-slate-400">
                                    Format gambar: JPG, PNG, JPEG. Maksimal 1MB.
                                </p>
                            </div>
                            @error('foto_profil')
                                <p class="mt-2 text-sm text-rose-500 dark:text-rose-400">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="flex justify-end">
                            <button type="submit"
                                class="rounded-2xl bg-gradient-to-r from-blue-600 to-indigo-600 px-6 py-3 text-sm font-semibold text-white shadow-lg transition hover:from-blue-700 hover:to-indigo-700">
                                Simpan Perubahan
                            </button>
                        </div>
                    </form>
                </div>

                <div class="my-8 border-t border-slate-200 dark:border-slate-800"></div>

                <div>
                    <h3 class="text-2xl font-bold text-slate-800 dark:text-slate-100">
                        Ubah Password
                    </h3>
                    <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">
                        Gunakan password yang kuat untuk menjaga keamanan akun Anda.
                    </p>

                    <form wire:submit.prevent="updatePassword" class="mt-6 space-y-5">
                        <div>
                            <label class="mb-2 block text-sm font-semibold text-slate-700 dark:text-slate-200">
                                Password Saat Ini
                            </label>
                            <input type="password" wire:model.defer="current_password"
                                class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm text-slate-800 outline-none transition focus:border-blue-500 focus:ring-4 focus:ring-blue-100 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-100 dark:placeholder:text-slate-500 dark:focus:border-blue-400 dark:focus:ring-blue-500/20"
                                placeholder="Masukkan password saat ini">
                            @error('current_password')
                                <p class="mt-2 text-sm text-rose-500 dark:text-rose-400">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="grid grid-cols-1 gap-5 md:grid-cols-2">
                            <div>
                                <label class="mb-2 block text-sm font-semibold text-slate-700 dark:text-slate-200">
                                    Password Baru
                                </label>
                                <input type="password" wire:model.defer="new_password"
                                    class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm text-slate-800 outline-none transition focus:border-blue-500 focus:ring-4 focus:ring-blue-100 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-100 dark:placeholder:text-slate-500 dark:focus:border-blue-400 dark:focus:ring-blue-500/20"
                                    placeholder="Masukkan password baru">
                                @error('new_password')
                                    <p class="mt-2 text-sm text-rose-500 dark:text-rose-400">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label class="mb-2 block text-sm font-semibold text-slate-700 dark:text-slate-200">
                                    Konfirmasi Password Baru
                                </label>
                                <input type="password" wire:model.defer="new_password_confirmation"
                                    class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm text-slate-800 outline-none transition focus:border-blue-500 focus:ring-4 focus:ring-blue-100 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-100 dark:placeholder:text-slate-500 dark:focus:border-blue-400 dark:focus:ring-blue-500/20"
                                    placeholder="Ulangi password baru">
                            </div>
                        </div>

                        <div class="flex justify-end">
                            <button type="submit"
                                class="rounded-2xl bg-slate-800 px-6 py-3 text-sm font-semibold text-white shadow-lg transition hover:bg-slate-900 dark:bg-slate-100 dark:text-slate-900 dark:hover:bg-white">
                                Update Password
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
</div>
