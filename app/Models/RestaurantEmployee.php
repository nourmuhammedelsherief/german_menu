<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class RestaurantEmployee extends Authenticatable
{
    use HasFactory;
    use Notifiable;
    protected $guard = 'employee';
    protected $table = 'restaurant_employees';
    protected $fillable = [
        'restaurant_id',
        'branch_id',
        'name',
        'email',
        'phone_number',
        'password',
        'email_verified_at',
        'is_active',    // [ 'true' , 'false']
        'verification_code',
        'order',   // [main. branch]
        'type' , 
    ];
    protected $hidden = [
        'password',
    ];
    protected $casts = [
        'type' => 'array' , 
    ];
    public static function booted()
    {
        parent::boot();
        static::addGlobalScope('type', function ($query) {
            $query->where('type', 'like', '%normal%');
        });
    }
    public function restaurant()
    {
        return $this->belongsTo(Restaurant::class , 'restaurant_id');
    }
    public function branch()
    {
        return $this->belongsTo(Branch::class , 'branch_id');
    }
}
