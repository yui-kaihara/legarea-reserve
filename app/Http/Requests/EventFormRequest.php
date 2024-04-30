<?php
declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class EventFormRequest extends FormRequest
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
            'times' => 'required|integer|unique:events,times,'.$this->times.',times',
            'date' => 'required',
            'start_time' => 'required',
            'end_time' => 'required',
            'place' => 'required',
            'amount' => 'required|integer',
            'capacity' => 'required|integer'
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
            'unique' => 'この:attributeは登録済みです'
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
            'times' => '開催回',
            'date' => '開催日',
            'start_time' => '開始時間',
            'end_time' => '終了時間',
            'place' => '場所',
            'amount' => '金額',
            'capacity' => '定員'
        ];
    }
}
