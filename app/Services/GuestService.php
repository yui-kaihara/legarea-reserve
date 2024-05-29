<?php
declare(strict_types=1);

namespace App\Services;

use App\Models\Event;
use App\Models\EventGuest;
use App\Models\Guest;
use Illuminate\Pagination\LengthAwarePaginator;

class GuestService
{
    /**
     * コンストラクタ
     * 
     * @param BlastmailService $blastmailService
     */
    public function __construct(BlastmailService $blastmailService)
    {
        $this->blastmailService = $blastmailService;
    }

    /**
     * 新規配信フラグ設定
     * 
     * @param array $requests
     * @return array
     */
    public function setIsNewStream(array $requests)
    {
        //新規配信フラグを初期化
        $newStreamFlag = FALSE;
        
        //配信用メールアドレスの入力がある場合
        if ($requests['stream_email']) {
            
            //ブラストメールへの反映をAPI経由で実行
            $newStreamFlag = $this->blastmailService->reflect($requests);
        }
        
        //新規配信フラグをリクエストに追加
        $requests['is_newStream'] = $newStreamFlag;
        
        return $requests;
    }

    /**
     * 一覧取得
     * 
     * @param int $times
     * @param string $status
     * @param int $page
     * @return array
     */
    public function getList(int $eventId, string $status = null, int $page = 10)
    {
        //クエリ作成
        $query = Guest::select('guests.*');

        //statusテキストを設定
        $statusText = '';

        if ($status) {
            
            //新規
            $statusText = '（新規）';
            $condition = '=';
            
            //2回目以降
            if ($status == 2) {
                $statusText = '（2回目以降）';
                $condition = '>';
            }
            
            //会社テーブルとJOIN
            $query->join('companies', function($join) use ($condition) {
                $join->on('guests.company_id', '=', 'companies.id');
                $join->where('companies.count', $condition, 1);
            });
        }

        //中間テーブルとJOIN
        $query->join('event_guests', function($join) use ($eventId) {
           $join->on('guests.id', '=', 'event_guests.guest_id');
           $join->on('guests.company_id', '=', 'event_guests.company_id');
           $join->where('event_guests.event_id', $eventId);
        });
        
        //件数の指定が0の場合、全件を設定
        if ($page === 0) {
            $page = $query->count();
        }

        //申込日時の降順にソート、ページネーションを設定して取得
        $guests = $query->orderby('event_guests.created_at', 'DESC')->paginate($page)->withQueryString();

        //配列を返却（ゲストモデル、新規or2回目以降）
        return [$guests, $statusText]; 
    }

    /**
     * 保存
     * 
     * @param array $requests
     * @return Guest
     */
    public function store(array $requests)
    {
        $guest = new Guest();
        return $guest->create($requests);
    }
    
    /**
     * 更新
     * 
     * @param array $requests
     * @param int $id
     * @return void
     */
    public function update(array $requests, int $id)
    {
        $guest = Guest::find($id);
        $guest->fill($requests)->save();
    }
}
