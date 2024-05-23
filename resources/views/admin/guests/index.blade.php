<x-app-layout>
    <x-slot name="navigation"></x-slot>

@section('scripts')
    @vite(['resources/js/submit.js'])
@endsection

    <x-slot name="header">予約者一覧{{ $statusText }}</x-slot>

@if (session('flash_message'))
    <p class="px-4 py-3 bg-{{ session('messageColor') }}-100 text-{{ session('messageColor') }}-600 text-center font-semibold">
        {{ session('flash_message') }}
    </p>
@endif

    <div class="w-11/12 lg:w-5/6 mx-auto py-10">
        <form action="{{ route('admin.guests.download', ['event' => request()->input('event'), 'status' => request()->input('status')]) }}" method="POST" class="mb-2 text-right">
            @csrf
            <button class="cursor-pointer w-32 lg:w-44 py-2 lg:px-2 text-xs lg:text-sm font-semibold rounded-lg border border-transparent bg-blue-100 text-blue-800 hover:bg-blue-200 disabled:opacity-50 disabled:pointer-events-none">ダウンロード</button>
        </form>
        <form action="{{ route('admin.guests.import') }}" method="POST" enctype="multipart/form-data" class="flex justify-end items-baseline text-right">
            @csrf
            <div>
                <input type="file" name="uploadFile" accept="application/vnd.openxmlformats-officedocument.spreadsheetml.sheet" class="w-48 lg:w-72 text-xs lg:text-sm" />
@error('uploadFile')
                <p class="mt-2 text-red-500 text-xs text-left">※{{ $message }}</p>
@enderror
            </div>
            <button class="cursor-pointer w-32 lg:w-44 py-2 lg:px-2 text-xs lg:text-sm font-semibold rounded-lg border border-transparent bg-orange-100 text-orange-600 hover:bg-orange-200 disabled:opacity-50 disabled:pointer-events-none">インポート</button>
        </form>
        <div class="flex justify-between items-end mb-2">
            <form action="javascript:void(0)">
                @csrf
                第
                <select name="times" class="w-16 mt-2 px-4 border-gray-200 rounded-lg cursor-point is-submit">

@foreach ($times as $time)
@php
$url = Request::url();
$status = request()->input('status');
$url = $url.'?event='.$time;
$url = ($status) ? $url.'&status='.$status : $url;

$event = request()->input('event');
$selected = '';
if ($event == $time) {
    $selected = ' selected="selected"';
}
@endphp
                    <option value="{{ $url }}"{{ $selected }}>{{ $time }}</option>
@endforeach
                </select>
                回
            </form>
            <p class="mb-2 pr-1 text-sm text-gray-600 text-right">合計 <span class="text-lg font-semibold">{{ $guests->total() }}</span> 名</p>
        </div>

@if ($guests->total() > 0)

        <table class="block overflow-x-scroll md:overflow-x-auto text-center">
            <thead>
                <tr class="bg-gray-200 text-xs font-medium text-gray-600">
                    <th scope="col" class="sticky top-0 left-0 min-w-32 w-1/6 py-3 bg-gray-200">会社名</th>
                    <th scope="col" class="sticky top-0 left-32 w-1/12 py-3 bg-gray-200">名前</th>
                    <th scope="col" class="w-1/12 py-3">ふりがな</th>
                    <th scope="col" class="w-1/12 py-3">年齢</th>
                    <th scope="col" class="min-w-32 w-1/6 py-3">メールアドレス</th>
                    <th scope="col" class="min-w-32 w-1/6 py-3">配信用メールアドレス</th>
                    <th scope="col" class="w-1/12 py-3">新規</th>
                    <th scope="col" class="w-1/12 py-3"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-300">
@foreach ($guests as $guest)
                <tr>
                    <td class="sticky top-0 left-0 px-2 lg:px-6 py-4 bg-gray-100 text-sm text-gray-800">{{ $guest->company->company_name }}</td>
                    <td class="sticky top-0 left-32 px-2 lg:px-6 py-4 bg-gray-100 whitespace-nowrap text-sm text-gray-800">{{ $guest->name }}</td>
                    <td class="px-2 lg:px-6 py-4 whitespace-nowrap text-sm text-gray-800">{{ $guest->name_kana }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-800">{{ $guest->age }}歳</td>
                    <td class="px-6 py-4 break-all text-sm text-gray-800">{{ $guest->email }}</td>
                    <td class="px-6 py-4 break-all text-sm text-gray-800">{{ $guest->stream_email }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-800">{{ ($guest->company->count > 1) ? '' : '○'; }}</td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="flex items-center gap-0.5 text-xs">
                            <a href="{{ route('admin.guests.edit', [$guest->id]) }}" class="bg-white hover:bg-gray-100 text-gray-500 font-semibold py-2 px-2 border border-gray-400 rounded shadow">
                                編集
                            </a>
                            <form action="{{ route('admin.guests.destroy', [$guest->id]) }}" method="post">
                                @csrf
                                @method('DELETE')
                                <input type="hidden" name="times" value="{{ $guest->event[0]->times }}" />
                                <input type="submit" value="削除" onclick="return confirm('本当にキャンセルしますか？')" class="bg-white hover:bg-gray-100 text-gray-500 font-semibold py-2 px-2 border border-gray-400 rounded shadow cursor-pointer" />
                            </form>
                        </div>
                    </td>
                </tr>
@endforeach
            </tbody>
        </table>
        {{ $guests->links('vendor.pagination.tailwind-custom') }}
@else

        <p class="mt-10 text-center">現在予約はありません。</p>

@endif

    </div>
</x-app-layout>
