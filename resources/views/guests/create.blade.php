<x-app-layout>
    <x-slot name="header">株式会社LEGAREA {{ ($event) ? '第'.$event->times.'回' : '' }}交流会 申込フォーム</x-slot>

@if ($event)

    <div class="text-center">
        <dl class="inline-block w-auto lg:w-2/5 mt-5 mx-3 md:mx-0 py-6 px-4 md:px-8 bg-white border border-gray-200 rounded-lg shadow text-left text-sm">
            <div class="flex items-baseline md:items-center">
                <dt class="w-1/6 font-semibold">開催日</dt>
                <dd class="w-5/6 md:text-base">{{ $event->date->isoFormat('Y年M月D日(ddd)') }}</dd>
            </div>
            <div class="flex items-baseline">
                <dt class="w-1/6 font-semibold">開催地</dt>
                <dd class="w-5/6 md:text-base">{{ $event->place }}</dd>
            </div>
            <div class="flex items-baseline">
                <dt class="w-1/6 font-semibold">【受付】</dt>
                <dd class="w-5/6 md:text-base">{{ $event->start_time->format('H:i') }}~※受け付け順に交流開始</dd>
            </div>
            <div class="flex items-baseline">
                <dt class="w-1/6 font-semibold">【終了】</dt>
                <dd class="w-5/6 md:text-base">~{{ $event->end_time->format('H:i') }}</dd>
            </div>
            <div class="flex items-baseline">
                <dt class="w-1/6 font-semibold">金額</dt>
                <dd class="w-5/6 md:text-base">{{ $event->amount }}円/1人※LEGAREA交流会に初参加の企業様無料！ </dd>
            </div>
            <div class="flex items-baseline">
                <dt class="w-1/6 font-semibold">定員</dt>
                <dd class="w-5/6 md:text-base">{{ $event->capacity }}名(1社につき2名まで)</dd>
            </div>
        </dl>
    </div>
    
    @include('common.guests.form', ['route' => route('guests.store'), 'submitText' => '申込'])

@else

    <p class="mt-20 mb-10 text-center">現在、開催予定の交流会はございません。<br />次回開催までしばらくお待ちください。</p>

@endif
    
</x-app-layout>