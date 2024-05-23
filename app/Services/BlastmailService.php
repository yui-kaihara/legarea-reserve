<?php
declare(strict_types=1);

namespace App\Services;

class BlastmailService
{
    /**
     * ログイン
     * 
     * @return stirng
     */
    public function login()
    {
        //ログインURL
        $url = 'https://api.bme.jp/rest/1.0/authenticate/login';
        
        //ログインデータ
        $loginDatas = [
            'username' => env('BLASTMAIL_USERNAME'),
            'password' => env('BLASTMAIL_PASSWORD'),
            'api_key' => env('BLASTMAIL_APIKEY'),
            'format' => 'json' //レスポンスをjson形式に指定
        ];
        
        //API経由でログイン処理実行
        $response = $this->executeCurl($url, $loginDatas);
        
        //レスポンスをjsonから配列に変換
        $token = json_decode($response, true);

        //アクセストークンを返却
        if (isset($token['accessToken'])) {
            return $token['accessToken'];
        }
        
        return false;
    }
    
    /**
     * ログアウト
     * 
     * @param string $accessToken
     * @return void
     */
    public function logout(string $accessToken)
    {
        //ログアウトURL
        $url = 'https://api.bme.jp/rest/1.0/authenticate/logout?';
        
        //エンコードされたクエリ文字列を生成
        $query = ['access_token' => $accessToken];
        $query = http_build_query($query);
        
        //ログアウト実行
        file_get_contents($url.$query);
    }
    
    /**
     * 反映
     * 
     * @param array $requests
     * @param bool $updateFlag
     * @return void
     */
    public function reflect(array $requests, bool $updateFlag = TRUE)
    {
        //ログイン処理
        $accessToken = $this->login();
        
        if ($accessToken) {
            
            //ユーザ検索
            $users = $this->search($requests, $accessToken);
    
            //ユーザが存在するか
            if ($users) {
                
                if ($updateFlag) {
                    
                    //更新
                    $this->update($requests, $accessToken, $users);   
                }
    
            } else {
    
                //登録
                $this->store($requests, $accessToken);
            }
            
            //ログアウト処理
            $this->logout($accessToken);
        }
    }    
    
    /**
     * 検索
     * 
     * @param array $requests
     * @param string $accessToken
     * @return array
     */
    public function search(array $requests, string $accessToken)
    {
        //検索URL
        $url = 'https://api.bme.jp/rest/1.0/contact/list/search?';
        
        //クエリを初期化
        $query = [
            'access_token' => $accessToken,
            'f' => 'json'
        ];
        
        //エンコードされたクエリ文字列を生成（同じメールアドレスを検索）
        $query['keywords'] = $requests['stream_email'];
        $encodeQuery = http_build_query($query);
        
        //検索を実行
        $response = file_get_contents($url.$encodeQuery);
        
        //レスポンスをjsonから配列に変換
        $users = json_decode($response, true)['contacts'];
        
        //ユーザが存在する場合（=会社名の変更のみ）
        if ($users) {

            //メールアドレスからドメイン部分を取得
            $domain = substr(strrchr($requests['email'], '@'), 1);
    
            //エンコードされたクエリ文字列を生成（同じドメインを検索）
            $query['keywords'] = $domain;
            $encodeQuery = http_build_query($query);
    
            //検索を実行
            $response = file_get_contents($url.$encodeQuery);
            
            //レスポンスをjsonから配列に変換
            $users = json_decode($response, true)['contacts'];
        }

        //ユーザ情報を返却
        return $users;
    }

    /**
     * 保存
     * 
     * @param array $requests
     * @param string $accessToken
     * @return void
     */
    public function store(array $requests, string $accessToken)
    {
        //個別登録URL
        $url = 'https://api.bme.jp/rest/1.0/contact/detail/create';
        
        //登録データ
        $registDatas = [
            'access_token' => $accessToken,
            'c0' => $requests['company_name'].'　ご担当者',
            'c15' => $requests['stream_email']
        ];
        
        //API経由で登録処理実行
        $this->executeCurl($url, $registDatas);
    }
    
    /**
     * 更新
     * 
     * @param array $requests
     * @param string $accessToken
     * @param array $users
     * @return void
     */
    public function update(array $requests, string $accessToken, array $users)
    {
        //更新URL
        $url = 'https://api.bme.jp/rest/1.0/contact/detail/update';

        foreach ($users as $user) {
            
            //更新データ
            $updateDatas = [
                'access_token' => $accessToken,
                'contactID' => $user['contactID'],
                'c2' => $requests['company_name'] //更新する項目は会社名のみ
            ];
            
            //ユーザごとにAPI経由で更新処理実行
            $this->executeCurl($url, $updateDatas);
        }
    }
    
    /**
     * curl処理実行
     * 
     * @param string $url
     * @param array $postDatas
     * @return void
     */
    public function executeCurl(string $url, array $postDatas)
    {
        //curlセッションを初期化する
        $curlHandle = curl_init();
        
        //取得するURLを指定
        curl_setopt($curlHandle, CURLOPT_URL, $url);
        
        //POST送信
        curl_setopt($curlHandle, CURLOPT_CUSTOMREQUEST, 'POST');
        
        //実行結果を文字列で返す
        curl_setopt($curlHandle, CURLOPT_RETURNTRANSFER, true);
    
        //POSTする値
        curl_setopt($curlHandle, CURLOPT_POSTFIELDS, http_build_query($postDatas));
        
        //APIを叩く
        $response = curl_exec($curlHandle);
        
        return $response;
    }
}
