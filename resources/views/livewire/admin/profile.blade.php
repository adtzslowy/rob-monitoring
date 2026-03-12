<div class="min-h-screen bg-slate-50 p-4 md:p-8">
    <div class="mx-auto max-w-6xl">
        <div class="mb-8">
            <h1 class="text-3xl font-bold tracking-tight text-slate-800">Profil Saya</h1>
            <p class="mt-2 text-sm text-slate-500">
                Kelola informasi akun, foto profil, dan password Anda.
            </p>
        </div>

        <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
            <!-- Card kiri -->
            <div class="lg:col-span-1">
                <div class="overflow-hidden rounded-3xl bg-white shadow-md ring-1 ring-slate-200">
                    <div class="h-28 bg-gradient-to-r from-blue-600 via-indigo-500 to-cyan-400"></div>

                    <div class="relative px-6 pb-6">
                        <div class="-mt-14 mb-4 flex justify-center">
                            @if ($foto_profil)
                                <img
                                    src="{{ $foto_profil->temporaryUrl() }}"
                                    alt="Preview Foto Profil"
                                    class="h-28 w-28 rounded-full border-4 border-white object-cover shadow-lg"
                                >
                            @elseif ($storedFotoProfil)
                                <img
                                    src="{{ asset('storage/' . $storedFotoProfil) }}"
                                    alt="Foto Profil"
                                    class="h-28 w-28 rounded-full border-4 border-white object-cover shadow-lg"
                                >
                            @else
                                <div class="flex h-28 w-28 items-center justify-center rounded-full border-4 border-white bg-slate-100 text-3xl font-bold text-slate-500 shadow-lg">
                                    {{ strtoupper(substr($name, 0, 1)) }}
                                </div>
                            @endif
                        </div>

                        <div class="text-center">
                            <h2 class="text-2xl font-bold text-slate-800">{{ $name }}</h2>
                            <p class="text-sm text-slate-500">{{ $email }}</p>

                            <span class="mt-4 inline-flex rounded-full bg-blue-50 px-4 py-1.5 text-sm font-semibold text-blue-700 ring-1 ring-blue-100">
                                {{ $roleName ?? 'No Role' }}
                            </span>
                        </div>

                        <div class="mt-6 space-y-3">
                            <div class="rounded-2xl bg-slate-50 p-4">
                                <p class="text-xs font-semibold uppercase tracking-wider text-slate-400">Status Akun</p>
                                <p class="mt-1 text-sm font-bold text-emerald-600">Aktif</p>
                            </div>

                            <div class="rounded-2xl bg-slate-50 p-4">
                                <p class="text-xs font-semibold uppercase tracking-wider text-slate-400">Role</p>
                                <p class="mt-1 text-sm font-bold text-slate-700">{{ $roleName ?? 'No Role' }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- 1 Card besar kanan -->
            <div class="lg:col-span-2">
                <div class="rounded-3xl bg-white p-6 shadow-md ring-1 ring-slate-200 md:p-8">
                    @if (session()->has('success'))
                        <div class="mb-5 rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-medium text-emerald-700">
                            {{ session('success') }}
                        </div>
                    @endif

                    @if (session()->has('success_password'))
                        <div class="mb-5 rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-medium text-emerald-700">
                            {{ session('success_password') }}
                        </div>
                    @endif

                    <!-- Section Informasi Profil -->
                    <div>
                        <h3 class="text-2xl font-bold text-slate-800">Informasi Profil</h3>
                        <p class="mt-1 text-sm text-slate-500">
                            Perbarui data pribadi dan foto profil Anda.
                        </p>

                        <form wire:submit.prevent="updateProfile" class="mt-6 space-y-5">
                            <div class="grid grid-cols-1 gap-5 md:grid-cols-2">
                                <div>
                                    <label class="mb-2 block text-sm font-semibold text-slate-700">Nama Lengkap</label>
                                    <input
                                        type="text"
                                        wire:model.defer="name"
                                        class="w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm outline-none transition focus:border-blue-500 focus:ring-4 focus:ring-blue-100"
                                        placeholder="Masukkan nama lengkap"
                                    >
                                    @error('name')
                                        <p class="mt-2 text-sm text-rose-500">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label class="mb-2 block text-sm font-semibold text-slate-700">Email</label>
                                    <input
                                        type="email"
                                        wire:model.defer="email"
                                        class="w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm outline-none transition focus:border-blue-500 focus:ring-4 focus:ring-blue-100"
                                        placeholder="Masukkan email"
                                    >
                                    @error('email')
                                        <p class="mt-2 text-sm text-rose-500">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>

                            <div>
                                <label class="mb-2 block text-sm font-semibold text-slate-700">Foto Profil</label>
                                <div class="rounded-2xl border-2 border-dashed border-slate-200 bg-slate-50 p-5">
                                    <input
                                        type="file"
                                        wire:model="foto_profil"
                                        class="block w-full text-sm text-slate-600 file:mr-4 file:rounded-xl file:border-0 file:bg-blue-600 file:px-4 file:py-2.5 file:text-sm file:font-medium file:text-white hover:file:bg-blue-700"
                                    >

                                    <div wire:loading wire:target="foto_profil" class="mt-3 text-sm text-blue-600">
                                        Mengunggah foto...
                                    </div>

                                    <p class="mt-3 text-xs text-slate-500">
                                        Format gambar: JPG, PNG, JPEG. Maksimal 1MB.
                                    </p>
                                </div>
                                @error('foto_profil')
                                    <p class="mt-2 text-sm text-rose-500">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="flex justify-end">
                                <button
                                    type="submit"
                                    class="rounded-2xl bg-gradient-to-r from-blue-600 to-indigo-600 px-6 py-3 text-sm font-semibold text-white shadow-lg transition hover:from-blue-700 hover:to-indigo-700"
                                >
                                    Simpan Perubahan
                                </button>
                            </div>
                        </form>
                    </div>

                    <!-- Divider -->
                    <div class="my-8 border-t border-slate-200"></div>

                    <!-- Section Password -->
                    <div>
                        <h3 class="text-2xl font-bold text-slate-800">Ubah Password</h3>
                        <p class="mt-1 text-sm text-slate-500">
                            Gunakan password yang kuat untuk menjaga keamanan akun Anda.
                        </p>

                        <form wire:submit.prevent="updatePassword" class="mt-6 space-y-5">
                            <div>
                                <label class="mb-2 block text-sm font-semibold text-slate-700">Password Saat Ini</label>
                                <input
                                    type="password"
                                    wire:model.defer="current_password"
                                    class="w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm outline-none transition focus:border-blue-500 focus:ring-4 focus:ring-blue-100"
                                    placeholder="Masukkan password saat ini"
                                >
                                @error('current_password')
                                    <p class="mt-2 text-sm text-rose-500">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="grid grid-cols-1 gap-5 md:grid-cols-2">
                                <div>
                                    <label class="mb-2 block text-sm font-semibold text-slate-700">Password Baru</label>
                                    <input
                                        type="password"
                                        wire:model.defer="new_password"
                                        class="w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm outline-none transition focus:border-blue-500 focus:ring-4 focus:ring-blue-100"
                                        placeholder="Masukkan password baru"
                                    >
                                    @error('new_password')
                                        <p class="mt-2 text-sm text-rose-500">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label class="mb-2 block text-sm font-semibold text-slate-700">Konfirmasi Password Baru</label>
                                    <input
                                        type="password"
                                        wire:model.defer="new_password_confirmation"
                                        class="w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm outline-none transition focus:border-blue-500 focus:ring-4 focus:ring-blue-100"
                                        placeholder="Ulangi password baru"
                                    >
                                </div>
                            </div>

                            <div class="flex justify-end">
                                <button
                                    type="submit"
                                    class="rounded-2xl bg-slate-800 px-6 py-3 text-sm font-semibold text-white shadow-lg transition hover:bg-slate-900"
                                >
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