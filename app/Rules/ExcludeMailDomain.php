<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class ExcludeMailDomain implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param  \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        //メールアドレスからドメイン部分を取得
        $domain = substr(strrchr($value, '@'), 1);
        
        //フリーメールアドレスのドメインを配列に設定
        $excludeDomains = ['gmail.com', 'yahoo.co.jp', 'outlook.com', 'outlook.jp', 'hotmail.com', 'icloud.com'];

        //フリーメールアドレスならはじく
        if (in_array($domain, $excludeDomains)) {

            $fail('フリーメールアドレスは利用できません');
        }
    }
}
