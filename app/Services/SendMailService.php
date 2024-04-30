<?php
declare(strict_types=1);

namespace App\Services;

use App\Mail\SendMail;
use App\Mail\SendMailFailure;
use App\Models\Guest;
use Illuminate\Support\Facades\Mail;

class SendMailService
{
    /**
     * メール送信
     * 
     * @param Guest $guest
     * @return void
     */
    public function send(Guest $guest)
    {
        //DBへの登録結果で分岐
        if ($guest->result) {
            
            //申込完了メール
            Mail::send(new SendMail($guest));
        } else {
            
            //申込不可メール
            Mail::send(new SendMailFailure($guest));
        }
    }
}
