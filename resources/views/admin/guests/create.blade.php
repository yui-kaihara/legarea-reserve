<x-app-layout>
    <x-slot name="navigation"></x-slot>
    <x-slot name="header">予約登録</x-slot>
    
@section('selectTimes')
    <div class="mb-8">
        <label for="times" class="flex gap-2 text-sm font-medium"><span class="px-1 py-0.5 bg-red-500 rounded-sm text-white text-xs font-normal">必須</span>開催回</label>
        第
        <select name="times" class="w-20 mt-2 px-4 border-gray-200 rounded-lg cursor-pointer" id="times">
@foreach ($times as $time)
@php
$selected = (old('times') == $time) ? ' selected="selected"' : '';
@endphp
            <option value="{{ $time }}"{{ $selected }}>{{ $time }}</option>
@endforeach
        </select>
        回
@error('times')
        <p class="mt-2 text-red-500 text-xs text-left">※{{ $message }}</p>
@enderror
    </div>
@endsection

    @include('common.guests.form', ['route' => route('admin.guests.store'), 'submitText' => '登録', 'formAddClass' => 'md:w-1/3'])
    


</x-app-layout>
