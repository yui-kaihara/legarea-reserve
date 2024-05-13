<?php
declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ImportFileFormRequest extends FormRequest
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
            'uploadFile' => 'required|mimes:xlsx'
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
            'required' => ':attributeを選択してください',
            'mimes' => ':attributeの拡張子は.xlsxにしてください'
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
            'uploadFile' => 'ファイル'
        ];
    }
}
