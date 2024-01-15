<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GroupTeam extends Model
{
    use HasFactory;
    protected $table = 'group_team';
    protected $fillable = [
        'group_id',
        'team_id',
        'standing',
        'points',
    ];
}
