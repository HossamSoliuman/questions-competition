<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


class Audience extends Model
{
    use HasFactory;

    protected $fillable = [
        'number',
        'name',
        'email',
        'phone',
        'test_id',
        'points',
    ];


    public function test()
    {
        return $this->belongsTo(Test::class, 'test_id');
    }
}
