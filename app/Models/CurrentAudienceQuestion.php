<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CurrentAudienceQuestion extends Model
{
    use HasFactory;
    protected $fillable = [
        'question_id',
        'test_id',
        'show_question',
        'show_answer',
        'random_number',
    ];
    public function question()
    {
        return $this->belongsTo(Question::class);
    }
}
