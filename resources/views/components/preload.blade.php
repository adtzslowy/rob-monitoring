@props([
    'theme' => 'localStorage',
    'bg'    => 'slate', {{-- 'slate' = slate-50/950, 'dark' = selalu slate-950 --}}
])

<div id="rob-preloader"
     @class([
         'fixed inset-0 z-[9999] flex flex-col items-center justify-center gap-6 transition-colors duration-300',
         'bg-slate-50 dark:bg-slate-950' => $bg === 'slate',
         'bg-slate-950'                  => $bg === 'dark',
     ])>

    {{-- grid --}}
    <div class="absolute inset-0 pointer-events-none opacity-[0.03] dark:opacity-[0.06]"
         style="background-image:linear-gradient(#3b82f6 1px,transparent 1px),linear-gradient(to right,#3b82f6 1px,transparent 1px);background-size:48px 48px;"></div>

    {{-- glow --}}
    <div class="absolute -top-24 -left-24 h-72 w-72 rounded-full bg-cyan-500/20 blur-3xl pointer-events-none"></div>
    <div class="absolute right-0 bottom-0 h-80 w-80 rounded-full bg-blue-600/20 blur-3xl pointer-events-none"></div>

    {{-- spinner --}}
    <div class="relative w-14 h-14 z-10">
        <div class="absolute inset-0 rounded-full border-[2.5px] border-transparent
                    border-t-cyan-400 border-r-cyan-400/20 border-b-cyan-400/10
                    animate-spin"></div>
        <div class="absolute inset-[10px] rounded-full border-[2.5px] border-transparent
                    border-t-blue-400/50
                    animate-[spin_0.65s_linear_infinite_reverse]"></div>
        <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2
                    w-2 h-2 rounded-full bg-cyan-400 animate-pulse"></div>
    </div>

    {{-- teks --}}
    <div class="text-center z-10">
        <p @class([
            'text-sm font-bold tracking-wide',
            'text-slate-900 dark:text-white' => $bg === 'slate',
            'text-white'                     => $bg === 'dark',
        ])>ROB Monitoring</p>
        <p class="text-xs text-slate-400 mt-1 flex items-center justify-center gap-1">
            <span id="rob-pl-status">Memuat halaman</span>
            <span class="flex gap-0.5 ml-0.5">
                <span class="w-1 h-1 rounded-full bg-cyan-400 animate-[bounce_1.2s_ease-in-out_infinite]"></span>
                <span class="w-1 h-1 rounded-full bg-cyan-400 animate-[bounce_1.2s_ease-in-out_0.2s_infinite]"></span>
                <span class="w-1 h-1 rounded-full bg-cyan-400 animate-[bounce_1.2s_ease-in-out_0.4s_infinite]"></span>
            </span>
        </p>
    </div>

    {{-- progress bar --}}
    <div class="w-32 h-0.5 rounded-full bg-cyan-400/15 overflow-hidden z-10">
        <div id="rob-pl-bar"
             class="h-full rounded-full bg-cyan-400 transition-all duration-300 ease-out"
             style="width:0%"></div>
    </div>
</div>

@if($theme === 'db')
    <script>
        (function(){
            const dbTheme = {{ Js::from($dbTheme ?? 'dark') }};
            document.documentElement.classList.toggle('dark', dbTheme === 'dark');
        })();
    </script>
@elseif($theme === 'localStorage')
    <script>
        (function(){
            const t = localStorage.getItem('theme');
            document.documentElement.classList.toggle('dark', t === 'dark' || (!t && true));
        })();
    </script>
@endif
{{-- theme="none" → tidak inject script, untuk halaman yang always dark --}}
