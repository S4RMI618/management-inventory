@if($messages && count($messages))
    <div id="alert"
        class="fixed top-4 left-1/2 transform -translate-x-1/2 z-50 max-w-md w-full px-6 py-4 flex items-start gap-3 shadow-xl rounded-2xl
        transition-all duration-500
        {{ $type === 'success' ? 'bg-green-100 text-green-800 border border-green-400' : 'bg-red-100 text-red-800 border border-red-400' }}">
        
        <div class="flex-1 text-left space-y-2">
            @foreach($messages as $msg)
                <div class="flex items-start gap-2">
                    @if($type === 'success')
                        <svg class="w-5 h-5 flex-shrink-0 text-green-500 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M5 13l4 4L19 7"/>
                        </svg>
                    @else
                        <svg class="w-5 h-5 flex-shrink-0 text-red-500 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    @endif
                    <span>{{ $msg }}</span>
                </div>
            @endforeach
        </div>
        {{-- Botón cerrar --}}
        <button onclick="document.getElementById('alert').classList.add('hidden')" class="ml-2 text-xl text-gray-500 hover:text-gray-700 focus:outline-none transition">
            &times;
        </button>
    </div>

    @push('scripts')
    <script>
        setTimeout(() => {
            const a = document.getElementById('alert');
            if (a) {
                a.classList.add('opacity-0');
                setTimeout(() => a.classList.add('hidden'), 500);
            }
        }, 5000);
    </script>
    @endpush
@endif
