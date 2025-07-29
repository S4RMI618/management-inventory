{{-- resources/views/traslados/create.blade.php --}}
<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-2xl text-accent text-center">Registrar Traslado</h2>
    </x-slot>

    <x-alert />
    <div class="md:p-4">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div
                class="bg-gradient-to-br from-primary-dark to-primary-soft border-4 border-primary shadow-2xl rounded-lg p-6 md:p-10">
                <form method="POST" action="{{ route('traslados.store') }}" id="traslado-form"
                    class="grid md:grid-cols-2 md:gap-6 md:mt-6">
                    @csrf

                    {{-- Columna izquierda: Datos del traslado --}}
                    <div class="space-y-4">
                        {{-- Almacén Origen --}}
                        <div>
                            <label class="text-white block mb-1">Almacén Origen</label>
                            <select name="almacen_origen_id" id="almacen-origen"
                                class="w-full border-2 border-primary-light rounded-lg bg-primary-bg text-white px-4 py-2 focus:ring-2 focus:ring-primary">
                                <option value="">– Seleccione –</option>
                                @foreach ($almacenes as $alm)
                                    <option value="{{ $alm->id }}">{{ $alm->nombre }}</option>
                                @endforeach
                            </select>
                            @error('almacen_origen_id')
                                <p class="text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Producto --}}
                        <div>
                            <label class="text-white block mb-1">Producto</label>
                            <select name="producto_id" id="producto-select"
                                class="w-full border-2 border-primary-light rounded-lg bg-primary-bg text-white px-4 py-2 focus:ring-2 focus:ring-primary"
                                disabled>
                                <option value="">– Seleccione Almacén Primero –</option>
                            </select>
                            @error('producto_id')
                                <p class="text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Lotes disponibles --}}
                        <div>
                            <label class="text-white block mb-1">Lotes Disponibles</label>
                            <div id="lotes-checkboxes"
                                class="border rounded-lg p-3 min-h-[60px] bg-primary-bg text-white border-primary-light space-y-2">
                                {{-- llenado dinámico --}}
                            </div>
                            @error('lote_ids')
                                <p class="text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        <div class="md:hidden block">
                            {{-- Título de la sección --}}
                            <h3 class="text-accent text-xl font-semibold">Series a Trasladar</h3>
                            <div
                                class="bg-primary-soft rounded-xl p-4 border border-primary-light overflow-y-auto min-h-[20vh]">

                                <div id="series-container" class="space-y-6 text-white">
                                    {{-- dinámico por JS --}}
                                </div>
                            </div>
                        </div>

                        {{-- Almacén Destino --}}
                        <div>
                            <label class="text-white block mb-1">Almacén Destino</label>
                            <select name="almacen_destino_id"
                                class="w-full border-2 border-primary-light rounded-lg bg-primary-bg text-white px-4 py-2 focus:ring-2 focus:ring-primary">
                                <option value="">– Seleccione –</option>
                                @foreach ($almacenes as $alm)
                                    <option value="{{ $alm->id }}">{{ $alm->nombre }}</option>
                                @endforeach
                            </select>
                            @error('almacen_destino_id')
                                <p class="text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Observaciones --}}
                        <div>
                            <label class="text-white block mb-1">Observaciones (opcional)</label>
                            <textarea name="observaciones" rows="3"
                                class="w-full border-2 border-primary-light rounded-lg bg-primary-bg text-white px-4 py-2 focus:ring-2 focus:ring-primary">{{ old('observaciones') }}</textarea>
                        </div>
                    </div>

                    {{-- Columna derecha: Series --}}
                    <div class="hidden md:block">
                        {{-- Título de la sección --}}
                        <h3 class="text-accent text-xl font-semibold">Series a Trasladar</h3>
                        <div
                            class="bg-primary-soft rounded-xl p-4 border border-primary-light overflow-y-auto min-h-[30vh] md:max-h-[80vh] md:min-h-[80vh]">

                            <div id="series-container" class="space-y-6 text-white">
                                {{-- dinámico por JS --}}
                            </div>
                        </div>
                    </div>

                    {{-- Botón submit debajo del grid (ocupa ambas columnas) --}}
                    <div class="md:col-span-2 flex mt-4">
                        <button type="submit"
                            class="w-full md:w-auto bg-primary text-white hover:bg-accent hover:scale-105 active:scale-95 transition-all duration-200 px-6 py-3 rounded-xl shadow-xl">
                            Transferir
                        </button>
                    </div>
                </form>

            </div>
        </div>
    </div>
    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', () => {
                console.log('📦 Traslados JS cargado');

                const origenSelect = document.getElementById('almacen-origen');
                const productoSelect = document.getElementById('producto-select');
                const lotesDiv = document.getElementById('lotes-checkboxes');
                const seriesCont = document.getElementById('series-container');

                origenSelect.addEventListener('change', () => {
                    console.log('🏷️ Almacén Seleccionado:', origenSelect.value);
                    productoSelect.disabled = true;
                    productoSelect.innerHTML = '<option>Cargando…</option>';
                    lotesDiv.innerHTML = '';
                    seriesCont.innerHTML = '';

                    if (!origenSelect.value) {
                        productoSelect.innerHTML = '<option>– Seleccione Almacén Primero –</option>';
                        return;
                    }

                    fetch(`/almacenes/${origenSelect.value}/productos`)
                        .then(res => res.json())
                        .then(products => {
                            productoSelect.disabled = false;
                            productoSelect.innerHTML = '<option value="">– Seleccione –</option>';
                            products.forEach(p => {
                                productoSelect.innerHTML +=
                                    `<option value="${p.id}">${p.codigo} – ${p.nombre}</option>`;
                            });
                        })
                        .catch(err => console.error('❌ Error cargando productos:', err));
                });

                productoSelect.addEventListener('change', () => {
                    console.log('📦 Producto seleccionado:', productoSelect.value);
                    const prodId = productoSelect.value;
                    const almId = origenSelect.value;
                    lotesDiv.innerHTML = prodId ? 'Cargando Lotes…' : '';
                    seriesCont.innerHTML = '';

                    if (!prodId) return;

                    fetch(`/productos/${prodId}/lotes?origen=${almId}`)
                        .then(res => res.json())
                        .then(lotes => {
                            lotesDiv.innerHTML = '';
                            lotes.forEach(l => {
                                lotesDiv.innerHTML += `
            <label class="flex items-center space-x-2 my-2 w-full border-2 rounded-lg bg-primary-bg text-white placeholder-gray-400 px-4 py-2 focus:border-primary focus:ring-2 focus:ring-primary transition-all duration-200">
                <input type="checkbox" class="lote-check accent-blue-600 w-5 h-5" value="${l.id}">
                <span class="font-semibold text-white">${l.numero_lote}</span>
            </label>
          `;
                            });
                            attachLoteListeners();
                        })
                        .catch(err => console.error('❌ Error cargando lotes:', err));
                });

                function attachLoteListeners() {
                    document.querySelectorAll('.lote-check').forEach(chk => {
                        chk.addEventListener('change', () => {
                            const loteId = chk.value;
                            const almId = origenSelect.value;
                            const group = `series-group-${loteId}`;

                            if (chk.checked) {
                                seriesCont.insertAdjacentHTML('beforeend', `
        <div id="${group}"class="mb-2 md:mb-6 p-2 md:p-4">
            <div class="flex items-center justify-between gap-1">
                <strong class="text-accent text-2xl">Lote ${loteId}</strong>
                <label class="flex items-center gap-2 text-primary-light cursor-pointer select-none">
                <input type="checkbox" class="select-all accent-accent-bright scale-125 transition-all duration-200 focus:ring-2 focus:ring-accent-bright" data-lote="${loteId}">
                <span class="font-medium">Marcar Todas</span>
                </label>
            </div>
            <div id="series-list-${loteId}" class="series-list mt-4 bg-primary-soft rounded-xl px-4 py-3 text-white shadow-inner animate-pulse">
                Cargando Series…
            </div>
        </div>

          `);

                                fetch(`/lotes/${loteId}/series?origen=${almId}`)
                                    .then(res => res.json())
                                    .then(series => {
                                        const listEl = document.getElementById(
                                            `series-list-${loteId}`);
                                        listEl.innerHTML = '';
                                        series.forEach(s => {
                                            listEl.innerHTML += `
                  <label class="inline-block mr-4">
                    <input
                      type="checkbox"
                      name="series_ids[]"
                      value="${s.id}"
                      data-lote="${loteId}">
                    ${s.numero_serie}
                  </label>
                `;
                                        });
                                        attachSelectAll(loteId);
                                    })
                                    .catch(err => console.error('❌ Error cargando series:', err));
                            } else {
                                document.getElementById(group)?.remove();
                            }
                        });
                    });
                }

                function attachSelectAll(loteId) {
                    const selAll = document.querySelector(`.select-all[data-lote="${loteId}"]`);
                    selAll.addEventListener('change', () => {
                        document
                            .querySelectorAll(`input[name="series_ids[]"][data-lote="${loteId}"]`)
                            .forEach(chk => chk.checked = selAll.checked);
                    });
                }
            });
        </script>
    @endpush
</x-app-layout>
