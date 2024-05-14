<x-app-layout>
    <x-slot name="navigation"></x-slot>
    <x-slot name="script">js/copyText.js</x-slot>
    <x-slot name="header">交流会一覧</x-slot>

@if (session('flash_message'))
    <p class="px-4 py-3 bg-blue-100 text-blue-600 text-center font-semibold text-sm md:text-base">
        {{ session('flash_message') }}
    </p>
@endif

@if ($events->total() > 0)

    <div class="w-11/12 lg:w-2/3 mx-auto py-10">
        <table class="block overflow-x-scroll md:overflow-x-auto text-center">
            <thead>
                <tr class="bg-gray-200 text-xs font-medium text-gray-500">
                    <th scope="col" class="sticky top-0 left-0 w-1/12 py-3 bg-gray-200">開催回</th>
                    <th scope="col" class="w-1/12 py-3">開催日</th>
                    <th scope="col" class="w-1/12 py-3">開始時間</th>
                    <th scope="col" class="w-1/12 py-3">終了時間</th>
                    <th scope="col" class="min-w-60 w-5/12 py-3">場所</th>
                    <th scope="col" class="w-1/12 py-3">金額</th>
                    <th scope="col" class="w-1/12 py-3">定員</th>
                    <th scope="col" class="w-1/12 py-3">公開</th>
                    <th scope="col" class="w-1/12 py-3"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-300">
@foreach ($events as $event)
                <tr>
                    <td class="sticky top-0 left-0 px-6 py-4 bg-gray-100 whitespace-nowrap text-sm text-gray-800">{{ $event->times }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-800">{{ $event->date->format('Y-n-d') }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-800">{{ $event->start_time->format('H:i') }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-800">{{ $event->end_time->format('H:i') }}</td>
                    <td class="px-6 py-4 text-sm text-gray-800">{{ $event->place }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-800">{{ $event->amount }}円</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-800">{{ $event->capacity }}人</td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <label>
                            <input v-model="isCheck" type="checkbox" class="peer sr-only" />
                            <span class="block w-[2em] cursor-pointer bg-gray-500 rounded-full p-[1px] after:block after:h-[1em] after:w-[1em] after:rounded-full after:bg-white after:transition peer-checked:bg-blue-500 peer-checked:after:translate-x-[calc(100%-2px)]"></span>
                        </label>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="flex items-center text-xs">
                            <a href="javascript:void(0)" data-hash-id="{{ $event->hashId }}" data-times="{{ $event->times }}" class="bg-white hover:bg-gray-100 text-gray-500 font-semibold py-2 px-2 border border-gray-400 rounded shadow is-copyText">
                                URL
                            </a>
                            <a href="{{ route('admin.events.edit', [$event]) }}" class="bg-white hover:bg-gray-100 text-gray-500 font-semibold py-2 px-2 border border-gray-400 rounded shadow">
                                編集
                            </a>
                            <form action="{{ route('admin.events.destroy', [$event]) }}" method="post">
                                @csrf
                                @method('DELETE')
                                <input type="submit" value="削除" onclick="return confirm('本当に削除しますか？')" class="bg-white hover:bg-gray-100 text-gray-500 font-semibold py-2 px-2 border border-gray-400 rounded shadow cursor-pointer" />
                            </form>
                        </div>
                    </td>
                </tr>
@endforeach
            </tbody>
        </table>
        {{ $events->links('vendor.pagination.tailwind1') }}
    </div>
    
@else

    <p class="mt-10 text-center">交流会の登録がありません。</p>

@endif

</x-app-layout>
