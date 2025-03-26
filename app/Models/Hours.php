<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Hours extends Model
{
    protected $table = 'hours';

    protected $fillable = [
        'member_id',
        'task_id',
        'hours',
        'date',
    ];

    // Define the relationship to the Member model
    public function member()
    {
        return $this->belongsTo(Member::class);
    }

    // Define the relationship to the Task model
    public function task()
    {
        return $this->belongsTo(Task::class);
    }
}
