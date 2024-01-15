<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Group extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'round',
        'competition_id',
    ];

    public function teams()
    {
        return $this->belongsToMany(Team::class, 'group_team')
            ->withPivot('standing', 'points');
    }
    public function competition()
    {
        return $this->belongsTo(Competition::class);
    }
}
