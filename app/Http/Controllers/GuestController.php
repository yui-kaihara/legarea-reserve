<?php
declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\ContactFormRequest;
use App\Models\Event;
use App\Services\BlastmailService;
use App\Services\ContactService;
use App\Services\EventService;
use App\Services\SendMailService;
use Illuminate\Http\Request;
use Hashids\Hashids;

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
     * @param Request $request
     * @return Illuminate\View\View
     */
    public function create(Request $request)
    {
        //idパラメータを取得
        $id = ($request->input('id')) ?? '';
        
        //idをデコード
        $hashids = new Hashids('', 8);
        $decodeIds = $hashids->decode($id);
        
        //idが取得できなければ404
        if (!$decodeIds) {
            abort(404);
        }
        
        $decodeId = $hashids->decode($id)[0];

        //交流会情報を取得
        $event = Event::find($decodeId);
        
        //参加人数を取得
        $guestCount = $this->contactService->guestCount($decodeId);
        
        //公開状況を取得
        $publicStatus = $this->eventService->getPublicStatus($event, $guestCount);

        //交流会詳細を初期化
        $eventDetail = [];

        //公開であれば、交流会詳細を取得
        if ($publicStatus) {
            $eventDetail = $this->eventService->getDetail($event->times, TRUE);
        }
        
        //ビューに渡す
        return view('guests.create', ['times' => $event->times, 'event' => $eventDetail]);
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
        
        //開催回を設定（登録不可の場合リレーションできないため）
        $guest->times = $requests['times'];
        
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
