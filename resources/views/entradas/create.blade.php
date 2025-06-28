<x-app-layout>
    <div class="max-w-3xl mx-auto py-10">
        <h2 class="text-xl font-bold mb-6">Registrar Entrada de Producto</h2>

        @if(session('success'))
            <div class="p-2 bg-green-200 text-green-800 mb-4 rounded">
                {{ session('success') }}
            </div>
        @endif

        <form method="POST" action="{{ route('entrada.store') }}">
            @csrf

            <!-- Buscador de Producto -->
            <div class="mb-4">
                <label class="block">Buscar Producto</label>
                <input type="text" id="productoBusqueda" class="w-full border p-2 rounded" placeholder="Escribe el nombre del producto...">
                <div id="productoResultados" class="mt-2"></div>
                <input type="hidden" name="producto_id" id="producto_id">
                <p id="infoBusqueda" class="text-sm mt-1 text-gray-600"></p>
            </div>

            <!-- Almacén (Filtrado por el producto seleccionado) -->
            <div class="mb-4">
                <label class="block">Almacén</label>
                <select name="almacen_id" class="w-full border p-2 rounded">
                    @foreach($almacenes as $almacen)
                        <option value="{{ $almacen->id }}">{{ $almacen->nombre }}</option>
                    @endforeach
                </select>
            </div>

            <div class="mb-4">
                <label class="block">Cantidad</label>
                <input type="number" name="cantidad" class="w-full border p-2 rounded" required>
            </div>

            <div class="mb-4">
                <label class="block">Número de Lote (opcional)</label>
                <input type="text" name="lote" class="w-full border p-2 rounded">
            </div>

            <div class="mb-4">
                <label class="block">Series (opcional, separadas por línea)</label>
                <textarea name="serie[]" rows="3" class="w-full border p-2 rounded" placeholder="SERIE001&#10;SERIE002"></textarea>
            </div>

            <button class="bg-blue-600 text-white px-4 py-2 rounded">Registrar Entrada</button>
        </form>
    </div>

    <script>
        document.getElementById('productoBusqueda').addEventListener('input', function () {
            const query = this.value;

            if (query.length < 4) {
                document.getElementById('productoResultados').innerHTML = ''; // Limpiar resultados
                return;
            }

            fetch("{{ route('producto.buscar.ajax') }}", {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ codigo: query })
            })
            .then(response => response.json())
            .then(data => {
                let resultadosHTML = '';
                if (data.productos && data.productos.length > 0) {
                    data.productos.forEach(producto => {
                        resultadosHTML += `
                            <div class="p-2 cursor-pointer bg-gray-100 hover:bg-gray-200" 
                                 onclick="seleccionarProducto(${producto.id}, '${producto.nombre}', '${producto.codigo}')">
                                 ${producto.nombre} (${producto.codigo})
                            </div>
                        `;
                    });
                } else {
                    resultadosHTML = '<div class="p-2 text-gray-600">No se encontraron productos.</div>';
                }

                document.getElementById('productoResultados').innerHTML = resultadosHTML;
            })
            .catch(error => {
                document.getElementById('productoResultados').innerHTML = '<div class="p-2 text-red-600">Error al buscar productos.</div>';
            });
        });

        function seleccionarProducto(id, nombre, codigo) {
            document.getElementById('producto_id').value = id;
            document.getElementById('productoBusqueda').value = `${nombre} (${codigo})`;
            document.getElementById('productoResultados').innerHTML = ''; // Limpiar resultados
        }
    </script>
</x-app-layout>
