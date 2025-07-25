{{-- resources/views/devoluciones/create.blade.php --}}
<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-2xl text-accent text-center">Registrar Devolución</h2>
    </x-slot>

    <x-alert />

    <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div
                class="bg-gradient-to-br from-primary-dark to-primary-soft border-4 border-primary shadow-2xl rounded-lg p-10">

                {{-- Buscar Serie --}}
                <div class="mb-4 flex gap-2">
                    <input type="text" id="numero_serie" placeholder="Ingrese número de serie"
                        class="flex-1 border-2 border-primary-light rounded-lg bg-primary-bg text-white placeholder-gray-400 px-4 py-2 focus:border-primary focus:ring-2 focus:ring-primary transition-all duration-200" />
                    <button type="button" id="btn-buscar-serie"
                        class="bg-primary text-white hover:bg-accent hover:scale-105 active:scale-95 transition-all duration-200 px-4 py-2 rounded-md shadow-x">
                        Buscar
                    </button>
                </div>

                {{-- Datos Serie (se llenan vía JS) --}}
                <div id="datos-serie" class="hidden mb-6 p-4 bg-primary-soft border-2 border-primary-light rounded-lg text-white">
                    <h3 class="text-xl font-semibold mb-3">Datos de la Serie</h3>
                    <p><b>Producto:</b> <span id="producto"></span></p>
                    <p><b>Estado actual:</b> <span id="estado"></span></p>
                    <p><b>Fecha de venta:</b> <span id="fecha_venta"></span></p>
                    <p><b>Cliente:</b> <span id="cliente"></span></p>
                    <p><b>Factura:</b> <span id="factura"></span></p>
                </div>

                {{-- Formulario Devolución --}}
                <form id="form-devolucion" action="{{ route('devoluciones.store') }}" method="POST" class="hidden space-y-4">
                    @csrf
                    <input type="hidden" name="serie_id" id="serie_id" />
                    <input type="hidden" name="producto_id" id="producto_id" />
                    <input type="hidden" name="almacen_id" id="almacen_id" />

                    {{-- Motivo --}}
                    <div>
                        <label class="text-white block mb-1">Motivo</label>
                        <select name="motivo"
                            class="w-full border-2 border-primary-light rounded-lg bg-primary-bg text-white px-4 py-2 focus:border-primary focus:ring-2 focus:ring-primary transition-all duration-200"
                            required>
                            <option value="">– Seleccione –</option>
                            <option value="garantia">Garantía</option>
                            <option value="perdida">Pérdida</option>
                            <option value="otro">Otro</option>
                        </select>
                    </div>

                    {{-- Detalle --}}
                    <div>
                        <label class="text-white block mb-1">Detalle</label>
                        <textarea name="detalle" rows="3"
                            class="w-full border-2 border-primary-light rounded-lg bg-primary-bg text-white px-4 py-2 focus:border-primary focus:ring-2 focus:ring-primary transition-all duration-200"
                            required></textarea>
                    </div>

                    {{-- Botón --}}
                    <button type="submit"
                        class="bg-primary text-white hover:bg-accent hover:scale-105 active:scale-95 transition-all duration-200 px-4 py-2 rounded-md shadow-x">
                        Registrar Devolución
                    </button>
                </form>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            document.getElementById('btn-buscar-serie').addEventListener('click', async () => {
                const serie = document.getElementById('numero_serie').value.trim();
                if (!serie) return alert('Ingrese un número de serie');

                const res = await fetch(`/buscar-serie/${serie}`);
                const data = await res.json();

                if (!data.success) {
                    alert('Serie no encontrada o sin venta registrada.');
                    return;
                }

                document.getElementById('datos-serie').classList.remove('hidden');
                document.getElementById('form-devolucion').classList.remove('hidden');

                document.getElementById('producto').textContent = data.producto.nombre;
                document.getElementById('estado').textContent = data.serie.estado;
                document.getElementById('fecha_venta').textContent = data.venta.fecha ?? 'N/A';
                document.getElementById('cliente').textContent = data.venta.cliente ?? 'N/A';
                document.getElementById('factura').textContent = data.venta.factura ?? 'N/A';

                document.getElementById('serie_id').value = data.serie.id;
                document.getElementById('producto_id').value = data.producto.id;
                document.getElementById('almacen_id').value = data.serie.almacen_id;
            });
        </script>
    @endpush
</x-app-layout>
