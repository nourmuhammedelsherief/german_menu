<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Party extends Model
{
    use HasFactory;
    protected $table = 'restaurant_party';
    protected $fillable = [
        'title_ar', 'title_en', 'description_ar', 'description_en', 'price', 'restaurant_id', 'branch_id' , 'image'
    ];

    public function getImagePathAttribute(){
        return empty($this->image) ? null : 'uploads/parties/' . $this->image;
    }
    public function getTitleAttribute()
    {
        return $this->attributes['title_' . app()->getLocale()];
    }
    public function getDescriptionAttribute()
    {
        return $this->attributes['description_' . app()->getLocale()];
    }
    public function restaurant()
    {
        return $this->belongsTo(Restaurant::class, 'restaurant_id');
    }
    public function branch()
    {
        return $this->belongsTo(PartyBranch::class, 'branch_id');
    }

    public function days()
    {
        return $this->hasMany(PartyDay::class, 'party_id');
    }
    public function fields()
    {
        return $this->hasMany(PartyField::class, 'party_id');
    }
    public function additions()
    {
        return $this->hasMany(PartyAddition::class, 'party_id');
    }
}
