<?php
declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\ContactFormRequest;
use App\Models\Event;
use App\Services\BlastmailService;
use App\Services\ContactService;
use App\Services\EventService;
use App\Services\SendMailService;

class GuestController extends Controller
{
    /**
     * コンストラクタ
     * 
     * @param BlastmailService $blastmailService
     * @param ContactService $contactService
     * @param EventService $eventService
     * @param SendMailService $sendMailService
     */
    public function __construct(
        BlastmailService $blastmailService,
        ContactService $contactService,
        EventService $eventService,
        SendMailService $sendMailService
    )
    {
        $this->blastmailService = $blastmailService;
        $this->contactService = $contactService;
        $this->eventService = $eventService;
        $this->sendMailService = $sendMailService;
    }

    /**
     * 登録画面表示
     * 
     * @return Illuminate\View\View
     */
    public function create()
    {
        $times = Event::max('times');
        
        //交流会データを取得
        $event = $this->eventService->getDetail((int)$times);
        
        //ビューに渡す
        return view('guests.create', ['event' => $event]);
    }

    /**
     * 保存
     * 
     * @param ContactFormRequest $request
     * @return Illuminate\View\View
     */
    public function store(ContactFormRequest $request)
    {
        //リクエストデータを配列化
        $requests = $request->all();

        //登録処理
        $guest = $this->contactService->store($requests);
        
        //メール送信処理
        $this->sendMailService->send($guest);
        
        //配信用メールアドレスの入力がある場合
        if ($requests['stream_email']) {
            
            //ブラストメールへの反映をAPI経由で実行
            $this->blastmailService->reflect($requests, FALSE);
        }
        
        //新たにトークンを作成（多重送信防止）
        $request->session()->regenerateToken();
        
        //予約完了画面に遷移
       return redirect(route('guests.complete'))->with(['guest' => $guest]);
    }
    
    /**
     * 予約完了画面表示
     * 
     * @return Illuminate\View\View
     */
    public function complete()
    {
        //ビューに渡す
        return view('guests.complete');
    }
}
