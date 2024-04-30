<x-app-layout>
    <x-slot name="navigation"></x-slot>
    <x-slot name="header">交流会内容 編集</x-slot>


    @include('admin.events.form', ['route' => route('admin.events.update', [$event]), 'submitText' => '更新'])

</x-app-layout>
