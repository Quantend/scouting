<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    protected $table = 'tasks';

    protected $fillable = [
        'title',
        'money',
    ];

    public function hours()
    {
        return $this->hasMany(Hours::class);
    }
}
