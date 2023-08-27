<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PartyAddition extends Model
{
    use HasFactory;
    protected $table = 'restaurant_party_additions';
    protected $fillable = [
        'name_ar' , 'name_en' , 'price'  , 'party_id' , 'is_required'
    ];
    public $timestamps = false;
    public function getNameAttribute(){
        return $this->attributes['name_' . app()->getLocale()];
    }
    public function party(){
        return $this->belongsTo(Restaurant::class , 'party_id');
    }
}
