<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PartyOrder extends Model
{
    use HasFactory;
    protected $table = 'restaurant_party_orders';
    protected $fillable = [
        'name_ar', 'name_en', 'user_id', 'restaurant_id', 'party_id', 'branch_id', 'price', 'total_price', 'status', 'date', 'time_from', 'time_to', 'payment_type' , 'online_payment_fees' , 'bank_id' , 'bank_photo' , 'payment_status' , 'tax' , 'num', 'cancel_reason'
    ];
    // public $timestamps = false;
    public function getNameAttribute()
    {
        return $this->attributes['name_' . app()->getLocale()];
    }
    public function getBankPhotoPathAttribute(){
        return  'uploads/transfers/' . $this->bank_photo; 
    }
    public function party()
    {
        return $this->belongsTo(Party::class, 'party_id');
    }
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
    public function restaurant()
    {
        return $this->belongsTo(Restaurant::class, 'restaurant_id');
    }
    public function branch()
    {
        return $this->belongsTo(PartyBranch::class, 'branch_id');
    }

    public function additions()
    {
        return $this->hasMany(PartyOrderAddition::class, 'order_id');
    }
    public function fields()
    {
        return $this->hasMany(PartyOrderField::class, 'order_id');
    }
    public function getToStringAttribute(){
        return Carbon::createFromTimestamp(strtotime($this->time_to))->translatedFormat('h:i A');
    }
    public function getFromStringAttribute(){
        return Carbon::createFromTimestamp(strtotime($this->time_from))->translatedFormat('h:i A');
    }
}
