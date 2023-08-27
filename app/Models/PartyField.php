<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PartyField extends Model
{
    use HasFactory;
    protected $table = 'restaurant_party_fields';
    protected $fillable = [
        'type' , // eunm [text , checkbox , selected]
        'name_ar' , 'name_en' , 'is_required'  , 'party_id'
    ];
    public $timestamps = false;
    public function getNameAttribute(){
        return $this->attributes['name_' . app()->getLocale()];
    }
    public function party(){
        return $this->belongsTo(Party::class , 'party_id');
    }
    public function options(){
        return $this->hasMany(PartyFieldOption::class , 'field_id');
    }
}
