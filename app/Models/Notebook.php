<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Notebook extends Model
{
   use HasFactory;
    protected $table = 'notebooks';
    protected $fillable = ['people_id','notebook'];
    
    public function person()
    {
        return $this->belongsTo(Person::class, 'people_id');
    }
    
    // その日の連絡帳が登録されているかチェックするメソッド
    public static function hasEntryForToday($peopleId)
    {
        return self::where('people_id', $peopleId)
            ->whereDate('created_at', now()->toDateString())
            ->exists();
    }
}