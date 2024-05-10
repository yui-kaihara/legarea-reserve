<?php
declare(strict_types=1);

namespace App\Services;

use App\Models\Event;

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
        //開催回の日付が今日以降なら、交流会データを取得
        //TODO:日付のチェックが必要か確認するwhere('date', '>=', now())
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
}
