<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PartyDayPeriod extends Model
{
    use HasFactory;
    protected $table = 'restaurant_party_day_periods';
    protected $fillable = [
        'time_from'  , 'party_day_id' , 'time_to'
    ];
    public $appends = [
        'to_string' , 'from_string' 
    ];
    public $timestamps = false;
    public function day(){
        return $this->belongsTo(PartyDay::class , 'party_day_id');
    }
    public function getToStringAttribute(){
        return Carbon::createFromTimestamp(strtotime($this->time_to))->translatedFormat('h:i A');
    }
    public function getFromStringAttribute(){
        return Carbon::createFromTimestamp(strtotime($this->time_from))->translatedFormat('h:i A');
    }

}
