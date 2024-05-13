<?php
declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\EventFormRequest;
use App\Models\Event;
use App\Services\EventService;
use Hashids\Hashids;

class EventController extends Controller
{
    /**
     * コンストラクタ
     * 
     * @param EventService $eventService
     */
    public function __construct(
        EventService $eventService
    )
    {
        $this->eventService = $eventService;
    }

    /**
     * 一覧表示
     * 
     * @return Illuminate\View\View
     */
    public function index()
    {
        //交流会一覧を取得
        $events = $this->eventService->getList();
        
        //ハッシュ後の文字数指定  
        $hashids = new Hashids('', 8);
        
        //エンコードしたIDを追加
        foreach ($events as $event) {
            
            $encodeId = $hashids->encode($event->id);
            $event->hashId = $encodeId;
        }

        return view('admin.events.index', ['events' => $events]);
    }

    /**
     * 登録画面表示
     * 
     * @return Illuminate\View\View
     */
    public function create()
    {
        return view('admin.events.create');
    }

    /**
     * 保存
     * 
     * @param EventFormRequest $request
     * @return Illuminate\View\View
     */
    public function store(EventFormRequest $request)
    {
        $this->eventService->store($request->all());
        return redirect(route('admin.events.index'))->with('flash_message', '登録が完了しました。');
        
    }

    /**
     * 編集画面表示
     * 
     * @param int $id
     * @return Illuminate\View\View
     */
    public function edit(int $id)
    {
        $event = Event::find($id);
        return view('admin.events.edit', ['event' => $event]);
    }

    /**
     * 更新
     * 
     * @param Request $request
     * @param int $id
     * @return Illuminate\View\View
     */
    public function update(EventFormRequest $request, int $id)
    {
        $this->eventService->update($request->all(), $id);
        return redirect(route('admin.events.index'))->with('flash_message', '更新が完了しました。');
    }

    /**
     * 削除
     * 
     * @param int $id
     * @return Illuminate\View\View
     */
    public function destroy(int $id)
    {
        $this->eventService->destroy($id);
        return redirect(route('admin.events.index'))->with('flash_message', '削除が完了しました。');
    }
}
