<?php
declare(strict_types=1);

namespace App\Services;

use App\Models\Event;
use Carbon\Carbon;

class EventService
{
    /**
     * 一覧表示
     * 
     * @return Event
     */
    public function getList()
    {
        //開催回の降順に10件ずつ取得
        $events = Event::orderby('times', 'desc')->paginate(10);
        return $events;
    }
    
    /**
     * 詳細取得
     * 
     * @param int $times
     * @return Event
     */
    public function getDetail(int $times)
    {
        //交流会データを取得
        $event = Event::where('times', $times)->first();
        return $event;
    }
    
    /**
     * 保存
     * 
     * @param array $requests
     * @return void
     */
    public function store(array $requests)
    {
        $event = new Event();
        $event->fill($requests)->save();
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
        $event = Event::find($id);
        $event->fill($requests)->save();
    }
    
    /**
     * 削除
     * 
     * @param int $id
     * @return void
     */
    public function destroy(int $id)
    {
        $event = Event::find($id);
        $event->delete();
    }
    
    /**
     * 公開状況を取得
     * 
     * @param Event $event
     * @param int $guestCount
     * @return void
     */
    public function getPublicStatus(Event $event, int $guestCount)
    {
        //公開状況を初期化（非公開）
        $publicStatus = FALSE;
        
        //回答期限日時
        $limitDateTime = Carbon::parse($event->date)->subDay()->setTime(23, 59, 59);
        
        //「定員オーバーでない+回答期限より前」または「公開フラグが2」であれば、公開
        if ((($guestCount < $event->capacity) && ($limitDateTime >= now())) || ($event->is_public === 2)) {
            $publicStatus = TRUE;
        }
        
        return $publicStatus;
    }
}
