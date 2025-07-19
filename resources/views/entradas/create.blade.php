<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-2xl text-accent text-center">Registrar Entrada de Producto</h2>
    </x-slot>

    {{-- Alertas (éxito, error o validación) --}}
    <x-alert />

    <div class="p-4">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div
                class="bg-gradient-to-br from-primary-dark to-primary-soft border-4 border-primary shadow-2xl rounded-lg p-10">
                <form method="POST" action="{{ route('entradas.store') }}" class="mt-6 space-y-4">
                    @csrf

                    <!-- 1) Buscador de producto -->
                    <div class="relative gap-2 space-y-2">
                        <label class="text-white">Buscar producto (nombre, modelo o código)</label>
                        <input id="producto-buscar" type="text"
                            class="w-full border-2 border-primary-light rounded-xl bg-[#181F32] text-white placeholder-gray-400 px-4 py-2 focus:border-primary focus:ring-2 focus:ring-primary transition-all duration-200"
                            autocomplete="off" value="">
                        <div id="resultado-producto"
                            class="absolute left-0 right-0 border bg-white mt-1 rounded hidden max-h-60 overflow-auto z-50">
                        </div>
                        <input type="hidden" name="producto_id" id="producto-id" value="{{ old('producto_id') }}">
                    </div>

                    <!-- 2) Mostrar producto seleccionado -->
                    <div id="producto-seleccionado"
                        class="p-2 border-2 border-primary-light rounded-xl {{ old('producto_id') ? '' : 'hidden' }} bg-[#181F32] text-white">
                        <strong>Producto:</strong>
                        <span id="producto-info"></span>
                    </div>

                    <!-- 3) Selección de almacén -->
                    <div class="gap-2 space-y-2">
                        <label class="text-white">Almacén</label>
                        <select name="almacen_id" id="almacen_id"
                            class="w-full border-2 border-primary-light rounded-xl bg-[#181F32] text-white px-4 py-2 focus:border-primary focus:ring-2 focus:ring-primary transition-all duration-200"
                            required>
                            <option value="">-- Selecciona --</option>
                            @foreach ($almacenes as $almacen)
                                <option value="{{ $almacen->id }}"
                                    {{ old('almacen_id') == $almacen->id ? 'selected' : '' }}>
                                    {{ $almacen->nombre }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- 4) Series ingresadas -->
                    <div class="gap-2 space-y-2">
                        <label class="text-white">Series (una por línea o separadas por espacio)</label>
                        <textarea name="series" rows="5"
                            class="w-full border-2 border-primary-light rounded-xl bg-[#181F32] text-white placeholder-gray-400 px-4 py-2 focus:border-primary focus:ring-2 focus:ring-primary transition-all duration-200"
                            id="series-input" placeholder="Escanea o escribe las series aquí...">{{ old('series') }}</textarea>
                    </div>

                    <!-- 5) Cálculo automático de cantidad -->
                    <div class="gap-2 space-y-2">
                        <label class="text-white">Cantidad</label>
                        <input type="number" name="cantidad" id="cantidad"
                            class="w-full border-2 border-primary-light rounded-xl bg-[#181F32] text-white placeholder-gray-400 px-4 py-2 focus:border-primary focus:ring-2 focus:ring-primary transition-all duration-200"
                            readonly
                            value="{{ old('series') ? count(preg_split('/[\s\n]+/', old('series'), -1, PREG_SPLIT_NO_EMPTY)) : '' }}">
                    </div>

                    <!-- 6) Número de lote -->
                    <div class="space-y-2">
                        <label class="text-white">Número de Lote</label>
                        <input type="text" name="numero_lote"
                            class="w-full border-2 border-primary-light rounded-xl bg-[#181F32] text-white placeholder-gray-400 px-4 py-2 focus:border-primary focus:ring-2 focus:ring-primary transition-all duration-200"
                            value="{{ old('numero_lote') }}">
                    </div>

                    <!-- 7) Fecha de fabricación -->
                    <div class="space-y-2">
                        <label class="text-white">Fecha de fabricación</label>
                        <input type="date" name="fecha_fabricacion" id="fecha_fabricacion"
                            class="w-full border-2 border-primary-light rounded-xl bg-[#181F32] text-white px-4 py-2 focus:border-primary focus:ring-2 focus:ring-primary transition-all duration-200"
                            value="{{ old('fecha_fabricacion') }}">
                    </div>

                    <!-- 8) Checkbox vencimiento -->
                    <div class="flex items-center space-x-2">
                        <input type="checkbox" name="tiene_vencimiento" id="tiene_vencimiento" class="h-4 w-4"
                            {{ old('tiene_vencimiento') ? 'checked' : '' }}>
                        <label for="tiene_vencimiento" class="text-white">¿El producto tiene vencimiento?</label>
                    </div>

                    <!-- 9) Fecha de vencimiento -->
                    <div class="space-y-2 {{ old('tiene_vencimiento') ? '' : 'hidden' }}" id="vencimiento-container">
                        <label class="text-white">Fecha de vencimiento</label>
                        <input type="date" name="fecha_vencimiento" id="fecha_vencimiento"
                            class="w-full border-2 border-primary-light rounded-xl bg-[#181F32] text-white px-4 py-2 focus:border-primary focus:ring-2 focus:ring-primary transition-all duration-200"
                            value="{{ old('fecha_vencimiento') }}">
                    </div>

                    <!-- 10) Checkbox INVIMA -->
                    <div class="flex items-center space-x-2">
                        <input type="checkbox" name="tiene_invima" id="tiene_invima" class="h-4 w-4"
                            {{ old('tiene_invima') ? 'checked' : '' }}>
                        <label for="tiene_invima" class="text-white">¿Tiene registro INVIMA?</label>
                    </div>

                    <button type="submit"
                        class="bg-primary text-white px-6 py-3 rounded-xl font-bold shadow-xl transition-all duration-200 hover:bg-accent hover:scale-105 active:scale-95">
                        Registrar Entrada
                    </button>
                </form>

                <!-- Modal de selección de producto -->
                <div id="modal-productos"
                    class="fixed inset-0 bg-gray-800 bg-opacity-50 hidden items-center justify-center z-50">
                    <div class="bg-white rounded shadow-lg w-full max-w-md p-4">
                        <h2 class="text-lg font-bold mb-4">Selecciona el producto correcto</h2>
                        <div id="lista-productos-modal" class="space-y-2"></div>
                        <div class="text-right mt-4">
                            <button onclick="cerrarModal()" class="text-red-600 px-4 py-2">Cancelar</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', () => {
                // Referencias
                const buscarInput = document.getElementById('producto-buscar');
                const resultadoBox = document.getElementById('resultado-producto');
                const productoIdInput = document.getElementById('producto-id');
                const productoInfo = document.getElementById('producto-info');
                const productoSel = document.getElementById('producto-seleccionado');
                const modal = document.getElementById('modal-productos');
                const listaModal = document.getElementById('lista-productos-modal');
                const seriesInput = document.getElementById('series-input');
                const cantidadInput = document.getElementById('cantidad');
                const chkVenc = document.getElementById('tiene_vencimiento');
                const vencContainer = document.getElementById('vencimiento-container');

                let productos = @json($productos);
                let opciones = [];
                let currentIndex = -1;

                // Funciones auxiliares
                function cerrarModal() {
                    modal.classList.add('hidden');
                }

                function seleccionarProducto(p) {
                    productoIdInput.value = p.id;
                    productoInfo.textContent =
                        `${p.nombre} (${p.modelo ?? '-'}) — ${p.marca?.nombre ?? 'Sin marca'} — ${p.codigo}`;
                    productoSel.classList.remove('hidden');
                    resultadoBox.classList.add('hidden');
                    resultadoBox.innerHTML = '';
                    opciones = [];
                    currentIndex = -1;
                    buscarInput.value = '';
                    document.querySelector('select[name="almacen_id"]').focus();
                }

                document.getElementById('series-input').addEventListener('keydown', e => {
                    if (e.key === 'Enter' && e.ctrlKey) {
                        e.preventDefault();
                        document.querySelector('[name="numero_lote"]').focus();
                    }
                });
                document.getElementById('almacen_id').addEventListener('keydown', e => {
                    if (e.key === 'Enter') {
                        e.preventDefault();
                        document.querySelector('[name="series"]').focus();
                    }
                });

                function limpiarPreseleccion() {
                    opciones.forEach(opt => opt.classList.remove('bg-gray-200'));
                }

                function actualizarPreseleccion() {
                    limpiarPreseleccion();
                    if (currentIndex >= 0 && currentIndex < opciones.length) {
                        opciones[currentIndex].classList.add('bg-gray-200');
                        opciones[currentIndex].scrollIntoView({
                            block: 'nearest'
                        });
                    }
                }

                function actualizarCantidad() {
                    const list = seriesInput.value
                        .split(/[\s\n]+/)
                        .filter(s => s.trim() !== '');
                    cantidadInput.value = list.length;
                }

                // 1) Inicializar selección desde old
                if (productoIdInput.value) {
                    const p = productos.find(x => x.id == productoIdInput.value);
                    if (p) {
                        // dibuja la selección sin cambiar foco
                        productoInfo.textContent =
                            `${p.nombre} (${p.modelo ?? '-'}) — ${p.marca?.nombre ?? 'Sin marca'} — ${p.codigo}`;
                        productoSel.classList.remove('hidden');
                    }
                }

                // 2) Inicializar cantidad
                actualizarCantidad();

                // 3) Toggle fecha de vencimiento
                chkVenc.addEventListener('change', () => {
                    if (chkVenc.checked) {
                        vencContainer.classList.remove('hidden');
                    } else {
                        vencContainer.classList.add('hidden');
                        document.getElementById('fecha_vencimiento').value = '';
                    }
                });

                // 4) Búsqueda, flechas y modal
                buscarInput.addEventListener('input', () => {
                    const txt = buscarInput.value.toLowerCase().trim();
                    resultadoBox.innerHTML = '';
                    resultadoBox.classList.add('hidden');
                    opciones = [];
                    currentIndex = -1;
                    if (txt.length < 3) return;

                    const filtrados = productos.filter(p =>
                        p.nombre.toLowerCase().includes(txt) ||
                        (p.modelo && p.modelo.toLowerCase().includes(txt)) ||
                        p.codigo.toLowerCase().includes(txt)
                    );
                    if (!filtrados.length) return;

                    resultadoBox.classList.remove('hidden');
                    filtrados.forEach((p, i) => {
                        const opt = document.createElement('div');
                        opt.classList.add('p-2', 'cursor-pointer', 'hover:bg-gray-100');
                        opt.textContent =
                            `${p.nombre} (${p.modelo ?? '-'}) — ${p.marca?.nombre ?? 'Sin marca'} — ${p.codigo}`;
                        opt.dataset.index = i;
                        opt.onclick = () => {
                            const mismos = filtrados.filter(x => x.nombre === p.nombre);
                            if (mismos.length > 1) {
                                listaModal.innerHTML = '';
                                mismos.forEach(m => {
                                    const item = document.createElement('div');
                                    item.classList.add('border-b', 'p-2', 'cursor-pointer');
                                    item.textContent =
                                        `${m.nombre} (${m.modelo ?? '-'}) — ${m.marca?.nombre ?? 'Sin marca'} — ${m.codigo}`;
                                    item.onclick = () => seleccionarProducto(m);
                                    listaModal.appendChild(item);
                                });
                                modal.classList.remove('hidden');
                            } else {
                                seleccionarProducto(p);
                            }
                        };
                        resultadoBox.appendChild(opt);
                    });
                    opciones = Array.from(resultadoBox.children);
                    if (opciones.length) {
                        currentIndex = 0;
                        actualizarPreseleccion();
                    }
                });
                buscarInput.addEventListener('keydown', e => {
                    if (!opciones.length || !['ArrowUp', 'ArrowDown', 'Enter'].includes(e.key)) return;
                    e.preventDefault();
                    if (e.key === 'ArrowDown') {
                        currentIndex = (currentIndex + 1) % opciones.length;
                        actualizarPreseleccion();
                    } else if (e.key === 'ArrowUp') {
                        currentIndex = (currentIndex - 1 + opciones.length) % opciones.length;
                        actualizarPreseleccion();
                    } else {
                        opciones[currentIndex].click();
                    }
                });

                // 5) Series → cantidad
                seriesInput.addEventListener('input', actualizarCantidad);
            });
        </script>
    @endpush
</x-app-layout>
