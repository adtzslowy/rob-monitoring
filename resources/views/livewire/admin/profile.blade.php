<div class="min-h-screen bg-gradient-to-br from-slate-50 via-white to-blue-50 p-4 md:p-8">
    <div class="mx-auto max-w-6xl">
        <div class="mb-8">
            <h1 class="text-3xl font-bold tracking-tight text-slate-800">Profil Saya</h1>
            <p class="mt-2 text-sm text-slate-500">
                Kelola informasi akun, foto profil, dan password Anda.
            </p>
        </div>

        <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
            <!-- Profile Summary -->
            <div class="lg:col-span-1">
                <div class="overflow-hidden rounded-3xl border border-white/60 bg-white/80 shadow-xl backdrop-blur">
                    <div class="h-28 bg-gradient-to-r from-blue-600 via-indigo-500 to-cyan-400"></div>

                    <div class="relative px-6 pb-6">
                        <div class="-mt-14 mb-4 flex justify-center">
                            <div class="relative">
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
                        </div>

                        <div class="text-center">
                            <h2 class="text-xl font-semibold text-slate-800">{{ $name }}</h2>
                            <p class="text-sm text-slate-500">{{ $email }}</p>

                            <div class="mt-4 inline-flex items-center rounded-full bg-blue-50 px-4 py-1.5 text-sm font-medium text-blue-700 ring-1 ring-blue-100">
                                {{ $roleName ?? 'No Role' }}
                            </div>
                        </div>

                        <div class="mt-6 grid grid-cols-1 gap-3">
                            <div class="rounded-2xl bg-slate-50 p-4">
                                <p class="text-xs font-medium uppercase tracking-wider text-slate-400">Status Akun</p>
                                <p class="mt-1 text-sm font-semibold text-emerald-600">Aktif</p>
                            </div>

                            <div class="rounded-2xl bg-slate-50 p-4">
                                <p class="text-xs font-medium uppercase tracking-wider text-slate-400">Role</p>
                                <p class="mt-1 text-sm font-semibold text-slate-700">{{ $roleName ?? 'No Role' }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Forms -->
            <div class="space-y-6 lg:col-span-2">
                <!-- Update Profile -->
                <div class="rounded-3xl border border-white/60 bg-white/80 p-6 shadow-xl backdrop-blur md:p-8">
                    <div class="mb-6 flex items-center justify-between">
                        <div>
                            <h3 class="text-xl font-semibold text-slate-800">Informasi Profil</h3>
                            <p class="mt-1 text-sm text-slate-500">
                                Perbarui data pribadi dan foto profil Anda.
                            </p>
                        </div>
                    </div>

                    @if (session()->has('success'))
                        <div class="mb-5 rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-medium text-emerald-700">
                            {{ session('success') }}
                        </div>
                    @endif

                    <form wire:submit.prevent="updateProfile" class="space-y-5">
                        <div class="grid grid-cols-1 gap-5 md:grid-cols-2">
                            <div class="md:col-span-1">
                                <label class="mb-2 block text-sm font-semibold text-slate-700">Nama Lengkap</label>
                                <input
                                    type="text"
                                    wire:model.defer="name"
                                    class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm text-slate-800 outline-none transition focus:border-blue-500 focus:ring-4 focus:ring-blue-100"
                                    placeholder="Masukkan nama lengkap"
                                >
                                @error('name')
                                    <p class="mt-2 text-sm text-rose-500">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="md:col-span-1">
                                <label class="mb-2 block text-sm font-semibold text-slate-700">Email</label>
                                <input
                                    type="email"
                                    wire:model.defer="email"
                                    class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm text-slate-800 outline-none transition focus:border-blue-500 focus:ring-4 focus:ring-blue-100"
                                    placeholder="Masukkan email"
                                >
                                @error('email')
                                    <p class="mt-2 text-sm text-rose-500">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <div>
                            <label class="mb-2 block text-sm font-semibold text-slate-700">Foto Profil</label>

                            <div class="rounded-2xl border-2 border-dashed border-slate-200 bg-slate-50 p-5 transition hover:border-blue-300 hover:bg-blue-50/40">
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
                                class="inline-flex items-center rounded-2xl bg-gradient-to-r from-blue-600 to-indigo-600 px-6 py-3 text-sm font-semibold text-white shadow-lg shadow-blue-200 transition hover:scale-[1.01] hover:from-blue-700 hover:to-indigo-700 focus:outline-none"
                            >
                                Simpan Perubahan
                            </button>
                        </div>
                    </form>
                </div>

                <!-- Update Password -->
                <div class="rounded-3xl border border-white/60 bg-white/80 p-6 shadow-xl backdrop-blur md:p-8">
                    <div class="mb-6">
                        <h3 class="text-xl font-semibold text-slate-800">Ubah Password</h3>
                        <p class="mt-1 text-sm text-slate-500">
                            Gunakan password yang kuat untuk menjaga keamanan akun Anda.
                        </p>
                    </div>

                    @if (session()->has('success_password'))
                        <div class="mb-5 rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-medium text-emerald-700">
                            {{ session('success_password') }}
                        </div>
                    @endif

                    <form wire:submit.prevent="updatePassword" class="space-y-5">
                        <div>
                            <label class="mb-2 block text-sm font-semibold text-slate-700">Password Saat Ini</label>
                            <input
                                type="password"
                                wire:model.defer="current_password"
                                class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm text-slate-800 outline-none transition focus:border-blue-500 focus:ring-4 focus:ring-blue-100"
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
                                    class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm text-slate-800 outline-none transition focus:border-blue-500 focus:ring-4 focus:ring-blue-100"
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
                                    class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm text-slate-800 outline-none transition focus:border-blue-500 focus:ring-4 focus:ring-blue-100"
                                    placeholder="Ulangi password baru"
                                >
                            </div>
                        </div>

                        <div class="flex justify-end">
                            <button
                                type="submit"
                                class="inline-flex items-center rounded-2xl bg-gradient-to-r from-slate-800 to-slate-700 px-6 py-3 text-sm font-semibold text-white shadow-lg shadow-slate-200 transition hover:scale-[1.01] hover:from-slate-900 hover:to-slate-800 focus:outline-none"
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