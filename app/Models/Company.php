<?php
declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Company extends Model
{
    use HasFactory;
    
    /**
     * 作成日時カラム
     *
     * @var string
     */
    const CREATED_AT = 'created_at';
    
    /**
     * 更新日時カラム
     *
     * @var string
     */
    const UPDATED_AT = 'updated_at';
    
    /**
     * モデルに関連付けるテーブル
     *
     * @var string
     */
    protected $table = 'companies';
    
    /*
     * 保存するカラム
     *
     * @array
     */
    protected $fillable = ['company_name', 'domain', 'count', 'event_id'];

    /*
     * 会社に所属するゲストの取得
     *
     * @return App\Models\Guest
     */
    public function guest()
    {
      return $this->hasMany(Guest::class);
    }
}
