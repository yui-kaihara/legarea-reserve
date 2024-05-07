@php
$times = '';
$email = '';
$guest = session('guest');
if ($guest) {
    $times = ' 第'.$guest->event->times.'回';
    $email = $guest->email.'宛てに';
}
@endphp
<x-app-layout>
    <x-slot name="header">株式会社LEGAREA{{ $times }}交流会 申込完了</x-slot>

    <div class="mt-20 p-2 text-center">
        <p>
            この度は、LEGAREA交流会にお申し込みいただきありがとうございました。<br class="hidden sm:block" />
            {{ $email }}確認メールを送信いたしますので、ご確認ください。</p>
    </div>
</x-app-layout>
