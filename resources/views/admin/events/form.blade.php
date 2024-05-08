<div class="md:flex py-10 px-3 justify-center">
    <form action="{{ $route }}" method="POST">
        @csrf
        
<!--予約画面-->
@php
$times = old('times');
$date = old('date');
$startTime = old('start_time');
$endTime = old('end_time');
$place = old('place');
$amount = old('amount');
$capacity = old('capacity');
@endphp

<!--更新画面-->
@isset($event)
        @method('PUT')
        
@php
        $times = $event->times;
        $date = $event->date->format('Y-m-d');
        $startTime = $event->start_time->format('H:i');
        $endTime = $event->end_time->format('H:i');
        $place = $event->place;
        $amount = $event->amount;
        $capacity = $event->capacity;
@endphp
@endisset

        <div>
            <label class="flex gap-2 text-sm font-medium"><span class="align-text-top px-1 py-0.5 bg-red-500 rounded-sm text-white text-xs">必須</span> 開催回</label>
            第 <input type="number" name="times" value="{{ $times }}" class="w-20 mt-2 px-4 border-gray-200 rounded-lg" /> 回
@error('times')
            <p class="mt-2 text-red-500 text-xs">※{{ $message }}</p>
@enderror
        </div>
        <div class="mt-8">
            <label class="flex gap-2 text-sm font-medium"><span class="align-text-top px-1 py-0.5 bg-red-500 rounded-sm text-white text-xs">必須</span> 開催日</label>
            <input type="date" name="date" value="{{ $date }}" class="w-full md:w-96 mt-2 px-4 border-gray-200 rounded-lg" />
@error('date')
            <p class="mt-2 text-red-500 text-xs">※{{ $message }}</p>
@enderror
        </div>
        <div class="mt-8">
            <label class="flex gap-2 text-sm font-medium"><span class="align-text-top px-1 py-0.5 bg-red-500 rounded-sm rounded-sm text-white text-xs">必須</span> 開始時間</label>
            <input type="time" name="start_time" value="{{ $startTime }}" class="w-full md:w-96 mt-2 px-4 border-gray-200 rounded-lg" />
@error('start_time')
            <p class="mt-2 text-red-500 text-xs">※{{ $message }}</p>
@enderror
        </div>
        <div class="mt-8">
            <label class="flex gap-2 text-sm font-medium"><span class="align-text-top px-1 py-0.5 bg-red-500 rounded-sm rounded-sm text-white text-xs">必須</span> 終了時間</label>
            <input type="time" name="end_time" value="{{ $endTime }}" class="w-full md:w-96 mt-2 px-4 border-gray-200 rounded-lg" />
@error('end_time')
            <p class="mt-2 text-red-500 text-xs">※{{ $message }}</p>
@enderror
        </div>
        <div class="mt-8">
            <label class="flex gap-2 text-sm font-medium"><span class="align-text-top px-1 py-0.5 bg-red-500 rounded-sm text-white text-xs">必須</span> 場所</label>
            <input type="text" name="place" value="{{ $place }}" class="w-full md:w-96 mt-2 px-4 border-gray-200 rounded-lg" />
@error('place')
            <p class="mt-2 text-red-500 text-xs">※{{ $message }}</p>
@enderror
        </div>
        <div class="mt-8">
            <label class="flex gap-2 text-sm font-medium"><span class="align-text-top px-1 py-0.5 bg-red-500 rounded-sm text-white text-xs">必須</span> 金額</label>
            <input type="number" name="amount" value="{{ $amount }}" class="w-24 mt-2 px-4 border-gray-200 rounded-lg" /> 円
@error('amount')
            <p class="mt-2 text-red-500 text-xs">※{{ $message }}</p>
@enderror
        </div>
        <div class="mt-8">
            <label class="flex gap-2 text-sm font-medium"><span class="align-text-top px-1 py-0.5 bg-red-500 rounded-sm text-white text-xs">必須</span> 定員</label>
            <input type="number" name="capacity" value="{{ $capacity }}" class="w-24 mt-2 px-4 border-gray-200 rounded-lg" /> 人
@error('capacity')
            <p class="mt-2 text-red-500 text-xs">※{{ $message }}</p>
@enderror
        </div>
        <div class="flex justify-center mt-10">
            <input type="submit" value="{{ $submitText }}" class="w-40 cursor-pointer py-3 px-4 text-sm font-semibold rounded-lg border border-transparent bg-blue-100 text-blue-800 hover:bg-blue-200 disabled:opacity-50 disabled:pointer-events-none" />
        </div>
    </form>
</div>