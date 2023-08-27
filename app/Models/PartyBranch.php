<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PartyBranch extends Model
{
    use HasFactory;
    protected $table = 'restaurant_party_branches';
    protected $fillable = [
        'name_ar' , 'name_en' , 'restaurant_id'
    ];
    protected $appends = ['name'];
    public function getNameAttribute(){
        return $this->attributes['name_' . app()->getLocale()];
    }

    public function restaurant(){
        return $this->belongsTo(Restaurant::class , 'restaurant_id');
    }
    public function parties(){
        return $this->hasMany(Party::class , 'branch_id');
    }
}
