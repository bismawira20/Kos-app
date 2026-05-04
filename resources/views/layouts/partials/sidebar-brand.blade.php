<div class="shrink-0 border-b border-slate-200 bg-white px-4 py-4">
    <a href="{{ $dashboardUrl }}" class="flex items-center gap-3 rounded-xl outline-none ring-slate-300 transition hover:bg-slate-50 focus-visible:ring-2">
        <div class="flex h-10 w-10 shrink-0 items-center justify-center overflow-hidden rounded-xl bg-indigo-600 shadow-lg shadow-indigo-200">
            <img src="{{ asset('images/epaykos-logo.png') }}" alt="{{ config('app.name', 'ePayKos') }}" class="h-full w-full object-cover">
        </div>
        <div class="min-w-0 leading-tight">
            <span class="block text-sm font-bold tracking-tight text-slate-900">{{ config('app.name', 'ePayKos') }}</span>
            <span class="text-xs font-medium text-slate-500">{{ $subtitle }}</span>
        </div>
    </a>
</div>
