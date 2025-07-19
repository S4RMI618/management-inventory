{{-- resources/views/traslados/create.blade.php --}}
<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-2xl text-accent text-center">Registrar Traslado</h2>
    </x-slot>

    <x-alert />
    <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div
                class="bg-gradient-to-br from-primary-dark to-primary-soft border-4 border-primary shadow-2xl rounded-lg p-10">
                <form method="POST" action="{{ route('traslados.store') }}" id="traslado-form">
                    @csrf

                    {{-- Almacén Origen --}}
                    <div class="mb-4">
                        <label class="text-white w-full block mb-1">Almacén Origen</label>
                        <select name="almacen_origen_id" id="almacen-origen"
                            class="w-full border-2 border-primary-light rounded-lg bg-primary-bg text-white placeholder-gray-400 px-4 py-2 focus:border-primary focus:ring-2 focus:ring-primary transition-all duration-200">
                            <option value="">– Seleccione –</option>
                            @foreach ($almacenes as $alm)
                                <option value="{{ $alm->id }}">{{ $alm->nombre }}</option>
                            @endforeach
                        </select>
                        @error('almacen_origen_id')
                            <p class="text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Producto (arranca deshabilitado) --}}
                    <div class="mb-4">
                        <label class="text-white w-full block mb-1">Producto</label>
                        <select name="producto_id" id="producto-select"
                            class="w-full border-2 border-primary-light rounded-lg bg-primary-bg text-white placeholder-gray-400 px-4 py-2 focus:border-primary focus:ring-2 focus:ring-primary transition-all duration-200"
                            disabled>
                            <option value="">– Seleccione Almacén Primero –</option>
                        </select>
                        @error('producto_id')
                            <p class="text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Lotes (checkboxes) --}}
                    <div class="mb-4">
                        <label class="text-white w-full block mb-1">Lotes Disponibles</label>
                        <div id="lotes-checkboxes" class="border rounded p-2 min-h-[60px] border-primary-light">
                            {{-- se llenará vía JS --}}
                        </div>
                        @error('lote_ids')
                            <p class="text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Series por lote --}}
                    <div class="mb-4">
                        <label class="text-white w-full block mb-1">Series a Trasladar</label>
                        <div id="series-container" class="border rounded p-2 min-h-[80px] border-primary-light">
                            {{-- se llenará vía JS --}}
                        </div>
                        @error('series_ids')
                            <p class="text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Almacén Destino --}}
                    <div class="mb-4">
                        <label class="text-white w-full block mb-1">Almacén Destino</label>
                        <select name="almacen_destino_id"
                            class="w-full border-2 border-primary-light rounded-lg bg-primary-bg text-white placeholder-gray-400 px-4 py-2 focus:border-primary focus:ring-2 focus:ring-primary transition-all duration-200">
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
                    <div class="mb-4">
                        <label class="text-white w-full block mb-1">Observaciones (opcional)</label>
                        <textarea name="observaciones"
                            class="w-full border-2 border-primary-light rounded-lg bg-primary-bg text-white placeholder-gray-400 px-4 py-2 focus:border-primary focus:ring-2 focus:ring-primary transition-all duration-200"
                            rows="3">{{ old('observaciones') }}</textarea>
                    </div>

                    <button type="submit"
                        class="bg-primary text-white hover:bg-accent hover:scale-105 active:scale-95 transition-all duration-200 px-4 py-2 rounded-md shadow-x">Transferir</button>
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
        <div id="${group}"class="mb-6 p-4">
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
