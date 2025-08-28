<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-2xl text-accent text-center">Registrar Entrada de Producto</h2>
    </x-slot>

    {{-- Alertas (éxito, error o validación) --}}
    <x-alert />

    <div class="md:p-4">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div
                class="bg-gradient-to-br from-primary-dark to-primary-soft border-4 border-primary shadow-2xl rounded-lg p-6 md:p-10">
                <form method="POST" action="{{ route('entradas.store') }}" class="mt-6 space-y-4" id="form-entrada">
                    @csrf

                    <!-- 1) Buscador de producto -->
                    <input type="hidden" name="tiene_invima" value="0">

                    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 items-center">
                        {{-- Buscador --}}
                        <div class="relative md:col-span-4 space-y-2">
                            <label class="text-white">Buscar producto (nombre, modelo o código)</label>
                            <input id="producto-buscar" type="text"
                                class="w-full h-12 border-2 border-primary-light rounded-lg bg-primary-dark text-white px-4 py-2 focus:border-primary focus:ring-primary transition-all"
                                autocomplete="off" value="">
                            <div id="resultado-producto"
                                class="absolute left-0 right-0 border bg-primary-dark mt-1 rounded hidden max-h-60 overflow-auto z-50">
                            </div>
                            <input type="hidden" name="producto_id" id="producto-id" value="{{ old('producto_id') }}">
                        </div>

                        {{-- Producto seleccionado --}}
                        <div id="producto-seleccionado"
                            class="p-2 md:col-span-4 border-2 border-primary-light rounded-lg bg-primary-bg text-white {{ old('producto_id') ? '' : 'hidden' }}">
                            <strong>Producto Seleccionado:</strong>
                            <span id="producto-info"></span>
                            <span id="badge-modo" class="hidden ml-2 text-xs px-2 py-1 rounded bg-primary-soft"></span>
                        </div>

                        {{-- Checkbox INVIMA --}}
                        <div class="flex items-center justify-center col-span-1 md:col-span-4 space-x-2">
                            <input type="checkbox" name="tiene_invima" id="tiene_invima" value="1"
                                class="h-4 w-4 rounded-full" {{ old('tiene_invima') == '1' ? 'checked' : '' }}>
                            <label for="tiene_invima" class="text-white">¿Tiene registro INVIMA?</label>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-5 gap-4 items-center">
                        {{-- Almacén --}}
                        <div class="gap-2 space-y-2 flex flex-col justify-between h-full">
                            <label class="text-white">Almacén</label>
                            <select name="almacen_id" id="almacen_id"
                                class="w-full border-2 border-primary-light rounded-lg bg-[#181F32] text-white px-4 py-2 focus:border-primary focus:ring-2 focus:ring-primary transition-all duration-200"
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

                        {{-- Número de lote --}}
                        <div class="space-y-2 flex flex-col justify-between h-full">
                            <label class="text-white">Número de Lote</label>
                            <input type="text" name="numero_lote"
                                class="w-full border-2 border-primary-light rounded-lg bg-[#181F32] text-white placeholder-gray-400 px-4 py-2 focus:border-primary focus:ring-2 focus:ring-primary transition-all duration-200"
                                value="{{ old('numero_lote') }}">
                        </div>

                        {{-- Fecha de fabricación --}}
                        <div class="space-y-2 flex flex-col justify-between h-full">
                            <label class="text-white">Fecha de fabricación</label>
                            <input type="date" name="fecha_fabricacion" id="fecha_fabricacion"
                                class="w-full h-12 border-2 border-primary-light rounded-lg bg-[#181F32] text-white px-4 py-2"
                                value="{{ old('fecha_fabricacion', date('Y-m-d')) }}">
                        </div>

                        {{-- Cantidad (editable solo si NO tiene series) --}}
                        <div class="gap-2 space-y-2 flex flex-col justify-between h-full">
                            <label class="text-white">Cantidad</label>
                            <input type="number" name="cantidad" id="cantidad"
                                class="w-full border-2 border-primary-light rounded-xl bg-[#181F32] text-white placeholder-gray-400 px-4 py-2 focus:border-primary focus:ring-2 focus:ring-primary transition-all duration-200"
                                readonly
                                value="{{ old('series') ? count(preg_split('/[\s\n]+/', old('series'), -1, PREG_SPLIT_NO_EMPTY)) : '' }}">
                        </div>
                        <div class="space-y-2 flex flex-col justify-between h-full">
                            <label class="text-white">Precio Costo Base (opcional)</label>
                            <input type="number" step="0.01" min="0" name="precio_costo_base"
                                id="precio_costo_base"
                                class="w-full border-2 border-primary-light rounded-xl bg-[#181F32] text-white placeholder-gray-400 px-4 py-2 focus:border-primary focus:ring-2 focus:ring-primary transition-all duration-200"
                                placeholder="0.00" value="{{ old('precio_costo_base') }}">
                        </div>
                    </div>

                    <small class="text-gray-300 text-xs ">Se aplicará a todos los productos agregados. Puedes
                        editarlo individualmente en la tabla.</small>

                    {{-- Series --}}
                    <div class="gap-2 space-y-2">
                        <label class="text-white">Series (una por línea o separadas por espacio)</label>
                        <textarea name="series" rows="5"
                            class="w-full border-2 border-primary-light rounded-lg bg-[#181F32] text-white placeholder-gray-400 px-4 py-2 focus:border-primary focus:ring-2 focus:ring-primary transition-all duration-200"
                            id="series-input" placeholder="Escanea o escribe las series aquí...">{{ old('series') }}</textarea>
                    </div>

                    {{-- Vencimiento --}}
                    <div class="flex items-center space-x-2">
                        <input type="checkbox" name="tiene_vencimiento" id="tiene_vencimiento" class="h-4 w-4 rou"
                            {{ old('tiene_vencimiento') ? 'checked' : '' }}>
                        <label for="tiene_vencimiento" class="text-white">¿El producto tiene vencimiento?</label>
                    </div>

                    <div class="space-y-2 {{ old('tiene_vencimiento') ? '' : 'hidden' }}" id="vencimiento-container">
                        <label class="text-white">Fecha de vencimiento</label>
                        <input type="date" name="fecha_vencimiento" id="fecha_vencimiento"
                            class="w-full border-2 border-primary-light rounded-xl bg-[#181F32] text-white px-4 py-2 focus:border-primary focus:ring-2 focus:ring-primary transition-all duration-200"
                            value="{{ old('fecha_vencimiento') }}">
                    </div>

                    {{-- Agregar a la tabla + Hidden JSON --}}
                    <div class="mt-2 flex gap-2">
                        <button type="button" id="btnAgregarEntrada"
                            class="bg-accent text-primary-dark hover:bg-accent-bright hover:scale-105 active:scale-95 transition-all duration-200 px-6 py-2 rounded-xl shadow-xl">
                            Agregar a la tabla
                        </button>

                    </div>
                    <input type="hidden" name="detalle_entrada" id="detalle_entrada">

                    {{-- Tabla de entradas seleccionadas --}}
                    <div class="mt-6 col-span-2 mb-6">
                        <div class="overflow-x-auto rounded-lg border border-primary-soft">
                            <div
                                class="grid grid-cols-9 md:grid-cols-8 min-w-[850px] bg-primary-dark text-white rounded-t-lg font-bold text-center py-2 place-items-center">
                                <div>#</div>
                                <div class="col-span-2 text-left">Descripción</div>
                                <div class="hidden md:block">Precio Costo</div>
                                <div class="md:hidden block">Precio</div>
                                <div class="hidden md:block">Fecha</div>
                                <div class="md:hidden block">Fec.</div>
                                <div class="hidden md:block">Cantidad</div>
                                <div class="md:hidden block">Cant</div>
                                <div>Vencimiento</div>
                                <div>Acción</div>
                            </div>
                            <div id="entradaPreviewGrid"></div>
                        </div>
                    </div>

                    <div class="flex justify-between items-center space-x-4">
                        <button type="submit"
                            class="w-full md:w-auto bg-primary text-white px-6 py-2 rounded-xl font-bold shadow-xl transition-all duration-200 hover:bg-accent hover:scale-105 active:scale-95">
                            Registrar Entrada
                        </button>
                        <button type="button" id="btnLimpiarTabla"
                            class="bg-red-600 text-white hover:bg-red-700  hover:scale-105 active:scale-95 transition-all duration-200 px-6 py-2 rounded-xl shadow-xl">
                            Limpiar Tabla
                        </button>
                    </div>
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
                // ---------- Previene Enter global (excepto TEXTAREA) ----------
                const form = document.getElementById('form-entrada');
                form.addEventListener('keydown', e => {
                    if (e.key === 'Enter' && e.target.tagName !== 'TEXTAREA') e.preventDefault();
                });

                // ---------- Referencias DOM ----------
                const buscarInput = document.getElementById('producto-buscar');
                const resultadoBox = document.getElementById('resultado-producto');
                const productoIdInput = document.getElementById('producto-id');
                const productoInfo = document.getElementById('producto-info');
                const productoSel = document.getElementById('producto-seleccionado');
                const modal = document.getElementById('modal-productos');
                const listaModal = document.getElementById('lista-productos-modal');
                const chkInvima = document.getElementById('tiene_invima');

                const almacenSelect = document.getElementById('almacen_id');
                const loteInput = document.querySelector('[name="numero_lote"]');
                const fechaInput = document.getElementById('fecha_fabricacion');
                const seriesInput = document.getElementById('series-input');
                const cantidadInput = document.getElementById('cantidad');
                const precioCostoBaseInput = document.getElementById('precio_costo_base');
                const chkVenc = document.getElementById('tiene_vencimiento');
                const vencContainer = document.getElementById('vencimiento-container');
                const fechaVencInput = document.getElementById('fecha_vencimiento');

                const btnAgregarEntrada = document.getElementById('btnAgregarEntrada');
                const btnLimpiarTabla = document.getElementById('btnLimpiarTabla');
                const hiddenDetalle = document.getElementById('detalle_entrada');

                // ---------- Estado ----------
                let opciones = [];
                let currentIndex = -1;
                let entradasSeleccionadas = []; // líneas agregadas a la tabla
                let productoSeleccionado = null; // último producto elegido (con tiene_series)

                // ---------- Funciones de LocalStorage ----------
                const STORAGE_KEY = 'entradas_productos_temp';

                function guardarEnStorage() {
                    try {
                        localStorage.setItem(STORAGE_KEY, JSON.stringify(entradasSeleccionadas));
                        console.log('Entradas guardadas en localStorage');
                    } catch (error) {
                        console.error('Error al guardar en localStorage:', error);
                    }
                }

                function cargarDesdeStorage() {
                    try {
                        const saved = localStorage.getItem(STORAGE_KEY);
                        if (saved) {
                            const parsed = JSON.parse(saved);
                            if (Array.isArray(parsed)) {
                                entradasSeleccionadas = parsed;
                                console.log('Entradas cargadas desde localStorage:', entradasSeleccionadas.length);

                                // Mostrar mensaje informativo al usuario
                                if (entradasSeleccionadas.length > 0) {
                                    showNotification(
                                        `Se restauraron ${entradasSeleccionadas.length} producto(s) de la sesión anterior`,
                                        'info'
                                    );
                                }
                            }
                        }
                    } catch (error) {
                        console.error('Error al cargar desde localStorage:', error);
                        localStorage.removeItem(STORAGE_KEY);
                    }
                }

                function limpiarStorage() {
                    try {
                        localStorage.removeItem(STORAGE_KEY);
                        console.log('Storage limpiado');
                    } catch (error) {
                        console.error('Error al limpiar storage:', error);
                    }
                }

                // Función para mostrar notificaciones temporales
                function showNotification(message, type = 'info') {
                    const notification = document.createElement('div');
                    notification.className = `fixed top-4 right-4 z-50 px-4 py-2 rounded-lg shadow-lg text-white transform transition-all duration-300 translate-x-full ${
                        type === 'info' ? 'bg-blue-600' : 
                        type === 'success' ? 'bg-green-600' : 
                        type === 'warning' ? 'bg-yellow-600' : 'bg-red-600'
                    }`;
                    notification.textContent = message;

                    document.body.appendChild(notification);

                    // Animar entrada
                    setTimeout(() => {
                        notification.classList.remove('translate-x-full');
                    }, 100);

                    // Animar salida y remover
                    setTimeout(() => {
                        notification.classList.add('translate-x-full');
                        setTimeout(() => {
                            if (notification.parentNode) {
                                notification.parentNode.removeChild(notification);
                            }
                        }, 300);
                    }, 3000);
                }

                // ---------- Utils ----------
                const endpoint = "{{ route('productos.buscar') }}";
                const debounce = (fn, delay = 300) => {
                    let t;
                    return (...args) => {
                        clearTimeout(t);
                        t = setTimeout(() => fn(...args), delay);
                    };
                };
                const escapeHtml = (s) => String(s ?? '').replace(/[&<>"']/g, ch => ({
                    '&': '&amp;',
                    '<': '&lt;',
                    '>': '&gt;',
                    '"': '&quot;',
                    "'": '&#39;'
                } [ch]));
                const chip = (t) =>
                    `<span class="inline-block bg-primary-soft text-xs px-2 py-1 rounded mr-2 mb-1">${escapeHtml(t)}</span>`;
                window.cerrarModal = () => modal.classList.add('hidden');

                function normalizeTieneSeries(v) {
                    if (v === true || v === 1) return true;
                    if (typeof v === 'string') {
                        const s = v.toLowerCase().trim();
                        return s === '1' || s === 'true';
                    }
                    return false;
                }

                // ---------- Toggle UI según producto (con/sin series) ----------
                function toggleCamposSegunProducto() {
                    if (!productoSeleccionado) return;
                    const esConSerie = !!productoSeleccionado.tiene_series;

                    const seriesWrap = seriesInput.closest('.gap-2') || seriesInput.parentElement;
                    const loteWrap = loteInput.closest('.space-y-2') || loteInput.parentElement;

                    if (esConSerie) {
                        // Producto CON series: mostrar todos los campos
                        loteWrap.classList.remove('hidden');
                        loteInput.required = true;
                        seriesWrap.classList.remove('hidden');
                        seriesInput.required = true;
                        cantidadInput.readOnly = true;
                        cantidadInput.required = false;
                        cantidadInput.classList.add('opacity-70', 'cursor-not-allowed');
                        if (!cantidadInput.value) cantidadInput.value = 0;
                    } else {
                        // Producto SIN series: ocultar lote y series
                        loteWrap.classList.add('hidden');
                        loteInput.required = false;
                        loteInput.value = '';
                        seriesWrap.classList.add('hidden');
                        seriesInput.required = false;
                        seriesInput.value = '';
                        cantidadInput.readOnly = false;
                        cantidadInput.required = true;
                        cantidadInput.classList.remove('opacity-70', 'cursor-not-allowed');
                        if (!cantidadInput.value || Number(cantidadInput.value) < 1) cantidadInput.value = 1;
                    }

                    // Fecha fabricación: siempre requerida
                    fechaInput.required = true;
                }

                function getNextField(currentField) {
                    if (!productoSeleccionado) return null;

                    const esConSerie = !!productoSeleccionado.tiene_series;

                    // Flujo para productos CON series
                    if (esConSerie) {
                        const flowConSeries = [
                            'tiene_invima', // checkbox invima
                            'almacen_id', // almacén
                            'numero_lote', // lote
                            'fecha_fabricacion', // fecha fabricación
                            'precio_costo_base', // precio base
                            'series-input', // series
                            'tiene_vencimiento' // checkbox vencimiento
                        ];

                        const currentIndex = flowConSeries.indexOf(currentField);
                        if (currentIndex >= 0 && currentIndex < flowConSeries.length - 1) {
                            return flowConSeries[currentIndex + 1];
                        }
                    } else {
                        // Flujo para productos SIN series
                        const flowSinSeries = [
                            'tiene_invima', // checkbox invima
                            'almacen_id', // almacén
                            'fecha_fabricacion', // fecha fabricación
                            'cantidad', // cantidad
                            'precio_costo_base', // precio base
                            'tiene_vencimiento' // checkbox vencimiento
                        ];

                        const currentIndex = flowSinSeries.indexOf(currentField);
                        if (currentIndex >= 0 && currentIndex < flowSinSeries.length - 1) {
                            return flowSinSeries[currentIndex + 1];
                        }
                    }

                    return null;
                }
                // ---------- Contar series -> cantidad ----------
                function actualizarCantidad() {
                    const list = (seriesInput.value || '').split(/[\s\n]+/).map(s => s.trim()).filter(Boolean);
                    cantidadInput.value = list.length;
                }
                seriesInput.addEventListener('input', actualizarCantidad);

                // ---------- Buscar productos (server) ----------
                async function fetchProductos(query) {
                    const url = new URL(endpoint, window.location.origin);
                    url.searchParams.set('q', query);
                    url.searchParams.set('per_page', 20);
                    const res = await fetch(url.toString(), {
                        headers: {
                            'Accept': 'application/json'
                        },
                        credentials: 'same-origin'
                    });
                    if (!res.ok) throw new Error('Error ' + res.status);
                    const json = await res.json();
                    return (json?.data ?? []).map(p => ({
                        ...p,
                        tiene_series: normalizeTieneSeries(p.tiene_series)
                    }));
                }

                function renderResultados(items) {
                    resultadoBox.innerHTML = '';
                    opciones = [];
                    currentIndex = -1;

                    if (!items.length) {
                        resultadoBox.classList.add('hidden');
                        return;
                    }
                    resultadoBox.classList.remove('hidden');

                    items.forEach(p => {
                        const opt = document.createElement('div');
                        opt.className =
                            'p-2 cursor-pointer hover:bg-primary transition-colors text-sm text-white';
                        opt.textContent =
                            `${p.nombre} (${p.modelo ?? '-'}) — ${(p.marca && p.marca.nombre)?p.marca.nombre:'Sin marca'} — ${p.codigo}`;
                        opt.onclick = () => {
                            const mismos = items.filter(x => x.nombre === p.nombre);
                            if (mismos.length > 1) {
                                listaModal.innerHTML = '';
                                mismos.forEach(m => {
                                    const it = document.createElement('div');
                                    it.className =
                                        'border-b p-2 cursor-pointer hover:bg-accent transition-colors';
                                    it.textContent =
                                        `${m.nombre} (${m.modelo ?? '-'}) — ${(m.marca && m.marca.nombre)?m.marca.nombre:'Sin marca'} — ${m.codigo}`;
                                    it.onclick = () => {
                                        seleccionarProducto(m);
                                        cerrarModal();
                                    };
                                    listaModal.appendChild(it);
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
                }

                function limpiarPreseleccion() {
                    opciones.forEach(opt => opt.classList.remove('bg-primary'));
                }

                function actualizarPreseleccion() {
                    limpiarPreseleccion();
                    if (currentIndex >= 0 && currentIndex < opciones.length) {
                        opciones[currentIndex].classList.add('bg-primary');
                        opciones[currentIndex].scrollIntoView({
                            block: 'nearest'
                        });
                    }
                }

                function seleccionarProducto(p) {
                    productoSeleccionado = p;
                    productoIdInput.value = p.id;
                    productoInfo.textContent =
                        `${p.nombre} (${p.modelo ?? '-'}) — ${(p.marca && p.marca.nombre) ? p.marca.nombre : 'Sin marca'} — ${p.codigo}`;
                    productoSel.classList.remove('hidden');
                    resultadoBox.classList.add('hidden');
                    resultadoBox.innerHTML = '';
                    opciones = [];
                    currentIndex = -1;
                    buscarInput.value = '';
                    productoSel.dataset.tieneSeries = p.tiene_series ? '1' : '0';

                    // Configurar campos según tipo de producto
                    toggleCamposSegunProducto();

                    // Hacer focus en el primer campo del flujo
                    chkInvima.focus();
                }

                // ---------- Tabla multi-fila ----------
                function renderEntradaPreviewTabla() {
                    const grid = document.getElementById('entradaPreviewGrid');
                    if (!grid) return;

                    if (!entradasSeleccionadas.length) {
                        grid.innerHTML =
                            `<div class="grid grid-cols-9 md:grid-cols-8 min-w-[850px] items-center border-b border-primary-soft text-white py-3 px-2">
                               <div class="col-span-9 text-center text-gray-400">Sin entradas en la tabla</div>
                             </div>`;
                        return;
                    }

                    grid.innerHTML = entradasSeleccionadas.map((e, idx) => {
                        let desc = `
                        <div class="font-semibold">${escapeHtml(`${e.nombre_producto} (${e.modelo || '-'}) — ${e.marca_nombre} — ${e.codigo}`)}</div>
                        <div class="mt-1"><span class="inline-block bg-primary text-xs px-2 py-1 rounded mr-2">Lote: ${escapeHtml(e.numero_lote ?? '-')}</span></div>
                      `;
                        if (e.tiene_series && e.series?.length) {
                            desc +=
                                `<div class="mt-1 flex flex-wrap">${e.series.map(s=>chip(s)).join('')}</div>`;
                        } else if (e.tiene_series) {
                            desc += `<div class="mt-1 text-xs text-gray-300">Sin series</div>`;
                        }

                        const qtyCol = e.tiene_series ?
                            `${e.cantidad}` :
                            `<input type="number" min="1" value="${e.cantidad}" class="w-16 text-center bg-primary-soft text-white rounded-md p-1 entrada-cantidad" data-idx="${idx}">`;

                        return `
                        <div class="grid grid-cols-9 md:grid-cols-8 min-w-[850px] items-center border-b text-white border-primary-soft py-2 px-2 hover:bg-primary-dark/40 transition">
                          <div class="text-center">${idx+1}</div>
                          <div class="col-span-2 pl-2">${desc}</div>
                          <div class="text-center">
                            <input type="number" step="0.01" min="0" value="${e.precio_costo || 0}" class="w-20 text-center bg-primary-soft text-white rounded-md p-1 entrada-precio" data-idx="${idx}" placeholder="0.00">
                          </div>
                          <div class="text-center">${escapeHtml(e.fecha_fabricacion || '—')}</div>
                          <div class="text-center">${qtyCol}</div>
                          <div class="text-center">${e.fecha_vencimiento ? escapeHtml(e.fecha_vencimiento) : '—'}</div>
                          <div class="flex items-center justify-center">
                            <button type="button" class="text-red-400 hover:text-red-600 entrada-eliminar" data-idx="${idx}" title="Eliminar fila">×</button>
                          </div>
                        </div>`;
                    }).join('');

                    // wire eventos
                    grid.querySelectorAll('.entrada-eliminar').forEach(btn => {
                        btn.addEventListener('click', () => {
                            const i = Number(btn.dataset.idx);
                            entradasSeleccionadas.splice(i, 1);
                            renderEntradaPreviewTabla();
                            syncHidden();
                        });
                    });
                    grid.querySelectorAll('.entrada-cantidad').forEach(inp => {
                        inp.addEventListener('input', () => {
                            const i = Number(inp.dataset.idx);
                            let val = parseInt(inp.value);
                            if (isNaN(val) || val < 1) val = 1;
                            entradasSeleccionadas[i].cantidad = val;
                            syncHidden();
                        });
                    });
                    grid.querySelectorAll('.entrada-precio').forEach(inp => {
                        inp.addEventListener('input', () => {
                            const i = Number(inp.dataset.idx);
                            let val = parseFloat(inp.value);
                            if (isNaN(val) || val < 0) val = 0;
                            entradasSeleccionadas[i].precio_costo = val;
                            syncHidden();
                        });
                    });
                }

                function construirEntradaDesdeFormulario() {
                    if (!productoSeleccionado) throw new Error('Selecciona un producto.');
                    const esConSerie = !!productoSeleccionado.tiene_series;

                    const lote = (loteInput.value || '').trim();
                    if (esConSerie && !lote) throw new Error('Ingresa el número de lote.');

                    const fechaFab = fechaInput.value || '';
                    if (!fechaFab) throw new Error('Ingresa la fecha de fabricación.');

                    const venc = chkVenc.checked ? (fechaVencInput?.value || '') : null;
                    const precioCostoBase = parseFloat(precioCostoBaseInput.value) || 0;

                    let series = [];
                    let cantidad = 0;

                    if (esConSerie) {
                        const raw = (seriesInput.value || '').split(/[\s\n]+/).map(s => s.trim()).filter(Boolean);
                        if (!raw.length) throw new Error('Debes ingresar al menos una serie.');
                        series = raw;
                        cantidad = raw.length;
                    } else {
                        const val = Number(cantidadInput.value);
                        if (!val || val < 1) throw new Error('La cantidad debe ser al menos 1.');
                        cantidad = val;
                    }

                    return {
                        producto_id: productoSeleccionado.id,
                        nombre_producto: productoSeleccionado.nombre,
                        modelo: productoSeleccionado.modelo || '',
                        codigo: productoSeleccionado.codigo || '',
                        marca_nombre: (productoSeleccionado.marca && productoSeleccionado.marca.nombre) ?
                            productoSeleccionado.marca.nombre : 'Sin marca',
                        tiene_series: esConSerie,

                        numero_lote: esConSerie ? lote : null,
                        fecha_fabricacion: fechaFab,
                        fecha_vencimiento: venc,

                        series: series,
                        cantidad: cantidad,
                        precio_costo: precioCostoBase, // Usar el precio base del formulario

                        tiene_invima: chkInvima.checked ? 1 : 0,
                    };
                }

                function syncHidden() {
                    hiddenDetalle.value = JSON.stringify(entradasSeleccionadas);
                    guardarEnStorage(); // Guardar automáticamente cada vez que cambie
                }


                // ---------- Listeners ----------
                chkVenc.addEventListener('change', () => {
                    if (chkVenc.checked) {
                        vencContainer.classList.remove('hidden');
                    } else {
                        vencContainer.classList.add('hidden');
                        if (fechaVencInput) fechaVencInput.value = '';
                    }
                });
                // Navegación con Enter
                function focusNextField(currentFieldId) {
                    const nextFieldId = getNextField(currentFieldId);
                    console.log(`Navegando de ${currentFieldId} a ${nextFieldId}`); // Debug
                    if (nextFieldId) {
                        const nextElement = document.getElementById(nextFieldId);
                        if (nextElement && !nextElement.closest(
                            '.hidden')) { // Verificar que el elemento no esté oculto
                            nextElement.focus();
                            if (nextElement.type === 'number' || nextElement.type === 'text') {
                                setTimeout(() => nextElement.select?.(), 10); // Pequeño delay para select
                            }
                        }
                    }
                }
                // Checkbox INVIMA
                chkInvima.addEventListener('keydown', e => {
                    if (e.key === 'Enter') {
                        e.preventDefault();
                        chkInvima.checked = !chkInvima.checked;
                        focusNextField('tiene_invima');
                    }
                });

                // Almacén
                almacenSelect.addEventListener('keydown', e => {
                    if (e.key === 'Enter') {
                        e.preventDefault();
                        focusNextField('almacen_id');
                    }
                });

                // Lote (solo visible para productos con series)
                loteInput.addEventListener('keydown', e => {
                    if (e.key === 'Enter') {
                        e.preventDefault();
                        focusNextField('numero_lote');
                    }
                });

                // Fecha fabricación
                fechaInput.addEventListener('keydown', e => {
                    if (e.key === 'Enter') {
                        e.preventDefault();
                        focusNextField('fecha_fabricacion');
                    }
                });

                // Cantidad (solo para productos sin series)
                cantidadInput.addEventListener('keydown', e => {
                    if (e.key === 'Enter' && !cantidadInput.readOnly) {
                        e.preventDefault();
                        focusNextField('cantidad');
                    }
                });

                // Precio costo base
                precioCostoBaseInput.addEventListener('keydown', e => {
                    if (e.key === 'Enter') {
                        e.preventDefault();
                        focusNextField('precio_costo_base');
                    }
                });

                // Series (solo para productos con series)
                seriesInput.addEventListener('keydown', e => {
                    if (e.key === 'Enter' && e.ctrlKey) {
                        e.preventDefault();
                        focusNextField('series-input');
                    }
                });

                // Checkbox vencimiento
                chkVenc.addEventListener('keydown', e => {
                    if (e.key === 'Enter') {
                        e.preventDefault();
                        chkVenc.checked = !chkVenc.checked;
                        chkVenc.dispatchEvent(new Event('change'));
                        if (chkVenc.checked && fechaVencInput) {
                            fechaVencInput.focus();
                        }
                        // Si no tiene vencimiento, podríamos hacer focus al botón de agregar
                        // btnAgregarEntrada.focus();
                    }
                });

                // Buscar (debounced)
                const onType = debounce(async () => {
                    const txt = (buscarInput.value || '').trim();
                    resultadoBox.classList.add('hidden');
                    resultadoBox.innerHTML = '';
                    opciones = [];
                    currentIndex = -1;

                    if (txt.length < 2) return;

                    try {
                        resultadoBox.classList.remove('hidden');
                        resultadoBox.innerHTML = '<div class="p-2 text-sm text-gray-400">Buscando…</div>';
                        const items = await fetchProductos(txt);
                        renderResultados(items);
                    } catch (e) {
                        resultadoBox.innerHTML = `<div class="p-2 text-sm text-red-500">${e.message}</div>`;
                    }
                }, 250);

                buscarInput.addEventListener('input', onType);
                buscarInput.addEventListener('keydown', e => {
                    if (!opciones.length) return;
                    if (e.key === 'ArrowDown') {
                        e.preventDefault();
                        currentIndex = (currentIndex + 1) % opciones.length;
                        actualizarPreseleccion();
                    } else if (e.key === 'ArrowUp') {
                        e.preventDefault();
                        currentIndex = (currentIndex - 1 + opciones.length) % opciones.length;
                        actualizarPreseleccion();
                    } else if (e.key === 'Enter') {
                        e.preventDefault();
                        if (currentIndex >= 0) opciones[currentIndex].click();
                    }
                });

                // ---------- Agregar a la tabla ----------
                btnAgregarEntrada.addEventListener('click', () => {
                    try {
                        const entry = construirEntradaDesdeFormulario();

                        // evitar duplicados exactos
                        const dup = entradasSeleccionadas.some(e =>
                            e.producto_id === entry.producto_id &&
                            e.numero_lote === entry.numero_lote &&
                            JSON.stringify(e.series) === JSON.stringify(entry.series) &&
                            e.fecha_fabricacion === entry.fecha_fabricacion &&
                            e.fecha_vencimiento === entry.fecha_vencimiento
                        );
                        if (dup) {
                            alert('Esta entrada ya está en la tabla.');
                            return;
                        }

                        entradasSeleccionadas.push(entry);
                        renderEntradaPreviewTabla();
                        syncHidden(); // Esto ya incluye guardarEnStorage()

                        // Mostrar notificación de éxito
                        showNotification('Producto agregado a la tabla', 'success');

                        // 🔁 Limpia la línea y lleva el foco al buscador (almacén se mantiene)
                        disableLineRequireds();
                        resetLinea();

                    } catch (err) {
                        alert(err.message);
                    }
                });

                // ---------- Limpiar tabla ----------
                btnLimpiarTabla.addEventListener('click', () => {
                    if (entradasSeleccionadas.length > 0 && confirm(
                            '¿Estás seguro de que quieres limpiar toda la tabla?')) {
                        entradasSeleccionadas = [];
                        renderEntradaPreviewTabla();
                        syncHidden();
                        limpiarStorage();
                        showNotification('Tabla limpiada', 'warning');
                    }
                });

                // ---------- Submit: exigir al menos una línea y no bloquear por línea en edición ----------
                form.addEventListener('submit', (e) => {
                    // Desactiva requireds de la línea para que no bloquee
                    disableLineRequireds();
                    form.setAttribute('novalidate', 'true');

                    if (!entradasSeleccionadas.length) {
                        e.preventDefault();
                        alert('Agrega al menos una entrada en la tabla.');
                        return;
                    }

                    syncHidden();

                    // Limpiar storage cuando se envía el formulario exitosamente
                    limpiarStorage();
                });

                // ---------- Advertencia antes de salir ----------
                let formSubmitted = false;

                form.addEventListener('submit', () => {
                    formSubmitted = true;
                });

                window.addEventListener('beforeunload', (e) => {
                    if (entradasSeleccionadas.length > 0 && !formSubmitted) {
                        e.preventDefault();
                        e.returnValue =
                            '¿Estás seguro de que quieres salir? Tienes productos agregados que no has guardado.';
                    }
                });

                // ---------- Helpers de línea ----------
                function resetLinea() {
                    productoSeleccionado = null;
                    productoIdInput.value = '';
                    productoInfo.textContent = '';
                    productoSel.classList.add('hidden');
                    const badge = document.getElementById('badge-modo');
                    if (badge) badge.textContent = '';

                    loteInput.value = '';
                    seriesInput.value = '';
                    cantidadInput.value = 1;
                    precioCostoBaseInput.value = '';
                    fechaInput.value = '{{ date('Y-m-d') }}';
                    chkVenc.checked = false;
                    vencContainer.classList.add('hidden');
                    if (fechaVencInput) fechaVencInput.value = '';

                    loteInput.required = false;
                    seriesInput.required = false;
                    cantidadInput.required = false;

                    const seriesWrap = seriesInput.closest('.gap-2') || seriesInput.parentElement;
                    const loteWrap = loteInput.closest('.space-y-2') || loteInput.parentElement;
                    seriesWrap.classList.add('hidden');
                    loteWrap.classList.add('hidden');

                    buscarInput.value = '';
                    buscarInput.focus();
                    disableLineRequireds();
                }

                function disableLineRequireds() {
                    // Quita required de TODOS los campos de la línea en edición
                    const ids = ['numero_lote', 'series-input', 'cantidad', 'fecha_fabricacion', 'fecha_vencimiento',
                        'producto-id'
                    ];
                    ids.forEach(id => {
                        const el = document.getElementById(id) || document.querySelector(`[name="${id}"]`);
                        if (el) el.required = false;
                    });
                }

                // ---------- Cargar datos guardados al inicializar ----------
                cargarDesdeStorage();

                // ---------- Render inicial de la tabla ----------
                renderEntradaPreviewTabla();
                syncHidden();
            });
        </script>
    @endpush
</x-app-layout>
