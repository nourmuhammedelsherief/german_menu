<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PartyOrderField extends Model
{
    use HasFactory;
    protected $table = 'restaurant_party_order_fields';
    protected $fillable = [
        'type' , // eunm [text , checkbox , selected]
        'name_ar' , 'name_en' , 'is_required'  , 'order_id'
    ];
    public $timestamps = false;
    public function getNameAttribute(){
        return $this->attributes['name_' . app()->getLocale()];
    }
    public function order(){
        return $this->belongsTo(PartyOrder::class , 'order_id');
    }
    public function options(){
        return $this->hasMany(PartyOrderFieldOption::class , 'field_id');
    }
}
