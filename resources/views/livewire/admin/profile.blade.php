<div class="min-h-screen bg-slate-50 p-4 dark:bg-slate-950">
    <div class="mx-auto max-w-5xl">

        <!-- CARD -->
        <div class="rounded-3xl bg-white shadow-sm ring-1 ring-slate-200 dark:bg-slate-900 dark:ring-white/10">

            <div class="p-6 sm:p-8">

                <!-- HEADER -->
                <div class="mb-8">
                    <h1 class="text-2xl font-bold text-slate-800 dark:text-white">
                        Profile Settings
                    </h1>
                    <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">
                        Kelola informasi akun kamu
                    </p>
                </div>

                <!-- AVATAR + UPLOAD -->
                <div class="mb-8 flex flex-col items-center gap-4 sm:flex-row sm:items-center">

                    <!-- Avatar -->
                    <div>
                        @if ($foto_profil)
                            <img src="{{ $foto_profil->temporaryUrl() }}"
                                class="h-24 w-24 rounded-full object-cover ring-4 ring-white dark:ring-slate-900">
                        @elseif ($storedFotoProfil)
                            <img src="{{ asset('storage/' . $storedFotoProfil) }}"
                                class="h-24 w-24 rounded-full object-cover ring-4 ring-white dark:ring-slate-900">
                        @else
                            <div class="flex h-24 w-24 items-center justify-center rounded-full bg-slate-200 text-2xl font-bold dark:bg-slate-800">
                                {{ strtoupper(substr($name, 0, 1)) }}
                            </div>
                        @endif
                    </div>

                    <!-- Upload -->
                    <div class="w-full">
                        <input type="file" wire:model="foto_profil"
                            class="block w-full text-sm file:mr-4 file:rounded-xl file:border-0
                                   file:bg-blue-600 file:px-4 file:py-2 file:text-white hover:file:bg-blue-700">

                        <p class="mt-2 text-xs text-slate-400">
                            JPG, PNG maksimal 1MB
                        </p>
                    </div>
                </div>

                <!-- FORM -->
                <form wire:submit.prevent="updateProfile" class="space-y-6">

                    <div class="grid grid-cols-1 gap-5 md:grid-cols-2">

                        <!-- Nama -->
                        <div>
                            <label class="text-sm font-semibold text-slate-700 dark:text-slate-200">
                                Nama
                            </label>
                            <input type="text" wire:model.defer="name"
                                class="mt-2 w-full rounded-xl border bg-slate-50 px-4 py-3 dark:bg-slate-800">
                        </div>

                        <!-- Email -->
                        <div>
                            <label class="text-sm font-semibold text-slate-700 dark:text-slate-200">
                                Email
                            </label>
                            <input type="email" wire:model.defer="email"
                                class="mt-2 w-full rounded-xl border bg-slate-50 px-4 py-3 dark:bg-slate-800">
                        </div>

                    </div>

                    <!-- BUTTON -->
                    <div class="flex justify-end">
                        <button type="submit"
                            class="rounded-xl bg-blue-600 px-6 py-3 text-sm font-semibold text-white hover:bg-blue-700">
                            Simpan
                        </button>
                    </div>

                </form>

                <!-- DIVIDER -->
                <div class="my-10 h-px bg-slate-200 dark:bg-white/10"></div>

                <!-- PASSWORD -->
                <form wire:submit.prevent="updatePassword" class="space-y-6">

                    <h3 class="text-lg font-bold text-slate-800 dark:text-white">
                        Ubah Password
                    </h3>

                    <div>
                        <input type="password" wire:model.defer="current_password"
                            placeholder="Password lama"
                            class="w-full rounded-xl border bg-slate-50 px-4 py-3 dark:bg-slate-800">
                    </div>

                    <div class="grid grid-cols-1 gap-5 md:grid-cols-2">
                        <input type="password" wire:model.defer="new_password"
                            placeholder="Password baru"
                            class="rounded-xl border bg-slate-50 px-4 py-3 dark:bg-slate-800">

                        <input type="password" wire:model.defer="new_password_confirmation"
                            placeholder="Konfirmasi password"
                            class="rounded-xl border bg-slate-50 px-4 py-3 dark:bg-slate-800">
                    </div>

                    <div class="flex justify-end">
                        <button type="submit"
                            class="rounded-xl bg-slate-900 px-6 py-3 text-sm font-semibold text-white hover:bg-slate-800 dark:bg-white dark:text-black">
                            Update Password
                        </button>
                    </div>

                </form>

            </div>
        </div>

    </div>
</div>