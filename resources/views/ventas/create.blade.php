<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-2xl text-accent text-center">Registrar Venta de Producto</h2>
    </x-slot>

    <x-alert />

    <div class="p-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div
                class="bg-gradient-to-br from-primary-dark to-primary-soft border-4 border-primary shadow-2xl rounded-lg p-10">
                <form method="POST" action="{{ route('ventas.store') }}" id="form-venta" class="mt-6 space-y-4">
                    @csrf

                    {{-- 1) Selección de almacén --}}
                    <div>
                        <label class="text-white">Almacén</label>
                        <select name="almacen_id" id="almacen_id"
                            class="w-full border-2 border-primary-light rounded-lg bg-primary-bg text-white placeholder-gray-400 px-4 py-2 focus:border-primary focus:ring-2 focus:ring-primary transition-all duration-200"
                            required>
                            <option value="">-- Selecciona --</option>
                            @foreach ($almacenes as $almacen)
                                <option value="{{ $almacen->id }}">{{ $almacen->nombre }}</option>
                            @endforeach
                        </select>
                    </div>

                    {{-- 2) Input de búsqueda --}}
                    <div class="flex flex-col items-center">
                        <label class="text-white w-full">Buscar producto (código, modelo, lote, serie):</label>
                        <div class="md:flex items-center gap-2 w-full">
                            <input id="buscador" type="text"
                                class="w-full border-2 border-primary-light rounded-xl bg-primary-bg text-white placeholder-gray-400 px-4 py-2 focus:border-primary focus:ring-2 focus:ring-primary transition-all duration-200"
                                autocomplete="off">
                            <button type="button" id="buscarBtn"
                                class="bg-primary text-white hover:bg-accent hover:scale-105 active:scale-95 transition-all duration-200 px-4 py-2 rounded-md shadow-xl">Buscar</button>
                        </div>
                    </div>

                    {{-- 3) Tabla de selección --}}
                    <div class="mt-6">
                        <table class="min-w-full divide-y divide-primary-soft" id="tablaSeleccion">
                            <thead class="bg-primary-dark">
                                <tr>
                                    <th
                                        class="px-6 py-3 text-center text-sm font-medium text-white uppercase tracking-wider">
                                        Tipo</th>
                                    <th
                                        class="px-6 py-3 text-center text-sm font-medium text-white uppercase tracking-wider">
                                        Producto</th>
                                    <th
                                        class="px-6 py-3 text-center text-sm font-medium text-white uppercase tracking-wider">
                                        Lote</th>
                                    <th
                                        class="px-6 py-3 text-center text-sm font-medium text-white uppercase tracking-wider">
                                        Serie</th>
                                    <th
                                        class="px-6 py-3 text-center text-sm font-medium text-white uppercase tracking-wider">
                                        Cantidad</th>
                                    <th
                                        class="px-6 py-3 text-center text-sm font-medium text-white uppercase tracking-wider">
                                        Acción</th>
                                </tr>
                            </thead>
                            <tbody class="bg-primary-soft">
                                <!-- Aquí se agregan los ítems seleccionados -->
                            </tbody>
                        </table>
                    </div>

                    <input type="hidden" name="detalle_venta" id="detalle_venta">

                    <button type="submit"
                        class="bg-primary-bg text-white hover:bg-accent-bright hover:scale-105 active:scale-95 transition-all duration-200 px-6 py-2 rounded-lg">Registrar
                        Venta</button>
                </form>

                {{-- Modal de selección múltiple --}}
                <div id="modalSeleccion"
                    class="fixed inset-0 bg-primary-bg/70 hidden flex items-center justify-center z-50 transition-opacity duration-300">
                    <div id="modalBox"
                        class="bg-gradient-to-br from-primary-dark to-primary-soft rounded-2xl shadow-2xl w-full max-w-lg p-6 transform scale-95 opacity-0 transition-all duration-300 border-4 border-[#1EA7FD]">
                        <h2 class="text-2xl font-bold text-white mb-4" id="modalTitulo"></h2>
                        <div id="modalCuerpo" class="text-white"></div>
                        <div class="text-right mt-4 flex gap-2 justify-end">
                            <button id="cerrarModal"
                                class="text-[#1EA7FD] bg-white hover:bg-primary hover:text-white transition-transform duration-200 px-5 py-2 rounded-lg shadow-sm transform hover:scale-105 active:scale-95">
                                Cancelar
                            </button>
                            <button id="agregarSeleccion"
                                class="bg-primary text-white hover:bg-accent hover:scale-105 active:scale-95 transition-all duration-200 px-6 py-2 rounded-lg shadow-xl opacity-60 cursor-not-allowed"
                                disabled>
                                Agregar
                            </button>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            let seleccionTemporal = [];

            // Referencias a los elementos
            const buscador = document.getElementById('buscador');
            const buscarBtn = document.getElementById('buscarBtn');
            const almacenSelect = document.getElementById('almacen_id');
            const modal = document.getElementById('modalSeleccion');
            const modalBox = document.getElementById('modalBox');
            const modalTitulo = document.getElementById('modalTitulo');
            const modalCuerpo = document.getElementById('modalCuerpo');
            const cerrarModalBtn = document.getElementById('cerrarModal');
            const agregarSelBtn = document.getElementById('agregarSeleccion');
            const tablaTbody = document.querySelector('#tablaSeleccion tbody');
            const detalleVenta = document.getElementById('detalle_venta');
            const formVenta = document.getElementById('form-venta');

            buscarBtn.addEventListener('click', buscarProducto);
            cerrarModalBtn.addEventListener('click', cerrarModal);
            agregarSelBtn.addEventListener('click', agregarASeleccion);
            formVenta.addEventListener('submit', prepararEnvio);

            // Permite cerrar modal haciendo click en fondo oscuro
            modal.addEventListener('click', function(e) {
                if (e.target === this) cerrarModal();
            });

            function mostrarModal() {
                modal.classList.remove('hidden');
                setTimeout(() => {
                    modalBox.classList.remove('scale-95', 'opacity-0');
                    modalBox.classList.add('scale-100', 'opacity-100');
                }, 10);
            }

            function cerrarModal() {
                modalBox.classList.remove('scale-100', 'opacity-100');
                modalBox.classList.add('scale-95', 'opacity-0');
                setTimeout(() => {
                    modal.classList.add('hidden');
                }, 300);
            }

            function buscarProducto() {
                let filtro = buscador.value.trim();
                let almacen_id = almacenSelect.value;
                if (!filtro) return alert('Ingresa un valor de búsqueda.');
                if (!almacen_id) return alert('Selecciona el almacén.');

                fetch(`/venta/buscar?filtro=${encodeURIComponent(filtro)}&almacen_id=${almacen_id}`)
                    .then(res => res.json())
                    .then(data => mostrarResultados(data))
                    .catch(() => alert('Error al buscar.'));
            }

            function mostrarResultados(data) {
                if (data.tipo === 'producto') {
                    modalTitulo.textContent = `Selección de coincidencias: ${data.producto.nombre}`;
                    let html = '<strong>Lotes disponibles:</strong><br>';
                    data.lotes.forEach(lote => {
                        if (lote.disponibles > 0) {
                            html += `<div>
                                <input type="radio" name="opcion" value="lote_${lote.id}" data-tipo="lote" data-id="${lote.id}" data-producto="${data.producto.id}" data-cantidad="${lote.disponibles}" data-nombre_producto="${data.producto.nombre}" data-numero_lote="${lote.numero_lote}">
                                Lote: ${lote.numero_lote} (${lote.disponibles} disponibles)
                            </div>`;
                        }
                    });
                    html += '<hr><strong>Series disponibles:</strong><br>';
                    data.series.forEach(serie => {
                        html += `<div>
                            <input type="checkbox" name="series" value="${serie.id}" data-tipo="serie" data-id="${serie.id}" data-producto="${data.producto.id}" data-lote="${serie.lote_id}" data-nombre_producto="${data.producto.nombre}" data-numero_lote="${serie.lote ? serie.lote.numero_lote : ''}" data-numero_serie="${serie.numero_serie}">
                            Serie: ${serie.numero_serie}
                        </div>`;
                    });
                    modalCuerpo.innerHTML = html;
                    chequearSeleccionModal();
                    mostrarModal();
                } else if (data.tipo === 'lote') {
                    modalTitulo.textContent = `Selecciona series del lote: ${data.lote.numero_lote}`;
                    let html = '<button type="button hover:bg-accent hover:scale-105 active:scale-95 transition-all duration-200 px-4 py-2 rounded-md" id="seleccionarTodas">Seleccionar todo</button><br>';
                    data.series.forEach(serie => {
                        html += `<div>
                            <input type="checkbox" name="series" value="${serie.id}" data-tipo="serie" data-id="${serie.id}" data-producto="${serie.producto_id}" data-lote="${serie.lote_id}" data-numero_serie="${serie.numero_serie}">
                            Serie: ${serie.numero_serie}
                        </div>`;
                    });
                    modalCuerpo.innerHTML = html;
                    chequearSeleccionModal();
                    mostrarModal();
                    setTimeout(() => {
                        const btnSelTodas = document.getElementById('seleccionarTodas');
                        if (btnSelTodas) {
                            btnSelTodas.onclick = () => {
                                document.querySelectorAll('#modalCuerpo input[type=checkbox]').forEach(cb => cb
                                    .checked = true);
                                chequearSeleccionModal();
                            };
                        }
                    }, 50);
                } else if (data.tipo === 'serie') {
                    // Agrega directo
                    seleccionTemporal.push({
                        tipo: 'serie',
                        producto_id: data.serie.producto_id,
                        lote_id: data.serie.lote_id,
                        serie_id: data.serie.id,
                        cantidad: 1,
                        nombre_producto: data.serie.producto ? data.serie.producto.nombre : '',
                        numero_lote: data.serie.lote ? data.serie.lote.numero_lote : '',
                        numero_serie: data.serie.numero_serie
                    });
                    renderTablaSeleccion();
                    alert('Serie agregada directamente');
                } else {
                    alert(data.message);
                }
            }
            modalCuerpo.addEventListener('change', chequearSeleccionModal);
            modalCuerpo.addEventListener('input', chequearSeleccionModal);

            function agregarASeleccion() {
                let radios = document.querySelectorAll('#modalCuerpo input[type=radio]:checked');
                let checkboxes = document.querySelectorAll('#modalCuerpo input[type=checkbox]:checked');
                if (radios.length) {
                    let radio = radios[0];
                    seleccionTemporal.push({
                        tipo: 'lote',
                        producto_id: radio.dataset.producto,
                        lote_id: radio.dataset.id,
                        cantidad: radio.dataset.cantidad,
                        nombre_producto: radio.dataset.nombre_producto,
                        numero_lote: radio.dataset.numero_lote,
                        numero_serie: ''
                    });
                }
                checkboxes.forEach(cb => {
                    seleccionTemporal.push({
                        tipo: 'serie',
                        producto_id: cb.dataset.producto,
                        lote_id: cb.dataset.lote,
                        serie_id: cb.dataset.id,
                        cantidad: 1,
                        nombre_producto: cb.dataset.nombre_producto,
                        numero_lote: cb.dataset.numero_lote,
                        numero_serie: cb.dataset.numero_serie
                    });
                });
                cerrarModal();
                renderTablaSeleccion();
            }

            function renderTablaSeleccion() {
                tablaTbody.innerHTML = '';
                seleccionTemporal.forEach((item, idx) => {
                    tablaTbody.innerHTML += `
                        <tr>
                            <td class="px-4 py-2 text-center capitalize">${item.tipo}</td>
                            <td class="px-4 py-2 text-center capitalize">${item.nombre_producto || ''}</td>
                            <td class="px-4 py-2 text-center capitalize">${item.numero_lote || ''}</td>
                            <td class="px-4 py-2 text-center">${item.numero_serie || 'Total'}</td>
                            <td class="px-4 py-2 text-center">${item.cantidad}</td>
                            <td class="px-4 py-2 text-center">
                                <button type="button"
                                    class="text-red-600 transition-all duration-200 hover:scale-110 active:scale-90"
                                    onclick="eliminarDeSeleccion(${idx})">Eliminar</button>
                            </td>
                        </tr>
                    `;
                });
                // Actualiza input hidden
                detalleVenta.value = JSON.stringify(seleccionTemporal);
            }

            // Para poder borrar usando botón eliminar
            window.eliminarDeSeleccion = function(idx) {
                seleccionTemporal.splice(idx, 1);
                renderTablaSeleccion();
            }

            // Antes de enviar el form, guardamos la selección
            function prepararEnvio(e) {
                if (!seleccionTemporal.length) {
                    e.preventDefault();
                    alert('Agrega al menos un producto/serie/lote a la venta.');
                    return false;
                }
                detalleVenta.value = JSON.stringify(seleccionTemporal);
            }

            // Habilitar o bloquear botón "Agregar"
            function chequearSeleccionModal() {
                let algoSeleccionado = document.querySelectorAll(
                    '#modalCuerpo input[type=radio]:checked, #modalCuerpo input[type=checkbox]:checked').length > 0;
                agregarSelBtn.disabled = !algoSeleccionado;
                if (algoSeleccionado) {
                    agregarSelBtn.classList.remove('opacity-60', 'cursor-not-allowed');
                } else {
                    agregarSelBtn.classList.add('opacity-60', 'cursor-not-allowed');
                }
            }
        </script>
    @endpush
</x-app-layout>
