<?php
declare(strict_types=1);

namespace App\Services;

use App\Models\Company;
use App\Models\Event;
use App\Models\EventGuest;
use App\Models\Guest;
use App\Services\CompanyService;
use App\Services\EventService;
use App\Services\EventGuestService;
use App\Services\GuestService;

class ContactService
{
    /**
     * コンストラクタ
     * 
     * @param CompanyService $companyService
     * @param EventService $eventService
     * @param EventGuestService $eventGuestService
     * @param GuestService $guestService
     */
    public function __construct(
        CompanyService $companyService,
        EventService $eventService,
        EventGuestService $eventGuestService,
        GuestService $guestService
    )
    {
        $this->companyService = $companyService;
        $this->eventService = $eventService;
        $this->eventGuestService = $eventGuestService;
        $this->guestService = $guestService;
    }

    /**
     * 保存
     * 
     * @param array $requests
     * @return Guest
     */
    public function store(array $requests)
    {
        //開催回を取得
        $times = (int)$requests['times'];

        //全体の参加予定人数を取得
        $existGuest = Guest::where('email', '=', $requests['email'])->first(); //申込者のデータを取得
        $guestCount = $this->guestCount($times, $existGuest);
        
        //該当の交流会情報を取得
        $event = $this->eventService->getDetail($times);
        
        //交流会の定員を取得
        $capacity = $event->capacity;
        
        //返却するゲストモデルを初期化
        $guest = new Guest($requests);

        //定員をオーバーしている場合は登録しない
        if ($guestCount >= $capacity) {
            
            //ゲストモデルを返却
            $guest->result = FALSE; //登録結果（不可）
            return $guest;
        }
        
        //会社IDを初期化
        $companyId = 0;
        
        //メールアドレスからドメイン部分を取得
        $domain = substr(strrchr($requests['email'], '@'), 1);
        
        //会社テーブルへの登録用データ
        $companyRequests = [
            'company_name' => $requests['company_name'],
            'domain' => $domain
        ];
        
        //ゲストテーブルへの登録用データ
        unset($requests['company_name']);
        
        //会社テーブルに同じドメインのメールアドレスが存在するか確認
        $existCompanyData = Company::where('domain', '=', $domain)->first();

        if ($existCompanyData) {
            
            //会社IDを設定
            $companyId = $existCompanyData->id;
            
            //会社の参加予定人数
            $companyGuestCount = $this->guestCount($times, $existGuest, $companyId);
            
            //1社2名の定員をオーバーしている場合は登録しない
            if ($companyGuestCount >= 2) {

                //ゲストモデルを返却
                $guest->result = FALSE;
                return $guest;
            }

            //ゲストテーブルに同じメールアドレスが存在する場合
            if ($existGuest) {
                
                //ゲスト更新処理
                $this->guestService->update($requests, $existGuest->id);
                $guest->id = $existGuest->id;
                $guest->company_id = $existGuest->company_id;

            } else {
                
                //ゲスト新規登録処理
                $requests['company_id'] = $companyId;
                $guest = $this->guestService->store($requests);
            }
            
        } else {
            
            //会社新規登録処理
            $company = $this->companyService->store($companyRequests);
            
            //ゲスト新規登録処理
            $companyId = $company->id; //会社IDを設定
            $requests['company_id'] = $companyId;
            $guest = $this->guestService->store($requests);
        }

        //開催回ごとのゲスト新規登録処理
        $eventGuestRequests = [
            'event_id' => $event->id,
            'guest_id' => $guest->id,
            'company_id' => $companyId
        ];
        $this->eventGuestService->store($eventGuestRequests);
        
        //会社更新処理
        $count = $this->companyAttendCount($companyId);
        $updateDatas = ['count' => $count];
        $this->companyService->update($updateDatas, $companyId);

        //登録結果を設定（完了）
        $guest->result = TRUE;

        //ゲストモデルを返却
        return $guest;
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
        //開催回ごとのゲストデータを削除
        $this->eventGuestService->destroy($eventId, $guestId);
        
        //会社IDを取得
        $guest = Guest::find($guestId);
        $companyId = $guest->company->id;
        
        //会社の更新データを設定
        $count = $this->companyAttendCount($companyId);
        $updateDatas = ['count' => $count];
        
        //会社更新処理
        $this->companyService->update($updateDatas, $companyId);
    }
    
    /**
     * 会社の通算参加カウント
     * 
     * @param int $companyId
     * @return int
     */
    public function companyAttendCount(int $companyId)
    {
        //会社IDが一致するデータから交流会IDを抽出し、重複を除外
        $eventIds = EventGuest::where('company_id', '=', $companyId)->pluck('event_id')->unique();
        
        //カウント
        $count = $eventIds->count();

        return $count;
    }
    
    /**
     * 参加者カウント
     * 
     * @param int $times
     * @param Guest $existGuest
     * @param int $companyId
     * @return int
     */
    public function guestCount(int $times, Guest $existGuest = NULL, int $companyId = NULL)
    {
        //該当回の交流会に参加するゲストを取得
        $eventGuests = EventGuest::where('event_id', '=', $times);

        //申込者がすでに登録済みの場合
        if ($existGuest) {
            
            //ゲストIDが一致しないデータ（自分以外）を取得
            $eventGuests = $eventGuests->where('guest_id', '!=', $existGuest->id);
        }
        
        //会社の参加人数をカウントする場合
        if ($companyId) {
            $eventGuests->where('company_id', '=', $companyId);
        }

        //参加者をカウント
        $guestCount = $eventGuests->count();
        
        return $guestCount;
    }
}
