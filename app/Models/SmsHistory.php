<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class SmsHistory extends Model
{
    use HasFactory;
    protected $table = 'restaurant_sms_history';
    protected $fillable = [
        'restaurant_id' , 'message' , 'message_id' , 'message_count'
    ];
    
    
    public function restaurant(){
        return $this->belongsTo(Restaurant::class , 'restaurant_id');
    }

    public function phones(){
        return $this->hasMany(SmsHistoryPhone::class , 'history_id');
    }
}


