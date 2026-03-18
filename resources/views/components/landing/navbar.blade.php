@php
    $navLinks = [
        ['label' => 'Tentang', 'route' => 'tentang'],
        ['label' => 'Fitur', 'route' => 'fitur'],
        ['label' => 'Peta Monitoring', 'route' => 'peta'],
        ['label' => 'Alur Kerja', 'route' => 'alur_kerja'],
        ['label' => 'Status', 'route' => 'status'],
        ['label' => 'Kontak', 'route' => 'kontak'],
    ];
@endphp

{{-- Desktop --}}
<div class="hidden md:flex items-center gap-6">
    @foreach ($navLinks as $n)
        <a href="{{ route($n['route']) }}" @class([
            'text-sm transition',
            'text-slate-900 dark:text-white font-semibold' => request()->routeIs(
                $n['route']),
            'text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white' => !request()->routeIs(
                $n['route']),
        ])>
            {{ $n['label'] }}
        </a>
    @endforeach
</div>

{{-- Mobile --}}
<div x-show="mobileMenu" x-cloak x-transition
    class="md:hidden border-t border-slate-200 dark:border-slate-800 py-3 space-y-1">
    @foreach ($navLinks as $n)
        <a href="{{ route($n['route']) }}"
            @click="mobileMenu = false"
            @class([
                'block px-3 py-2 rounded-xl text-sm transition',
                'bg-blue-50 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400 font-semibold' => request()->routeIs($n['route']),
                'text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800' => !request()->routeIs($n['route']),
            ])>
            {{ $n['label'] }}
        </a>
    @endforeach
    <a href="{{ route('login') }}"
        class="block px-3 py-2 rounded-xl text-sm font-semibold text-blue-600 hover:bg-blue-50 dark:hover:bg-blue-900/30">
        Masuk ke Dashboard →
    </a>
</div>
