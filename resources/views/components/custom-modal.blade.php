<div x-show="open"
     x-transition.opacity
     class="fixed inset-0 z-50 flex items-center justify-center bg-[#181F32]/70"
     style="display: none;">
    <div class="bg-gradient-to-br from-[#222E44] to-[#485273] border-4 border-[#1EA7FD] rounded-2xl shadow-2xl max-w-lg w-full p-6"
         x-transition.scale.origin.center>
        <div class="flex justify-between items-center mb-4">
            <h2 class="text-2xl font-bold text-white">{{ $title }}</h2>
            <button type="button" @click="open = false"
                class="text-[#1EA7FD] bg-white hover:bg-[#1EA7FD] hover:text-white transition-all duration-200 px-3 py-1 rounded-lg shadow-sm hover:scale-105 active:scale-95">✕</button>
        </div>
        <div class="text-white">
            {{ $slot }}
        </div>
        <div class="flex justify-end gap-2 mt-6">
            {{ $footer ?? '' }}
        </div>
    </div>
</div>
