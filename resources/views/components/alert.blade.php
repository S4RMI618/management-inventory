@if($messages && count($messages))
    <div id="alert"
         class="fixed top-0 inset-x-0 p-4 z-50 text-center
                {{ $type === 'success' ? 'bg-green-200 text-green-800' : 'bg-red-200 text-red-800' }}">
        <div class="max-w-xl mx-auto">
            <ul class="list-disc list-inside space-y-1">
                @foreach($messages as $msg)
                    <li>{{ $msg }}</li>
                @endforeach
            </ul>
        </div>
    </div>

    @push('scripts')
    <script>
        setTimeout(() => {
            const a = document.getElementById('alert');
            if (a) a.classList.add('hidden');
        }, 5000);
    </script>
    @endpush
@endif
