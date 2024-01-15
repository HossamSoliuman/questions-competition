<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


class Team extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'email',
        'password',
    ];
    public function groups()
    {
        return $this->belongsToMany(Group::class, 'group_team')
            ->withPivot('standing', 'points');
    }
}
