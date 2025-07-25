<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-2xl text-accent text-center leading-tight">
            {{ __('Perfil') }}
        </h2>
    </x-slot>

    <div class="py-12 bg-gradient-to-br from-primary-dark to-primary-bg min-h-screen rounded-lg">
        <div class="px-10 gap-8 mx-auto grid md:grid-cols-2 items-stretch place-items-stretch max-w-5xl">
            <div class="bg-gradient-to-br from-primary-dark to-primary-soft border-4 border-primary shadow-2xl rounded-2xl p-8 h-full flex flex-col">
                <div class="max-w-xl mx-auto flex-1 flex flex-col justify-between">
                    @include('profile.partials.update-profile-information-form')
                </div>
            </div>

            <div class="bg-gradient-to-br from-primary-dark to-primary-soft border-4 border-primary shadow-2xl rounded-2xl p-8 h-full flex flex-col">
                <div class="max-w-xl mx-auto flex-1 flex flex-col justify-between">
                    @include('profile.partials.update-password-form')
                </div>
            </div>
        </div>
        <div class="mt-8 px-10 max-w-3xl mx-auto">
            <div class="bg-gradient-to-br from-primary-dark to-primary-soft border-4 border-primary shadow-2xl rounded-2xl p-8">
                <div class="max-w-xl mx-auto">
                    @include('profile.partials.delete-user-form')
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
