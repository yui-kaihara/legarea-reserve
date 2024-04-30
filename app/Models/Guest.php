<?php
declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Guest extends Model
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
    protected $table = 'guests';
    
    /*
     * 保存するカラム
     *
     * @array
     */
    protected $fillable = ['company_name', 'name', 'name_kana', 'age', 'email', 'stream_email', 'event_id', 'company_id'];
    
    /*
     * ゲストが所属する会社を取得
     *
     * @return App\Models\Company
     */
    public function company()
    {
      return $this->belongsTo(Company::class);
    }
    
    /*
     * ゲストが参加する交流会を取得
     *
     * @return App\Models\Event
     */
    public function event()
    {
      return $this->belongsTo(Event::class, 'event_id', 'times');
    }
}