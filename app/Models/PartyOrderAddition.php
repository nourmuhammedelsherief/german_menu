<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PartyOrderAddition extends Model
{
    use HasFactory;
    protected $table = 'restaurant_party_order_additions';
    protected $fillable = [
        'name_ar' , 'name_en' , 'price'  , 'order_id'
    ];
    public $timestamps = false;
    public function getNameAttribute(){
        return $this->attributes['name_' . app()->getLocale()];
    }
    public function order(){
        return $this->belongsTo(PartyOrder::class , 'order_id');
    }
}
