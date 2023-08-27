<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RestaurantIcon extends Model
{
    use HasFactory;

    protected $table = 'restaurant_home_icons';
    protected $fillable = [
        'restaurant_id' , 'image' , 'title_ar' , 'title_en' , 'link' , 'sort' , 'code' , 'is_active' , // enum [true , false]
    ];
    public function getImagePathAttribute(){
        return 'uploads/home_icons/' . $this->image;
    }
    public function getTitleAttribute(){
        return app()->getLocale() == 'ar' ? $this->title_ar : $this->title_en;
    }

    public function restaurant(){
        return $this->belongsTo(Restaurant::class , 'restaurant_id');
    }
}
