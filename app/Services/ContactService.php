<?php
declare(strict_types=1);

namespace App\Services;

use App\Models\Company;
use App\Models\Event;
use App\Models\Guest;
use App\Services\CompanyService;
use App\Services\GuestService;

class ContactService
{
    public function __construct(
        CompanyService $companyService,
        GuestService $guestService
    )
    {
        $this->companyService = $companyService;
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
        $nextTime = Event::max('times');
        
        //メールアドレスからドメイン部分を取得
        $domain = substr(strrchr($requests['email'], '@'), 1);
        
        //会社テーブルへの登録用データ
        $companyRequests = [
            'company_name' => $requests['company_name'],
            'domain' => $domain,
            'event_id' => $nextTime
        ];
        
        //ゲストテーブルへの登録用データ
        unset($requests['company_name']);
        $requests['event_id'] = $nextTime;
        
        //参加予定者（自分以外）
        $eventGuests = Guest::where('event_id', $nextTime)->where('email', '!=', $requests['email']);

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
        $existCompanyData = Company::where('domain', $domain)->first();

        if ($existCompanyData) {
            
            //会社の参加予定人数
            $companyGuestCount = $eventGuests->where('company_id', $existCompanyData->id)->count();
            
            //1社2名の定員をオーバーしている場合は登録しない
            if ($companyGuestCount >= 2) {
                
                //登録結果（不可）
                $guest->result = FALSE;
                
                //ゲストモデルを返却
                return $guest;
            }
            
            //会社のイベントIDが次回のものと一致していない場合
            if ($existCompanyData->event_id !== $nextTime) {
                
                //会社の更新データを設定
                $updateDatas = [
                    'count' => $existCompanyData->count + 1,
                    'event_id' => $nextTime
                ];
                
                //会社更新処理
                $this->companyService->update($updateDatas, $existCompanyData->id);
            }
            
            //ゲストテーブルに同じメールアドレスが存在するか確認
            $existGuestData = Guest::where('email', $requests['email'])->first();
            
            if ($existGuestData) {
                
                //ゲストの更新データを追加
                $requests['event_id'] = $nextTime;
                
                //ゲスト更新処理
                $this->guestService->update($requests, $existGuestData->id);
                $guest->company_id = $existGuestData->company_id;

            } else {
                
                //ゲストの登録データを追加
                $requests['company_id'] = $existCompanyData->id;
                
                //ゲスト新規登録処理
                $guest = $this->guestService->store($requests);
            }
            
        } else {
            
            //会社新規登録処理
            $company = $this->companyService->store($companyRequests);
            
            //ゲストの登録データを追加
            $requests['company_id'] = $company->id;
            
            //ゲスト新規登録処理
            $guest = $this->guestService->store($requests);
        }
        
        //登録結果を設定（完了）
        $guest->result = TRUE;

        //ゲストモデルを返却
        return $guest;
    }
    
    /**
     * 削除
     * 
     * @param int $id
     * @return void
     */
    public function destroy(int $id)
    {
        //該当ゲスト情報を取得
        $guest = Guest::find($id);
        
        //ゲスト削除処理
        $this->guestService->destroy($id);
        
        //同じ会社で次回参加予定者がいるか
        $nextTimeGuests = Guest::where([
            'event_id' => $guest->event_id,
            'company_id' => $guest->company_id
        ])->first();

        //いなければ会社としての参加をキャンセル
        if (!$nextTimeGuests) {
            $this->companyService->destroy($guest->company->id);
        }
    }
}
