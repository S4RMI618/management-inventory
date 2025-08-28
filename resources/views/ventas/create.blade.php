<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-2xl text-accent text-center">Registrar Venta de Producto</h2>
    </x-slot>

    <x-alert />
    <div class="md:p-4">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div
                class="bg-gradient-to-br from-primary-dark to-primary-soft border-4 border-primary shadow-2xl rounded-lg p-4 md:p-10">
                <form method="POST" action="{{ route('ventas.store') }}" id="form-venta"
                    class="grid md:grid-cols-2 md:gap-4 items-center mt-6 md:space-y-4">
                    @csrf

                    {{-- 1) Selección de almacén --}}
                    <div class="mb-4">
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
                    {{-- 2) Input de búsqueda de cliente --}}
                    <div class="mb-4 relative">
                        <label class="text-white">Cliente</label>
                        <input type="text" id="cliente_buscador"
                            class="w-full border-2 border-primary-light rounded-lg bg-primary-bg text-white placeholder-gray-400 px-4 py-2"
                            placeholder="Buscar por nombre o documento..." autocomplete="off">
                        <input type="hidden" name="socio_comercial_id" id="socio_comercial_id">
                        <div id="sugerenciasCliente"
                            class="absolute left-0 right-0 mt-1 z-50 bg-primary-dark rounded-lg shadow-xl text-white hidden">
                        </div>
                    </div>


                    {{-- 2) Input de búsqueda --}}
                    <div class="w-full flex flex-col items-center col-span-2">
                        <label class="text-white w-full">Buscar producto (código, modelo, lote, serie):</label>
                        <div class="grid items-stretch place-items-center md:grid-cols-4 gap-4 w-full">
                            <input id="buscador" type="text"
                                class="col-span-3 w-full border-2 border-primary-light rounded-xl bg-primary-bg text-white placeholder-gray-400 px-4 py-2 focus:border-primary focus:ring-2 focus:ring-primary transition-all duration-200"
                                autocomplete="off">
                            <button type="button" id="buscarBtn"
                                class="col-span-3 md:col-span-1 w-full bg-primary text-white hover:bg-accent hover:scale-105 active:scale-95 transition-all duration-200 px-4 py-2 rounded-md shadow-xl">Buscar</button>
                        </div>
                    </div>

                    <div class="mt-6 col-span-2 mb-6">
                        <!-- Encabezado -->
                        <div
                            class="grid grid-cols-8 md:grid-cols-7 bg-primary-dark text-white rounded-t-xl font-bold text-center py-2 place-items-center">
                            <div>#</div>
                            <div class="col-span-3 text-left">Descripción</div>
                            <div class="hidden md:block">Cantidad</div>
                            <div class="md:hidden block">Cant</div>
                            <div class="hidden md:block">Precio Unitario</div>
                            <div class="md:hidden block col-span-2 md:col-span-1">$</div>
                            <div class="flex items-center justify-center gap-2">
                                <button id="btnEliminarSeleccionados" type="button"
                                    class="ml-2 text-red-400 hover:text-red-700 text-xl px-2 py-0 rounded transition-all duration-150"
                                    title="Eliminar seleccionados">
                                    &#10006;
                                </button>
                            </div>
                        </div>
                        <div id="productosSeleccionadosGrid"></div>
                        <div class="grid grid-cols-2">
                            <div class="text-white bg-primary-dark p-2 rounded-xl mt-2">
                                Total Productos/Series: <span id="totalItems"
                                    class="font-bold">{{ count($productosSeleccionados ?? []) }}</span>
                            </div>
                        </div>
                    </div>

                    <div class="col-span-2 mb-4">
                        <label class="text-white">Referencia Externa</label>
                        <input type="text" id="referencia_externa" name="referencia_externa"
                            class="w-full border-2 border-primary-light rounded-lg bg-primary-bg text-white placeholder-gray-400 px-4 py-2"
                            placeholder="" autocomplete="off">
                    </div>

                    <input type="hidden" name="detalle_venta" id="detalle_venta">

                    <button type="submit"
                        class="col-span-2 justify-self-center bg-primary-bg text-white hover:bg-accent-bright hover:scale-105 active:scale-95 transition-all duration-200 px-6 py-2 rounded-lg">
                        Registrar Venta
                    </button>
                </form>

                {{-- Modal de selección múltiple --}}
                <div id="modalSeleccion"
                    class="fixed inset-0 z-50 hidden bg-primary-bg/80 backdrop-blur-sm transition-opacity duration-300 flex items-center justify-center">
                    <div id="modalBox"
                        class="bg-gradient-to-br from-primary-dark to-primary-soft rounded-2xl shadow-xl border-t-4 border-primary w-full max-w-2xl p-6 transform scale-95 opacity-0 transition-all duration-300">

                        <!-- Encabezado -->
                        <div class="flex items-center justify-between mb-4 border-b border-primary-light pb-3">
                            <h2 id="modalTitulo" class="text-2xl font-bold text-white">Seleccionar Producto</h2>
                            <button id="cerrarModal"
                                class="text-white hover:text-accent-bright transition-all text-2xl font-light">
                                &times;
                            </button>
                        </div>

                        <!-- Cuerpo -->
                        <div id="modalCuerpo"
                            class="text-white max-h-[60vh] overflow-y-auto space-y-2 text-sm md:text-base px-1">
                            <!-- Radios o checkboxes dinámicos aquí -->
                        </div>

                        <!-- Footer -->
                        <div class="flex justify-end mt-6 gap-3 pt-4 border-t border-primary-light">
                            <button id="cerrarModal"
                                class="bg-primary-soft text-white hover:bg-primary-dark px-5 py-2 rounded-lg transition-all duration-200">
                                Cancelar
                            </button>
                            <button id="agregarSeleccion"
                                class="bg-accent text-primary-dark hover:bg-accent-bright hover:scale-105 active:scale-95 transition-all duration-200 px-6 py-2 rounded-xl shadow-xl opacity-60 cursor-not-allowed"
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
            // -------------------------------
            // LISTA GLOBAL PERSISTENTE
            let productosSeleccionados = JSON.parse(localStorage.getItem('productosSeleccionados')) || [];

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
            const clienteBuscador = document.getElementById('cliente_buscador');
            const sugerenciasCliente = document.getElementById('sugerenciasCliente');
            const socioComercialId = document.getElementById('socio_comercial_id');

            let clientesSugeridos = [];
            let sugerenciaActiva = -1;

            clienteBuscador.addEventListener('input', function() {
                const q = clienteBuscador.value.trim();
                socioComercialId.value = '';
                sugerenciaActiva = -1;
                if (q.length < 2) {
                    sugerenciasCliente.classList.add('hidden');
                    sugerenciasCliente.innerHTML = '';
                    return;
                }
                fetch(`/clientes/buscar?q=${encodeURIComponent(q)}`)
                    .then(res => res.json())
                    .then(data => {
                        clientesSugeridos = data;
                        sugerenciaActiva = 0;
                        renderSugerencias();
                    });
            });

            function renderSugerencias() {
                if (clientesSugeridos.length === 0) {
                    sugerenciasCliente.innerHTML = '<div class="px-4 py-2 text-gray-400">Sin resultados</div>';
                    sugerenciasCliente.classList.remove('hidden');
                    return;
                }
                sugerenciasCliente.innerHTML = clientesSugeridos.map((c, idx) =>
                    `<div class="px-4 py-2 hover:bg-primary-soft cursor-pointer rounded-lg ${sugerenciaActiva === idx ? 'bg-accent-bright text-primary-dark' : ''}"
            data-idx="${idx}" data-id="${c.id}" data-nombre="${c.nombre}" data-documento="${c.documento}">
            <span class="font-semibold">${c.nombre}</span> - <span class="text-sm">${c.documento}</span>
        </div>`
                ).join('');
                sugerenciasCliente.classList.remove('hidden');
            }

            // Click en sugerencia
            sugerenciasCliente.addEventListener('click', function(e) {
                const item = e.target.closest('[data-idx]');
                if (item) {
                    seleccionarClienteSugerencia(Number(item.dataset.idx));
                }
            });

            // Navegación con teclado
            clienteBuscador.addEventListener('keydown', function(e) {
                if (sugerenciasCliente.classList.contains('hidden') || clientesSugeridos.length === 0) return;

                if (e.key === "ArrowDown") {
                    e.preventDefault();
                    sugerenciaActiva = (sugerenciaActiva + 1) % clientesSugeridos.length;
                    renderSugerencias();
                } else if (e.key === "ArrowUp") {
                    e.preventDefault();
                    sugerenciaActiva = (sugerenciaActiva - 1 + clientesSugeridos.length) % clientesSugeridos.length;
                    renderSugerencias();
                } else if (e.key === "Enter") {
                    e.preventDefault();
                    if (sugerenciaActiva >= 0 && sugerenciaActiva < clientesSugeridos.length) {
                        seleccionarClienteSugerencia(sugerenciaActiva);
                    }
                }
            });

            function seleccionarClienteSugerencia(idx) {
                const cliente = clientesSugeridos[idx];
                socioComercialId.value = cliente.id;
                clienteBuscador.value = `${cliente.nombre} - ${cliente.documento}`;
                sugerenciasCliente.classList.add('hidden');
                sugerenciasCliente.innerHTML = '';
                clientesSugeridos = [];
                sugerenciaActiva = -1;
            }

            // Ocultar sugerencias si haces click fuera
            document.addEventListener('click', function(e) {
                if (!clienteBuscador.contains(e.target) && !sugerenciasCliente.contains(e.target)) {
                    sugerenciasCliente.classList.add('hidden');
                }
            });

            // -------------------------------
            // FUNCIONES DE PERSISTENCIA Y UTILIDAD

            function guardarLista() {
                localStorage.setItem('productosSeleccionados', JSON.stringify(productosSeleccionados));
                detalleVenta.value = JSON.stringify(productosSeleccionados);
            }

            function agregarProducto(obj) {
                // Prevenir duplicados por serie
                if (obj.serie_id) {
                    if (productosSeleccionados.some(p => p.serie_id == obj.serie_id)) {
                        alert('Esta serie ya está agregada.');
                        return;
                    }
                }
                // Prevenir duplicados por lote y producto
                if (obj.tipo === "lote" && obj.lote_id) {
                    if (productosSeleccionados.some(p => p.tipo === "lote" && p.lote_id == obj.lote_id && p.producto_id == obj
                            .producto_id)) {
                        alert('Este lote ya fue agregado.');
                        return;
                    }
                }
                productosSeleccionados.push(obj);
                guardarLista();
                renderizarTabla();
            }

            function eliminarProducto(idx) {
                productosSeleccionados.splice(idx, 1);
                guardarLista();
                renderizarTabla();
            }

            function editarCantidad(idx, valor) {
                valor = parseInt(valor);
                if (isNaN(valor) || valor < 1) valor = 1;
                productosSeleccionados[idx].cantidad = valor;
                guardarLista();
                renderizarTabla();
            }

            // -------------------------------
            // FUNCIÓN UNIVERSAL PARA ARMAR EL OBJETO COMPLETO

            function construirProductoSeleccionado({
                producto = {},
                lote = {},
                serie = {},
                tipo = "",
                cantidad = 1,
                almacen = {}
            }) {
                return {
                    // Datos del producto
                    producto_id: producto.id || null,
                    codigo: producto.codigo || "",
                    nombre_producto: producto.nombre || "",
                    modelo: producto.modelo || "",
                    marca_id: producto.marca_id || null,
                    marca_nombre: producto.marca?.nombre || "",
                    categoria_id: producto.categoria_id || null,
                    categoria_nombre: producto.categoria?.nombre || "",
                    precio_costo: producto.precio_costo || null,
                    precio_venta: producto.precio_venta || null,
                    ubicacion: producto.ubicacion || "",
                    estado_producto: producto.estado || "",
                    tiene_invima: producto.tiene_invima || false,
                    tiene_series: producto.tiene_series || false,
                    cantidad_minima: producto.cantidad_minima || null,
                    cantidad_maxima: producto.cantidad_maxima || null,

                    // Lote (si aplica)
                    lote_id: lote.id || null,
                    numero_lote: lote.numero_lote || "",
                    fecha_fabricacion: lote.fecha_fabricacion || "",
                    fecha_vencimiento: lote.fecha_vencimiento || "",
                    estado_lote: lote.estado || "",
                    tiene_invima_lote: lote.tiene_invima || false,

                    // Serie (si aplica)
                    serie_id: serie.id || null,
                    numero_serie: serie.numero_serie || "",
                    estado_serie: serie.estado || "",
                    almacen_id: serie.almacen_id || almacen?.id || null,
                    almacen_nombre: (serie.almacen?.nombre || almacen?.nombre) || "",

                    // Control de cantidad y tipo de agregación
                    tipo: tipo, // 'lote', 'serie', 'producto'
                    cantidad: cantidad
                };
            }

            // -------------------------------
            // RENDERIZADO DE TABLA

            function renderizarTabla() {
                const grid = document.getElementById('productosSeleccionadosGrid');
                grid.innerHTML = '';

                if (!productosSeleccionados.length) {
                    grid.innerHTML =
                        `<div class="grid grid-cols-8 md:grid-cols-7 min-w-[750px] items-center border-b border-primary-soft text-white py-3 px-2">
           <div class="col-span-8 text-center text-gray-400">No Se Han Seleccionado Productos</div>
         </div>`;
                    return;
                }
                productosSeleccionados.forEach((item, idx) => {
                    // Series del lote o serie individual
                    let series = '';
                    if (item.tipo === 'lote' && item.series && item.series.length) {
                        series = item.series.map(s => s.numero_serie).join(', ');
                    } else if (item.tipo === 'serie') {
                        series = item.numero_serie;
                    }

                    // Descripción
                    let descripcion =
                        `<span class="font-semibold">${item.nombre_producto || ''} - ${item.codigo || ''}</span>`;
                    if (item.tipo === 'lote') {
                        descripcion +=
                            `<br><span class="inline-block bg-primary text-xs px-2 py-1 rounded mr-2 mt-1">Lote: ${item.numero_lote || '-'}</span>`;
                        if (series) {
                            descripcion += `<br><span class="text-xs text-gray-300">${series}</span>`;
                        }
                    } else if (item.tipo === 'serie') {
                        descripcion +=
                            `<br><span class="inline-block bg-primary-soft text-xs px-2 py-1 rounded mt-1">Serie: ${item.numero_serie || '-'}</span>`;
                    }

                    grid.innerHTML += `
            <div class="grid grid-cols-8 md:grid-cols-7 items-center border-b text-white border-primary-soft py-2 hover:bg-primary-dark/40 transition cursor-pointer group" data-idx="${idx}">
                <div class="text-center">${idx + 1}</div>
                <div class="col-span-3 pl-2">${descripcion}</div>
                <div class="text-center">
                    ${
                        item.tipo === 'lote'
                        ? `<input type="number" min="1" value="${item.cantidad}" class="w-14 text-center bg-primary-soft text-white rounded-md p-1 cantidad-edit" data-idx="${idx}">`
                        : `1`
                    }
                </div>
                <div class="text-center col-span-2 md:col-span-1">$${Number(item.precio_venta || 0).toLocaleString()}</div>
                <div class="flex items-center justify-center pl-2">
                    <input type="checkbox" class="checkbox-borrar" data-idx="${idx}">
                </div>
            </div>
        `;
                });
                detalleVenta.value = JSON.stringify(productosSeleccionados);
            }

            // Eliminar todos los productos seleccionados
            document.addEventListener('click', function(e) {
                if (e.target && e.target.id === 'btnEliminarSeleccionados') {
                    const checkboxes = document.querySelectorAll('.checkbox-borrar:checked');
                    if (!checkboxes.length) return; // No hacer nada si ninguno seleccionado

                    // Borra de mayor a menor índice para evitar bugs
                    const indices = Array.from(checkboxes).map(cb => Number(cb.dataset.idx)).sort((a, b) => b - a);
                    indices.forEach(idx => {
                        productosSeleccionados.splice(idx, 1);
                    });
                    guardarLista();
                    renderizarTabla();
                }
            });
            document.addEventListener('input', function(e) {
                if (e.target && e.target.classList.contains('cantidad-edit')) {
                    const idx = e.target.dataset.idx;
                    let val = parseInt(e.target.value);
                    if (isNaN(val) || val < 1) val = 1;
                    productosSeleccionados[idx].cantidad = val;
                    guardarLista();
                    // Opcional: renderizar de nuevo si necesitas actualizar algo más
                }
            });
            document.addEventListener('click', function(e) {
                const fila = e.target.closest('.group[data-idx]');
                if (fila) {
                    const idx = fila.dataset.idx;
                    abrirModalEdicionSeries(idx);
                }
            });


            // -------------------------------
            // MODAL FUNCIONALIDAD

            buscarBtn.addEventListener('click', buscarProducto);
            cerrarModalBtn.addEventListener('click', cerrarModal);
            agregarSelBtn.addEventListener('click', agregarASeleccion);
            formVenta.addEventListener('submit', prepararEnvio);

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

            // Guarda la última respuesta para tener contexto dentro de agregarASeleccion
            let ultimaRespuesta = {};

            function mostrarResultados(data) {
                ultimaRespuesta = data;
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
                    let html =
                        '<button type="button hover:bg-accent hover:scale-105 active:scale-95 transition-all duration-200 px-4 py-2 rounded-md" id="seleccionarTodas">Seleccionar todo</button><br>';
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
                    const obj = construirProductoSeleccionado({
                        producto: data.serie.producto || {},
                        lote: data.serie.lote || {},
                        serie: data.serie,
                        tipo: 'serie',
                        cantidad: 1,
                        almacen: data.serie.almacen || {}
                    });
                    agregarProducto(obj);
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

                // Si es un lote seleccionado
                if (radios.length) {
                    let radio = radios[0];
                    // Busca el lote real en tu respuesta guardada
                    const lote = ultimaRespuesta.lotes.find(l => l.id == radio.dataset.id) || {};
                    const obj = construirProductoSeleccionado({
                        producto: ultimaRespuesta.producto,
                        lote: lote,
                        tipo: 'lote',
                        cantidad: radio.dataset.cantidad
                    });
                    agregarProducto(obj);
                }

                // Si son series seleccionadas
                checkboxes.forEach(cb => {
                    // Busca la serie real en tu respuesta guardada
                    const serie = ultimaRespuesta.series.find(s => s.id == cb.value || s.id == cb.dataset.id) || {};
                    const obj = construirProductoSeleccionado({
                        producto: ultimaRespuesta.producto,
                        lote: serie.lote || {},
                        serie: serie,
                        tipo: 'serie',
                        cantidad: 1,
                        almacen: serie.almacen || {}
                    });
                    agregarProducto(obj);
                });

                cerrarModal();
            }

            function prepararEnvio(e) {
                if (!productosSeleccionados.length) {
                    e.preventDefault();
                    alert('Agrega al menos un producto/serie/lote a la venta.');
                    return false;
                }
                detalleVenta.value = JSON.stringify(productosSeleccionados);
                localStorage.removeItem('productosSeleccionados');
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

            buscador.addEventListener('keydown', function(e) {
                if (e.key === "Enter" && document.activeElement === buscador) {
                    e.preventDefault(); // Prevenir el comportamiento por defecto
                    buscarProducto(); // Ejecutar la búsqueda
                }
            });

            // Función para manejar la navegación con Enter
            function manejarNavegacionEnter(e) {
                if (e.key === "Enter") {
                    e.preventDefault();

                    // Obtenemos todos los campos navegables en orden
                    const campos = [
                        document.getElementById('almacen_id'),
                        document.getElementById('cliente_buscador'),
                        document.getElementById('buscador')
                    ];

                    // Encontramos el índice del campo actual
                    const currentIndex = campos.indexOf(e.target);

                    // Si encontramos el campo y no es el último, movemos el foco al siguiente
                    if (currentIndex > -1 && currentIndex < campos.length - 1) {
                        campos[currentIndex + 1].focus();
                    } else if (currentIndex === campos.length - 1) {
                        // Si es el último campo (el buscador), ejecutamos la búsqueda
                        buscarProducto();
                    }
                }
            }

            // Agregamos los event listeners a los campos
            document.getElementById('almacen_id').addEventListener('keydown', manejarNavegacionEnter);
            document.getElementById('cliente_buscador').addEventListener('keydown', manejarNavegacionEnter);
            document.getElementById('buscador').addEventListener('keydown', manejarNavegacionEnter);
            // -------------------------------
            // LIMPIAR SELECCIÓN TRAS VENTA EXITOSA
            document.addEventListener('DOMContentLoaded', () => {
                @if (session('venta_exitosa'))
                    localStorage.removeItem('productosSeleccionados');
                    productosSeleccionados = [];
                    renderizarTabla();
                @else
                    productosSeleccionados = JSON.parse(localStorage.getItem('productosSeleccionados')) || [];
                    renderizarTabla();
                @endif
            });
            // Exponer eliminarProducto y editarCantidad para uso en el HTML
            window.eliminarProducto = eliminarProducto;
            window.editarCantidad = editarCantidad;
        </script>
    @endpush
</x-app-layout>
