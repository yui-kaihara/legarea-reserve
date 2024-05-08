<?php
declare(strict_types=1);

namespace App\Services;

use App\Models\Company;
use App\Models\Event;
use App\Models\EventGuest;
use App\Models\Guest;
use App\Services\CompanyService;
use App\Services\EventGuestService;
use App\Services\GuestService;

class ContactService
{
    /**
     * コンストラクタ
     * 
     * @param CompanyService $companyService
     * @param EventGuestService $eventGuestService
     * @param GuestService $guestService
     */
    public function __construct(
        CompanyService $companyService,
        EventGuestService $eventGuestService,
        GuestService $guestService
    )
    {
        $this->companyService = $companyService;
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
        //次の開催回を取得
        $nextTime = $requests['times'];
        
        //メールアドレスからドメイン部分を取得
        $domain = substr(strrchr($requests['email'], '@'), 1);
        
        //会社テーブルへの登録用データ
        $companyRequests = [
            'company_name' => $requests['company_name'],
            'domain' => $domain
        ];
        
        //ゲストテーブルへの登録用データ
        unset($requests['company_name']);

        //参加予定者（自分以外）
        $guestIds = Guest::where('email', '!=', $requests['email'])->pluck('id');
        $eventGuests = EventGuest::where('event_id', '=', $nextTime);

        if ($guestIds->count() > 0) {
            $eventGuests = $eventGuests->whereIn('guest_id', $guestIds);
        }

        //全体の参加予定人数
        $guestCount = $eventGuests->count();
        
        //定員
        $capacity = Event::where('times', $nextTime)->first()->capacity;
        
        //返却するゲストモデルを初期化
        $guest = new Guest($requests);
        
        //定員をオーバーしている場合は登録しない
        if ($guestCount >= $capacity) {
            
            //登録結果（不可）
            $guest->result = FALSE;
            
            //ゲストモデルを返却
            return $guest;
        }
        
        //会社テーブルに同じドメインのメールアドレスが存在するか確認
        $existCompanyData = Company::where('domain', '=', $domain)->first();
        
        //会社IDを初期化
        $companyId = 0;

        if ($existCompanyData) {
            
            //会社IDを設定
            $companyId = $existCompanyData->id;
            
            //会社の参加予定人数
            $companyGuestCount = $eventGuests->where('company_id', $companyId)->count();
            
            //1社2名の定員をオーバーしている場合は登録しない
            if ($companyGuestCount >= 2) {
                
                //登録結果（不可）
                $guest->result = FALSE;
                
                //ゲストモデルを返却
                return $guest;
            }

            //ゲストテーブルに同じメールアドレスが存在するか確認
            $existGuestData = Guest::where('email', $requests['email'])->first();
            
            if ($existGuestData) {
                
                //ゲスト更新処理
                $this->guestService->update($requests, $existGuestData->id);
                $guest->company_id = $existGuestData->company_id;

            } else {
                
                //ゲストの登録データを追加
                $requests['company_id'] = $companyId;
                
                //ゲスト新規登録処理
                $guest = $this->guestService->store($requests);
            }
            
        } else {
            
            //会社新規登録処理
            $company = $this->companyService->store($companyRequests);
            
            //会社IDを設定
            $companyId = $company->id;
            
            //ゲストの登録データを追加
            $requests['company_id'] = $companyId;
            
            //ゲスト新規登録処理
            $guest = $this->guestService->store($requests);
        }
        
        //開催回ごとのゲストデータを設定
        $eventGuestRequests = [
            'event_id' => $nextTime,
            'guest_id' => $guest->id,
            'company_id' => $companyId
        ];
        
        //開催回ごとのゲスト新規登録処理
        $this->eventGuestService->store($eventGuestRequests);
        
        //会社の更新データを設定
        $count = EventGuest::where('company_id', '=', $companyId)->pluck('event_id')->unique()->count();
        $updateDatas = ['count' => $count];
        
        //会社更新処理
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
        
        $guest = Guest::find($guestId);
        
        $companyId = $guest->company->id;
        
        //会社の更新データを設定
        $count = EventGuest::where('company_id', '=', $companyId)->pluck('event_id')->unique()->count();
        $updateDatas = ['count' => $count];
        
        //会社更新処理
        $this->companyService->update($updateDatas, $companyId);
    }
}
