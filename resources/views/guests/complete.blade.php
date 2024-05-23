@php
$times = '';
$email = '';
$guest = session('guest');
if ($guest) {
    $times = '第'.$guest->times.'回';
    $email = $guest->email.'宛てに';
}
@endphp
<x-app-layout>
    <x-slot name="title">{{ $times }}SES交流会 申込完了｜株式会社LEGAREA</x-slot>
    <x-slot name="header">株式会社LEGAREA {{ $times }}SES交流会 申込完了</x-slot>
    <div class="text-center">
        <div class="inline-block w-auto lg:w-2/5 mt-5 mx-3 md:mx-0 py-6 px-4 md:px-8 bg-white border border-gray-200 rounded-lg shadow text-left text-sm leading-6">
            <p class="mb-3">
                この度は、LEGAREA交流会にお申し込みいただきありがとうございました。<br />
                {{ $email }}確認メールを送信いたしますので、ご確認ください。
            </p>
            <p class="mb-3">メールが届かない場合は、以下までお問い合わせください。</p>
            <p>
                株式会社LEGAREA 営業担当<br />
                {{ config('contacts.name') }} （Tel：{{ config('contacts.tel') }}）
            </p>
        </div>
    </div>
</x-app-layout>
