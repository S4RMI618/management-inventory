<button {{ $attributes->merge([
    'class' => 'bg-primary text-white hover:bg-accent hover:text-primary-dark hover:scale-105 active:scale-95 transition-all duration-200 px-5 py-2 rounded-2xl shadow-xl font-semibold'
]) }}>
    {{ $slot }}
</button>