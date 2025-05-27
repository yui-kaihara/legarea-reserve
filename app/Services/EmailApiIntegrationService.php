<?php
declare(strict_types=1);

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class EmailApiIntegrationService
{
    /**
     * 入力されたメールアドレスが存在しているかチェック
     *
     * @param string $streamEmail
     * @return boolean
     */
    public function checkEmail(string $streamEmail)
    {
        $apiToken = env('MAIL_SERVICE_API_TOKEN');
        $listApiUrl = 'https://'.env('MAIL_SERVICE_API_URL').'/api/v1/custurl/get';

        //APIで既存のメールアドレス一覧を取得
        $response = Http::withHeaders([
            'Content-Type'  => 'application/json',
            'X-HM-apitoken' => $apiToken,
        ])->post($listApiUrl, []);
        
        
        if ($response->successful()) {
            //CSVデータとして保存
            $csv = $response->body();
            Storage::put('public/customer.csv', $csv);
        } else {
            Log::error('配信メールサービスからCSVの取得に失敗しました', [
                'status' => $response->status(),
                'error' => $response->body(),
                'stream_email' => $streamEmail
            ]);
        }

        //取得したCSVからメールアドレスのみを配列で抽出
        $csvPath = storage_path('app/public/customer.csv');
        $emails = [];
        
        if (($handle = fopen($csvPath, 'r')) !== false) {
            $header = fgetcsv($handle); //ヘッダー行の読み取り
        
            $emailIndex = array_search('メールアドレス', $header); //メールアドレス列のインデックス取得
        
            if ($emailIndex !== false) {
                while (($row = fgetcsv($handle)) !== false) {
                    $emails[] = $row[$emailIndex]; //メールアドレス列だけ配列に追加
                }
            }
        
            fclose($handle);
        }
        
        //申込時に入力されたメールアドレスが登録済みかどうかをチェック
        if (in_array($streamEmail, $emails, true)) {
            return true; //登録済み
        }

        return false; //未登録
    }

    /**
     * CSVファイル作成
     * 
     * @param array $requests
     * @return void
     */
    public function createCsv(array $requests)
    {
        try {
            //登録するデータ
            $importData = [
                ['メールアドレス', 'ステータス', '会社名', '名前', '部署名', '役職', '郵便番号', '住所', '電話番号', '登録担当者'], //ヘッダー行
                [$requests['stream_email'], 0, $requests['company_name'], 'ご担当者', '', '', '', '', '', '交流会']
            ];
            
            //保存先のパス
            $csvPath = storage_path('app/public/import.csv');
            
            $handle = fopen($csvPath, 'w');
            
            foreach ($importData as $row) {
                fputcsv($handle, $row);
            }
            
            fclose($handle);

        } catch (\Exception $e) {
            Log::error('CSVファイル作成中にエラーが発生しました', [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'stream_email' => $requests['stream_email']
            ]);
        }
    }

    /**
     * メールアドレス登録
     * 
     * @return void
     */
    public function registEmail()
    {
        $apiToken = env('MAIL_SERVICE_API_TOKEN');
        $apiLoginId = env('MAIL_SERVICE_API_LOGIN_ID');
        $importApiUrl = 'https://'.env('MAIL_SERVICE_API_URL').'/api/v1/custcsv/import';
        $csvPath = storage_path('app/public/import.csv');

        //顧客登録処理
        for ($i = 1; $i <= 2; $i++) {
            
            $importSettings = [
                'import_type' => "0", //登録
                'gid' => (string)$i, //グループ配信ID
                'report_option' => "2", //エラー時のみレポートメール送信
                'login_id' => $apiLoginId
            ];
            
            if ($i == 2) {
                $importSettings['is_overwrite'] = "1"; //上書きする
            }
            
            $jsonSettings = json_encode($importSettings);

            $response = Http::withHeaders([
                'X-HM-apitoken' => $apiToken,
            ])->asMultipart()->post($importApiUrl, [
                [
                    'name' => 'import_settings',
                    'contents' => $jsonSettings,
                ],
                [
                    'name' => 'customer_list',
                    'contents' => fopen($csvPath, 'r'),
                    'filename' => 'customer_list.csv',
                    'headers' => ['Content-Type' => 'text/csv'],
                ],
            ]);
            
            if (!$response->successful()) {
                Log::error('配信メールサービスへの登録に失敗しました', [
                    'status' => $response->status(),
                    'error' => $response->body()
                ]);
            }
        }
    }

    /**
     * 配信メールサービスへの反映
     * 
     * @param array $requests
     * @return boolean
     */
    public function reflect(array $requests)
    {
        //入力されたメールアドレスが存在しているかチェック
        $checkResult = $this->checkEmail($requests['stream_email']);

        //メールアドレスがすでに存在する場合は新規配信フラグFALSEを返す
        if ($checkResult) {
            return false;
        }
        
        //CSV作成
        $this->createCsv($requests);
        
        //顧客登録
        $this->registEmail();

        //新規配信フラグTRUEを返す
        return true;
    }
}
