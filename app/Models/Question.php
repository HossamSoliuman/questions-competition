<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Question extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'a',
        'b',
        'c',
        'd',
        'correct_answer',
        'category_id',
        'repeated',
    ];



    public function category()
    {
        return $this->belongsTo(Category::class);
    }
    public function tests()
    {
        return $this->belongsToMany(Test::class);
    }
    public function Questiontests()
    {
        return $this->belongsToMany(Test::class, 'question_test', 'question_id', 'test_id');
    }
}
