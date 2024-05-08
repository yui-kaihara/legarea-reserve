<?php
declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EventGuest extends Model
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
    protected $table = 'event_guests';
    
    /*
     * 保存するカラム
     *
     * @array
     */
    protected $fillable = ['event_id', 'guest_id', 'company_id'];
    
    /*
     * 交流会の取得
     *
     * @return App\Models\Event
     */
    public function event()
    {
      return $this->belongsTo(Event::class);
    }

    /*
     * 参加するゲストの取得
     *
     * @return App\Models\Guest
     */
    public function guest()
    {
      return $this->belongsTo(Guest::class);
    }
}
