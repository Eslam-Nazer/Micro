<?php

namespace App\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class TodoList extends Model
{
    protected $table = 'todo';

    protected $guarded = [];

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($todo) {
            $todo->user_id = Auth::id();
        });
    }

    public function users()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }
}
