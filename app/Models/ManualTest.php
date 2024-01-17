<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ManualTest extends Model
{
    use HasFactory;
    protected $fillable = [
        'test_id',
        'question_id',
        'group_id',
        'question_start_at',
        'question_time',
        'answer_time',
    ];
}
