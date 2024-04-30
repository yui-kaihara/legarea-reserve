<x-app-layout>
    <x-slot name="navigation"></x-slot>
    <x-slot name="header">予約登録</x-slot>

    @include('common.guests.form', ['route' => route('admin.guests.store'), 'submitText' => '登録'])

</x-app-layout>
