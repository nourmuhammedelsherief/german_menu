<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PartyFieldOption extends Model
{
    use HasFactory;
    protected $table = 'restaurant_party_field_options';
    protected $fillable = [
        'name_ar' , 'name_en' , 'is_default'  , 'field_id'
    ];
    public $timestamps = false;
    public function getNameAttribute(){
        return $this->attributes['name_' . app()->getLocale()];
    }
    public function field(){
        return $this->belongsTo(PartyField::class , 'field_id');
    }
}
