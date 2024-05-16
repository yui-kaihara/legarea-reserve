<x-app-layout>
    <x-slot name="navigation"></x-slot>
    <x-slot name="header">予約内容 編集</x-slot>

@section('selectTimes')
    <input type="hidden" name="times" value="{{ $guest->event[0]->times }}" />
@endsection

    @include('common.guests.form', ['route' => route('admin.guests.update', [$guest]), 'submitText' => '更新', 'formAddClass' => 'md:w-1/3'])

</x-app-layout>
