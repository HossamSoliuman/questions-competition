<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


class Test extends Model
{
    use HasFactory;

    const PAST = 2;
    const CURRENT = 1;
    const COMMING = 0;
    protected $fillable = [
        'name',
        'start_time',
        'question_time',
        'answer_time',
        'status',
        'group_id'
    ];
    public function group()
    {
        return $this->belongsTo(Group::class);
    }
    public function questions()
    {
        return $this->belongsToMany(Question::class)
            ->withPivot(['id','team_id','answered']);;
    }

	public function audiences(){
		return $this->hasMany(Audience::class);
	}
}