<div class="min-h-screen bg-gradient-to-br from-zinc-50 to-zinc-100 p-4 dark:from-zinc-950 dark:to-zinc-900">
    <div class="mx-auto">


            <!-- subtle glow -->
            <div class="pointer-events-none absolute -top-20 -right-20 h-72 w-72 rounded-full bg-blue-500/10 blur-3xl"></div>

            <div class="p-6 sm:p-8">

                <!-- HEADER -->
                <div class="mb-10 flex items-start justify-between">
                    <div>
                        <h1 class="text-2xl font-bold text-zinc-800 dark:text-white">
                            Profile Settings
                        </h1>
                        <p class="mt-1 text-sm text-zinc-500 dark:text-zinc-400">
                            Kelola informasi akun dan keamanan kamu
                        </p>
                    </div>

                    <!-- status badge -->
                    <span class="hidden sm:inline-flex items-center rounded-full bg-emerald-500/10 px-3 py-1 text-xs font-semibold text-emerald-600 dark:text-emerald-400">
                        ● Active
                    </span>
                </div>

                <!-- PROFILE -->
                <div class="mb-10 flex flex-col items-center gap-5 sm:flex-row">

                    <!-- Avatar -->
                    <div class="relative">
                        @if ($foto_profil)
                            <img src="{{ $foto_profil->temporaryUrl() }}"
                                class="h-24 w-24 rounded-full object-cover ring-4 ring-white shadow-lg dark:ring-zinc-900">
                        @elseif ($storedFotoProfil)
                            <img src="{{ asset('storage/' . $storedFotoProfil) }}"
                                class="h-24 w-24 rounded-full object-cover ring-4 ring-white shadow-lg dark:ring-zinc-900">
                        @else
                            <div class="flex h-24 w-24 items-center justify-center rounded-full bg-zinc-200 text-2xl font-bold dark:bg-zinc-800">
                                {{ strtoupper(substr($name, 0, 1)) }}
                            </div>
                        @endif

                        <!-- online dot -->
                        <span class="absolute bottom-1 right-1 h-3 w-3 rounded-full bg-emerald-500 ring-2 ring-white dark:ring-zinc-900"></span>
                    </div>

                    <!-- Info -->
                    <div class="flex-1 text-center sm:text-left">
                        <h2 class="text-lg font-semibold text-zinc-800 dark:text-white">
                            {{ $name }}
                        </h2>
                        <p class="text-sm text-zinc-500 dark:text-zinc-400">
                            {{ $email }}
                        </p>

                        <!-- Upload -->
                        <div class="mt-3">
                            <input type="file" wire:model="foto_profil"
                                class="block w-full text-xs text-zinc-600 dark:text-zinc-300
                                       file:mr-4 file:rounded-lg file:border-0
                                       file:bg-gradient-to-r file:from-blue-600 file:to-indigo-600
                                       file:px-4 file:py-2 file:text-white
                                       hover:file:from-blue-700 hover:file:to-indigo-700">
                        </div>
                    </div>
                </div>

                <!-- FORM -->
                <form wire:submit.prevent="updateProfile" class="space-y-6">

                    <div class="grid grid-cols-1 gap-6 md:grid-cols-2">

                        <!-- Nama -->
                        <div>
                            <label class="text-sm font-semibold text-zinc-700 dark:text-zinc-200">
                                Nama Lengkap
                            </label>
                            <input type="text" wire:model.defer="name"
                                class="mt-2 w-full rounded-xl border border-zinc-200 bg-zinc-50 px-4 py-3 text-sm
                                       focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10
                                       dark:border-white/10 dark:bg-zinc-800 dark:text-white">
                        </div>

                        <!-- Email -->
                        <div>
                            <label class="text-sm font-semibold text-zinc-700 dark:text-zinc-200">
                                Email
                            </label>
                            <input type="email" wire:model.defer="email"
                                class="mt-2 w-full rounded-xl border border-zinc-200 bg-zinc-50 px-4 py-3 text-sm
                                       focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10
                                       dark:border-white/10 dark:bg-zinc-800 dark:text-white">
                        </div>

                    </div>

                    <!-- BUTTON -->
                    <div class="flex justify-end">
                        <button type="submit"
                            class="rounded-xl bg-gradient-to-r from-blue-600 to-indigo-600 px-6 py-3 text-sm font-semibold text-white
                                   shadow-lg shadow-blue-500/20 transition hover:scale-[1.02]">
                            Simpan Perubahan
                        </button>
                    </div>

                </form>

                <!-- DIVIDER -->
                <div class="my-10 h-px bg-gradient-to-r from-transparent via-zinc-200 to-transparent dark:via-white/10"></div>

                <!-- PASSWORD -->
                <form wire:submit.prevent="updatePassword" class="space-y-6">

                    <div>
                        <h3 class="text-lg font-semibold text-zinc-800 dark:text-white">
                            Ubah Password
                        </h3>
                        <p class="text-sm text-zinc-500 dark:text-zinc-400">
                            Gunakan password yang kuat
                        </p>
                    </div>

                    <input type="password" wire:model.defer="current_password"
                        placeholder="Password saat ini"
                        class="w-full rounded-xl border border-zinc-200 bg-zinc-50 px-4 py-3 text-sm
                               focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10
                               dark:border-white/10 dark:bg-zinc-800 dark:text-white">

                    <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                        <input type="password" wire:model.defer="new_password"
                            placeholder="Password baru"
                            class="rounded-xl border border-zinc-200 bg-zinc-50 px-4 py-3 text-sm
                                   focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10
                                   dark:border-white/10 dark:bg-zinc-800 dark:text-white">

                        <input type="password" wire:model.defer="new_password_confirmation"
                            placeholder="Konfirmasi password"
                            class="rounded-xl border border-zinc-200 bg-zinc-50 px-4 py-3 text-sm
                                   focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10
                                   dark:border-white/10 dark:bg-zinc-800 dark:text-white">
                    </div>

                    <div class="flex justify-end">
                        <button type="submit"
                            class="rounded-xl bg-zinc-900 px-6 py-3 text-sm font-semibold text-white
                                   transition hover:bg-zinc-800 dark:bg-white dark:text-black">
                            Update Password
                        </button>
                    </div>

                </form>

            </div>
    </div>
</div>