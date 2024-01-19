<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


class Competition extends Model
{
    use HasFactory;
    const FINAL = 'Final Round';
    const GROUPS = 'Groups Round';
    protected $fillable = [
        'name',
    ];
    public function groups()
    {
        return $this->hasMany(Group::class);
    }
}
