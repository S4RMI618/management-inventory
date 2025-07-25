@props(['disabled' => false])

<input @disabled($disabled) {{ $attributes->merge(['class' => "bg-primary-bg text-white border-2 border-primary-light rounded-xl px-4 py-3 focus:ring-accent focus:border-accent transition-all duration-200 w-full"]) }}>
