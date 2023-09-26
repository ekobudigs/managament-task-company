<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Department extends Model
{
    use HasFactory;

    protected $fillable = ['manager_id', 'description', 'name'];
 
    public function manager()
{
    return $this->belongsTo(User::class, 'manager_id');
}
}
