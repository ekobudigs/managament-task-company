<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Task extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function taskAssignments()
    {
        return $this->hasMany(TaskAssignments::class);
    }
    public function asigne()
    {
        return $this->belongsToMany(User::class);
    }
}
