<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <h3 class="text-lg font-semibold mb-4">Buscar Producto, Lote o Serie</h3>

                    <!-- Formulario de Búsqueda -->
                    <form method="POST" action="{{ route('buscar.producto') }}">
                        @csrf
                        <div class="mb-4">
                            <label for="codigo" class="block text-sm font-medium text-gray-700">Código del Producto / Lote / Serie</label>
                            <input type="text" id="codigo" name="codigo" class="w-full border p-2 rounded-md" placeholder="Ingrese el código, lote o serie" required>
                        </div>
                        <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded">Buscar</button>
                    </form>

                    <!-- Mostrar Resultados de Búsqueda -->
                    @isset($resultado)
                        <div class="mt-6">
                            <h3 class="font-semibold text-xl">Resultado de la Búsqueda:</h3>
                            @if($tipo == 'producto')
                                <p><strong>Producto:</strong> {{ $resultado->nombre }} (Código: {{ $resultado->codigo }})</p>
                                <p><strong>Lotes:</strong></p>
                                <ul>
                                    @foreach($resultado->lotes as $lote)
                                        <li>Lote: {{ $lote->numero_lote }} (Fecha de vencimiento: {{ $lote->fecha_vencimiento }})</li>
                                    @endforeach
                                </ul>
                            @elseif($tipo == 'lote')
                                <p><strong>Lote:</strong> {{ $resultado->numero_lote }} (Producto: {{ $resultado->producto->nombre }})</p>
                                <p><strong>Series:</strong></p>
                                <ul>
                                    @foreach($resultado->series as $serie)
                                        <li>Serie: {{ $serie->numero_serie }} (Estado: {{ $serie->estado }})</li>
                                    @endforeach
                                </ul>
                            @elseif($tipo == 'serie')
                                <p><strong>Serie:</strong> {{ $resultado->numero_serie }} (Producto: {{ $resultado->producto->nombre }})</p>
                            @else
                                <p>No se encontraron coincidencias.</p>
                            @endif
                        </div>
                    @endisset
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
