<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TaskUser extends Model
{
    use HasFactory;
    protected $guarded = ['id'];

    // Hubungkan dengan model Task
    public function task()
    {
        return $this->belongsTo(Task::class);
    }

    // Hubungkan dengan model User
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
