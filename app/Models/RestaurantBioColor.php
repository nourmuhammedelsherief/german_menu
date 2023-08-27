<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RestaurantBioColor extends Model
{
    use HasFactory;
    protected $table = 'restaurant_bio_colors';
    protected $fillable = [
        'restaurant_id',
        'main_line',
        'background',
        'main_cats',
        'sub_cats',
        'sub_background',
        'sub_cats_line',
        'background_image',
    ];

    public function restaurant()
    {
        return $this->belongsTo(Restaurant::class , 'restaurant_id');
    }
}
