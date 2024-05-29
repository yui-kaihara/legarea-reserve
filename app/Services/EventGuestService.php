<?php
declare(strict_types=1);

namespace App\Services;

use App\Models\EventGuest;

class EventGuestService
{
    /**
     * 一覧表示
     * 
     * @param int $eventId
     * @return EventGuest
     */
    public function getList(int $eventId)
    {
        $eventGuests = EventGuest::where('event_id', '=', $eventId);
        return $eventGuests;
    }
    
    /**
     * 保存
     * 
     * @param array $requests
     * @return void
     */
    public function store(array $requests)
    {
        //既に登録済みではないか確認
        $eventGuests = EventGuest::where([
            'event_id' => $requests['event_id'],
            'guest_id' => $requests['guest_id'],
            'company_id' => $requests['company_id']
        ])->get();

        if ($eventGuests->isEmpty()) {
            $eventGuest = new EventGuest();
            $eventGuest->fill($requests)->save();
        }
    }
    
    /**
     * 削除
     * 
     * @param int $eventId
     * @param int $guestId
     * @return void
     */
    public function destroy(int $eventId, int $guestId)
    {
        //交流会IDとゲストIDが一致するものを削除
        $eventGuest = EventGuest::where([
            'event_id' => $eventId,
            'guest_id' => $guestId
        ]);
        $eventGuest->delete();
    }
}
