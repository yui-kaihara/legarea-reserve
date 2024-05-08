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
        $eventGuest = new EventGuest();
        $eventGuest->fill($requests)->save();
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
        $eventGuest = EventGuest::where([
            'event_id' => $eventId,
            'guest_id' => $guestId
        ]);
        $eventGuest->delete();
    }
}
