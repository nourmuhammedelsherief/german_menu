<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PartyOrderFieldOption extends Model
{
    use HasFactory;
    protected $table = 'restaurant_party_order_options';
    protected $fillable = [
        'type' , // eunm [text , checkbox , selected]
        'name_ar' , 'name_en'  , 'field_id'
    ];
    public $timestamps = false;
    public function getNameAttribute(){
        return $this->attributes['name_' . app()->getLocale()];
    }
    public function field(){
        return $this->belongsTo(PartyOrderField::class , 'field_id');
    }
}
