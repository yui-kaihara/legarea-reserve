<?php
declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\ContactFormRequest;
use App\Models\Guest;
use App\Services\BlastmailService;
use App\Services\CompanyService;
use App\Services\ContactService;
use App\Services\DownloadService;
use App\Services\GuestService;
use Illuminate\Http\Request;

class GuestController extends Controller
{
    /**
     * コンストラクタ
     * 
     * @param BlastmailService $blastmailService
     * @param CompanyService $companyService
     * @param ContactService $contactService
     * @param DownloadService $downloadService
     * @param GuestService $guestService
     */
    public function __construct(
        BlastmailService $blastmailService,
        CompanyService $companyService,
        ContactService $contactService,
        DownloadService $downloadService,
        GuestService $guestService
    )
    {
        $this->blastmailService = $blastmailService;
        $this->companyService = $companyService;
        $this->contactService = $contactService;
        $this->downloadService = $downloadService;
        $this->guestService = $guestService;
    }

    /**
     * 一覧表示
     * 
     * @param Request $request
     * @return Illuminate\View\View
     */
    public function index(Request $request)
    {
        //statusパラメータを取得
        $status = $request->input('status');

        //ゲスト一覧を取得
        $results = $this->guestService->getList($status);

        //ビューに渡す
        return view('admin.guests.index', ['guests' => $results[0], 'nextTime' => $results[1], 'statusText' => $results[2]]);
    }

    /**
     * 登録画面表示
     * 
     * @return Illuminate\View\View
     */
    public function create()
    {
        return view('admin.guests.create');
    }

    /**
     * 保存
     * 
     * @param ContactFormRequest $request
     * @return Illuminate\View\View
     */
    public function store(ContactFormRequest $request)
    {
        //登録処理
        $guest = $this->contactService->store($request->all());
        
        //配信用メールアドレスの入力がある場合
        if ($request['stream_email']) {
            
            //ブラストメールへの反映をAPI経由で実行
            $this->blastmailService->reflect($request->all());
        }
        
        //フラッシュメッセージ用に設定
        $flashMessage = '定員オーバーのため登録できませんでした。';
        $messageColor = 'red';
        
        if ($guest->result) {
            $flashMessage = '登録が完了しました。';
            $messageColor = 'blue';
        }
        
        //一覧画面にリダイレクト
        return redirect(route('admin.guests.index'))->with(['flash_message' => $flashMessage, 'messageColor' => $messageColor]);
        
    }

    /**
     * 編集画面表示
     * 
     * @param int $id
     * @return Illuminate\View\View
     */
    public function edit(int $id)
    {
        $guest = Guest::find($id);
        return view('admin.guests.edit', ['guest' => $guest]);
    }

    /**
     * 更新
     * 
     * @param ContactFormRequest $request
     * @param int $id
     * @return Illuminate\View\View
     */
    public function update(ContactFormRequest $request, int $id)
    {
        //リクエストデータを取得
        $requests = $request->all();
        
        //該当のゲスト情報を取得
        $guest = Guest::find($id);
        
        //会社更新処理
        $this->companyService->update(['company_name' => $requests['company_name']], $guest->company->id);

        //ブラストメールへの反映をAPI経由で実行
        $this->blastmailService->reflect($requests);

        //リクエストデータから会社名を除外
        unset($requests['company_name']);
        
        //ゲスト更新処理
        $this->guestService->update($requests, $id);
        
        //一覧画面にリダイレクト
        return redirect(route('admin.guests.index'))->with(['flash_message' => '更新が完了しました。', 'messageColor' => 'blue']);
    }

    /**
     * 削除
     * 
     * @param int $id
     * @return Illuminate\View\View
     */
    public function destroy(int $id)
    {
        //削除処理
        $this->contactService->destroy($id);
        
        //一覧画面にリダイレクト
        return redirect(route('admin.guests.index'))->with(['flash_message' => 'キャンセルが完了しました。', 'messageColor' => 'blue']);
    }
    
    /**
     * ダウンロード
     * 
     * @param Request $request
     * @param void
     */
    public function download(Request $request)
    {
        //ステータスパラメータを取得
        $status = $request->input('status');

        //ゲスト一覧関連のデータを取得
        $results = $this->guestService->getList($status, 0);

        //ゲスト一覧
        $guests = $results[0]->all();
        
        //追加ファイル名
        $addFileName = $results[2];

        //Excelダウンロード
        $this->downloadService->download($guests, $addFileName);

    }
}