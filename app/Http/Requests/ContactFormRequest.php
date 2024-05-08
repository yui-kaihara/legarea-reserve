<?php
declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ContactFormRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * バリデーションルール設定
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'name' => 'required',
            'name_kana' => 'required',
            'age' => 'required|integer',
            'email' => 'required|email',
            'email_stream' => 'nullable|email',
            'company_name' => 'required',
            'times' => 'required|integer'
        ];
    }
    
    /**
     * エラーメッセージ取得
     *
     * @return array
     */
    public function messages()
    {
        return [
            'required' => ':attributeは必須です',
            'integer' => ':attributeは数値で入力してください',
            'email' => ':attributeの形式で入力してください'
        ];
    }
    
    /**
     * バリデーションエラーのカスタム属性の取得
     *
     * @return array
     */
    public function attributes()
    {
        return [
	        'name' => '名前',
	        'name_kana' => 'ふりがな',
	        'age' => '年齢',
	        'email' => 'メールアドレス',
	        'email_stream' => 'メールアドレス',
	        'company_name' => '会社名',
	        'times' => '開催回'
        ];
    }
}
