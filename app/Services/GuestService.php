<?php
declare(strict_types=1);

namespace App\Services;

use App\Models\Event;
use App\Models\Guest;
use Illuminate\Pagination\LengthAwarePaginator;

class GuestService
{
    /**
     * 一覧取得
     * 
     * @param string $status
     * @param int $page
     * @return array
     */
    public function getList(string $status = null, int $page = 10)
    {
        //次の開催回を取得
        $nextTime = Event::max('times');
        
        //クエリ作成
        $query = Guest::select('guests.*')->where('guests.event_id', '=', $nextTime);
        
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
        
        //配列を返却（ゲストモデル、開催回、新規or2回目以降）
        return [$guests, $nextTime, $statusText]; 
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
    
    /**
     * 削除
     * 
     * @param int $id
     * @return void
     */
    public function destroy(int $id)
    {
        $guest = Guest::find($id);
        
        //交流会IDをマイナス1
        $requests = ['event_id' => $guest->event_id - 1];
        
        //更新処理
        $this->update($requests, $id);
    }
}
