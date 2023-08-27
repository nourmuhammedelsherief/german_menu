<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WaiterItem extends Model
{
    use HasFactory;
    protected $table = 'restaurant_waiter_items';
    protected $fillable = [
        'restaurant_id' , 'branch_id' , 'name_ar' , 'name_en' , 'sort' , 
        'status' , // enum [true , false]
    ];

    public function getNameAttribute(){
        return app()->getLocale() == 'ar' ? $this->name_ar : $this->name_en;
    }
    public function restaurant(){
        return $this->belongsTo(Restaurant::class , 'restaurant_id');
    }
    public function branch(){
        return $this->belongsTo(Branch::class , 'branch_id');
    }
}
