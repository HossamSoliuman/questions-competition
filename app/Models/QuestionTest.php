<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class QuestionTest extends Model
{
    use HasFactory;
    public $table = 'question_test';
    protected $fillable = [
        'test_id',
        'question_id',
        'team_id',
        'answered',
        'set',
    ];
    public function test()
    {
        return $this->belongsTo(Test::class);
    }
    public function question()
    {
        return $this->belongsTo(Question::class);
    }
}
