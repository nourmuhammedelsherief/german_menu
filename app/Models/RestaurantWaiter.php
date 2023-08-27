<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class RestaurantWaiter extends RestaurantEmployee
{

    protected $guard = 'waiter';
   
    public static function booted()
    {
        parent::boot();
        static::addGlobalScope('type', function ($query) {
            $query->where('type', 'like', '%waiter%');
        });
    }
   
}
