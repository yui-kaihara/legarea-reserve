<x-app-layout>
    <x-slot name="navigation"></x-slot>
    <x-slot name="header">予約内容 編集</x-slot>

    @include('common.guests.form', ['route' => route('admin.guests.update', [$guest]), 'submitText' => '更新'])

</x-app-layout>
