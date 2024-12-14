<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HogoshaText extends Model
{
   use HasFactory;
    protected $table = 'hogosha_texts';
    protected $fillable =['people_id','last_name','first_name','user_identifier','notebook','is_read'];
    
    public function person()
    {
        return $this->belongsTo(Person::class, 'people_id');
    }

    // 未読メッセージを取得するスコープ
    public function scopeUnread($query, $userId)
    {
        return $query->where('is_read', false)
                     ->where('user_identifier', '!=', $userId);
    }

}