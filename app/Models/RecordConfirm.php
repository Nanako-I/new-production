<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RecordConfirm extends Model
{
    use HasFactory;
    protected $table = 'records_confirm';

    protected $fillable = ['person_id', 'kiroku_date', 'is_confirmed'];
}