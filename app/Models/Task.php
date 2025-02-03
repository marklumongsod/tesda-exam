<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    protected $fillable = ['title', 'description', 'user_assigned', 'status'];

    protected $casts = [
        'user_assigned' => 'array',
    ];

    public function users()
    {
        return $this->belongsToMany(User::class, 'user_assigned', 'id', 'id');
    }
}
