<x-app-layout>
    <x-slot name="navigation"></x-slot>
    <x-slot name="header">交流会登録</x-slot>


    @include('admin.events.form', ['route' => route('admin.events.store'), 'submitText' => '登録'])

</x-app-layout>
