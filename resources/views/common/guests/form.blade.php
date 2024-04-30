<div class="px-3 py-10 block md:flex md:justify-center">
    <form action="{{ $route }}" method="POST">
        @csrf

<!--予約画面-->
@php
$companyName = old('company_name');
$name = old('name');
$nameKana = old('name_kana');
$age = old('age');
$email = old('email');
$streamEmail = old('stream_email');
@endphp

<!--更新画面-->
@isset($guest)
        @method('PUT')
        
@php
        $companyName = $guest->company->company_name;
        $name = $guest->name;
        $nameKana = $guest->name_kana;
        $age = $guest->age;
        $email = $guest->email;
        if ($guest->stream_email) {
            $streamEmail = $guest->stream_email;
        }
@endphp
@endisset

        <div>
            <label class="flex gap-2 text-sm font-medium"><span class="px-1 py-0.5 bg-red-500 rounded-sm text-white text-xs font-normal">必須</span>会社名</label>
            <input type="text" name="company_name" value="{{ $companyName }}" placeholder="株式会社LEGAREA" class="w-full md:w-96 mt-2 px-4 border-gray-200 rounded-lg" />
@error('company_name')
            <p class="mt-2 text-red-500 text-xs">※{{ $message }}</p>
@enderror
        </div>
        <div class="mt-8">
            <label class="flex gap-2 text-sm font-medium"><span class="px-1 py-0.5 bg-red-500 rounded-sm text-white text-xs font-normal">必須</span>名前</label>
            <input type="text" name="name" value="{{ $name }}" placeholder="山田 太郎" class="w-full md:w-96 mt-2 px-4 border-gray-200 rounded-lg" />
@error('name')
            <p class="mt-2 text-red-500 text-xs">※{{ $message }}</p>
@enderror
        </div>
        <div class="mt-8">
            <label class="flex gap-2 text-sm font-medium"><span class="px-1 py-0.5 bg-red-500 rounded-sm text-white text-xs font-normal">必須</span>ふりがな</label>
            <input type="text" name="name_kana" value="{{ $nameKana }}" placeholder="やまだ たろう" class="w-full md:w-96 mt-2 px-4 border-gray-200 rounded-lg" />
@error('name_kana')
            <p class="mt-2 text-red-500 text-xs">※{{ $message }}</p>
@enderror
        </div>
        <div class="mt-8">
            <label class="flex gap-2 text-sm font-medium"><span class="px-1 py-0.5 bg-red-500 rounded-sm text-white text-xs font-normal">必須</span>年齢</label>
            <input type="number" name="age" value="{{ $age }}" placeholder="30" class="w-24 mt-2 px-4 border-gray-200 rounded-lg" /> 歳
@error('age')
            <p class="mt-2 text-red-500 text-xs">※{{ $message }}</p>
@enderror
        </div>
        <div class="mt-8">
            <label class="flex gap-2 text-sm font-medium"><span class="px-1 py-0.5 bg-red-500 rounded-sm text-white text-xs font-normal">必須</span>メールアドレス</label>
            <input type="email" name="email" value="{{ $email }}" placeholder="test@legarea.jp" class="w-full md:w-96 mt-2 px-4 border-gray-200 rounded-lg" />
@error('email')
            <p class="mt-2 text-red-500 text-xs">※{{ $message }}</p>
@enderror
        </div>
        <div class="mt-8">
            <label class="flex gap-2 text-sm font-medium"><span class="px-1 py-0.5 bg-gray-400 rounded-sm text-white text-xs font-normal">任意</span>LEGAREA配信希望の方</label>
            <input type="email" name="stream_email" value="{{ $streamEmail }}" placeholder="sales@legarea.jp" class="w-full md:w-96 mt-2 px-4 border-gray-200 rounded-lg" />
@error('stream_email')
            <p class="mt-2 text-red-500 text-xs">※{{ $message }}</p>
@enderror
        </div>
        <div class="flex justify-center mt-10">
            <input type="submit" value="{{ $submitText }}" class="w-40 cursor-pointer py-3 px-4 text-sm font-semibold rounded-lg border border-transparent bg-blue-100 text-blue-800 hover:bg-blue-200 disabled:opacity-50 disabled:pointer-events-none" />
        </div>
    </form>
</div>