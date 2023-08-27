<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class SmsHistoryPhone extends Model
{
    use HasFactory;
    protected $table = 'restaurant_sms_history_phones';
    protected $fillable = [
        'history_id' , 'phone' , 'is_sent' 
    ];
    public $timestamps = false;
    
    public function history(){
        return $this->belongsTo(SmsHistory::class , 'history_id');
    }
}


