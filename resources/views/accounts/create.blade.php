<x-app-layout>
    <x-slot name="header">
        <h2 class="text-2xl font-bold text-gray-800">{{ isset($account) ? 'Editar Cuenta' : 'Nueva Cuenta' }}</h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="bg-white rounded-2xl p-8 shadow-sm border border-gray-100">
                @include('accounts.form')
</x-app-layout>