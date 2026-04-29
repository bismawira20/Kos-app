<div class="border-b border-white/10 px-4 py-4">
    <a href="{{ $dashboardUrl }}" class="flex items-center gap-3 rounded-lg outline-none ring-white/0 transition hover:bg-white/5 focus-visible:ring-2 focus-visible:ring-white/40">
        <img src="{{ asset('images/epaykos-logo.svg') }}" alt="{{ config('app.name', 'ePayKos') }}" class="h-10 w-auto shrink-0" width="160" height="40">
        <div class="min-w-0 leading-tight">
            <span class="block text-lg font-bold tracking-tight text-white">{{ config('app.name', 'ePayKos') }}</span>
            <span class="text-xs text-indigo-200">{{ $subtitle }}</span>
        </div>
    </a>
</div>
