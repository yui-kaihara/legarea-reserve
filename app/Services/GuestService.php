<?php
declare(strict_types=1);

namespace App\Services;

use App\Models\Event;
use App\Models\EventGuest;
use App\Models\Guest;
use App\Services\EventService;
use Illuminate\Pagination\LengthAwarePaginator;

class GuestService
{
    /**
     * コンストラクタ
     * 
     * @param EventService $eventService
     */
    public function __construct(EventService $eventService)
    {
        $this->eventService = $eventService;
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
        $eventGuestIds = EventGuest::where('event_guests.event_id', $eventId)->pluck('guest_id');
        $query = Guest::select('guests.*')->whereIn('guests.id', $eventGuestIds);

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
        
        //件数の指定が0の場合、全件を設定
        if ($page === 0) {
            $page = $query->count();
        }

        //会社IDの昇順にソート、ページネーションを設定して取得
        $guests = $query->orderby('company_id')->paginate($page);
        
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
