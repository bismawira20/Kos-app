<div class="shrink-0 border-b border-slate-200 bg-white px-4 py-4">
    <a href="{{ $dashboardUrl }}" class="flex items-center gap-3 rounded-lg outline-none ring-slate-300 transition hover:bg-slate-50 focus-visible:ring-2">
        <img src="{{ asset('images/epaykos-logo.svg') }}" alt="{{ config('app.name', 'ePayKos') }}" class="h-10 w-auto shrink-0" width="160" height="40">
        <div class="min-w-0 leading-tight">
            <span class="block text-sm font-bold tracking-tight text-slate-900">{{ config('app.name', 'ePayKos') }}</span>
            <span class="text-xs text-slate-500">{{ $subtitle }}</span>
        </div>
    </a>
</div>
