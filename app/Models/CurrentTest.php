<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CurrentTest extends Model
{
    use HasFactory;
    protected $fillable = [
        'group_id',
        'test_id',
        'question_id',
        'question_start_at',
        'question_time',
        'answer_time',
    ];
}
