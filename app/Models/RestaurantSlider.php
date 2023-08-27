<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RestaurantSlider extends Model
{
    use HasFactory;
    protected $table = 'restaurant_sliders';
    protected $fillable = [
        'restaurant_id',
        'photo',
        'type' , 'youtube' , 
        'description_en' , 'description_ar' ,
        'slider_type' , // enum => home' , 'contact_us_client' , 'contact_us'
    ];

    public function getDescriptionAttribute(){
        return app()->getLocale() == 'ar' ? $this->description_ar : $this->description_en;
    }
    public function restaurant()
    {
        return $this->belongsTo(Restaurant::class , 'restaurant_id');
    }
}
