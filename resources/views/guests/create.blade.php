<x-app-layout>
    <x-slot name="header">株式会社LEGAREA 第{{ $times }}回交流会 申込フォーム</x-slot>

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
    
@section('selectTimes')
    <input type="hidden" name="times" value="{{ $event->times }}" />
@endsection

@section('checkbox')
    <div class="mt-8">
        <div class="flex gap-2 md:gap-3">
@php
$checked = (old('agreeCheck')) ? ' checked="checked"' : '';
@endphp
            <input type="checkbox" name="agreeCheck" class="mt-1 border-gray-400 rounded cursor-pointer" id="agreeCheck"{{ $checked }} />
            <label for="agreeCheck" class="font-normal text-sm leading-6">
                「個人情報の取扱いについて」に同意する。<br />
                個⼈情報の取り扱いについて、詳しくは弊社の<a href="https://www.legarea.jp/privacy" target="_blank" class="underline text-blue-500">プライバシーポリシー</a>をご覧ください。
            </label>
        </div>
@error('agreeCheck')
        <p class="mt-2 text-red-500 text-xs">※{{ $message }}</p>
@enderror
    </div>
@endsection
    
    @include('common.guests.form', ['route' => route('guests.store'), 'submitText' => '申込', 'formAddClass' => ''])

@else

    <div class="text-center">
        <div class="inline-block w-auto lg:w-2/5 mt-5 mx-3 md:mx-0 py-6 px-4 md:px-8 bg-white border border-gray-200 rounded-lg shadow text-left text-sm leading-6">
            <p class="mb-3 text-base md:text-lg font-semibold">株式会社LEGAREA 第{{ $times }}回交流会</p>
            <p class="mb-3">「第{{ $times }}回交流会」の回答の受け付けは終了しました。<br />間違いであると思われる場合は、以下までお問い合わせください。</p>
            <p>株式会社LEGAREA 営業担当<br />{{ config('contacts.name') }} （Tel：{{ config('contacts.tel') }}）</p>
        </div>
    </div>

@endif
    
</x-app-layout>