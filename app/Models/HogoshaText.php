<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HogoshaText extends Model
{
   use HasFactory;
    protected $table = 'hogosha_texts';
    protected $fillable = ['people_id','notebook'];
    
    public function person()
    {
        return $this->belongsTo(Person::class, 'people_id');
    }

}