<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FormRegister extends Model
{
    use HasFactory;
    protected $table = 'form_registers';
    protected $fillable = [
        'name',
        'email',
        'country_id',
        'phone_number',
        'type'
        
    ];

    public function country(){
        return $this->belongsTo(Country::class , 'country_id');
    }

}
