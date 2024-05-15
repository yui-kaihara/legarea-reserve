<?php
declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Event extends Model
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
    protected $table = 'events';
    
    /**
     * 型をキャストするカラム
     *
     * @var array
     */
    protected $casts = [
        'date' => 'datetime',
        'start_time' => 'datetime',
        'end_time' => 'datetime'
    ];
    
    /*
     * 保存するカラム
     *
     * @array
     */
    protected $fillable = ['times', 'date', 'start_time', 'end_time', 'place', 'amount', 'capacity', 'is_public'];

    /*
     * 交流会に参加するユーザを取得
     *
     * @return App\Models\Guest
     */
    public function guest()
    {
      return $this->hasMany(Guest::class, 'event_id', 'times');
    }
}
