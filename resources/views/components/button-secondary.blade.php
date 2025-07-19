<button {{ $attributes->merge([
    'class' => 'bg-white text-primary hover:bg-primary hover:text-white border border-primary hover:scale-105 active:scale-95 transition-all duration-200 px-5 py-2 rounded-2xl shadow font-semibold'
]) }}>
    {{ $slot }}
</button>