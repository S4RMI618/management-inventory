<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">

        <!-- Total Clientes -->
    <div class="bg-gradient-to-br from-primary-dark to-primary-soft border-4 border-primary shadow-xl rounded-2xl p-6 flex items-center gap-4">
        <div class="bg-accent rounded-full p-3 flex items-center justify-center shadow">
            <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M17 20h5v-2a4 4 0 00-3-3.87M9 20H4v-2a4 4 0 013-3.87M15 7a4 4 0 11-8 0 4 4 0 018 0zm6 4v6M3 11v6"/>
            </svg>
        </div>
        <div>
            <div class="text-3xl font-bold text-white">{{ $totalClientes }}</div>
            <div class="text-accent text-lg font-semibold">Clientes registrados</div>
        </div>
    </div>

    <!-- Movimientos recientes -->
    <div class="md:col-span-2 bg-gradient-to-br from-primary-dark to-primary-soft border-4 border-primary shadow-xl rounded-2xl p-6">
        <div class="mb-4 text-accent text-lg font-bold">Movimientos recientes</div>
        <div class="divide-y divide-primary-soft">
            @forelse($movimientos as $mov)
                <div class="py-3 flex items-center gap-3">
                    <div class="flex-shrink-0 w-9 h-9 rounded-full bg-primary-bg flex items-center justify-center text-xl font-bold text-accent">
                        {{ strtoupper(substr($mov->tipo,0,1)) }}
                    </div>
                    <div class="flex-1">
                        <div class="font-bold text-white">{{ $mov->producto->nombre ?? 'Producto' }}</div>
                        <div class="text-gray-300 text-sm">
                            {{ ucfirst($mov->tipo) }} -
                            Cliente: {{ $mov->socioComercial->nombre ?? 'N/A' }},
                            Usuario: {{ $mov->usuario->name ?? '' }},
                            Fecha: {{ $mov->created_at->format('d/m/Y H:i') }}
                        </div>
                    </div>
                    <div class="font-semibold text-accent">{{ $mov->cantidad }}</div>
                </div>
            @empty
                <div class="py-6 text-gray-400 text-center">Sin movimientos recientes.</div>
            @endforelse
        </div>
    </div>
</div>

</x-app-layout>