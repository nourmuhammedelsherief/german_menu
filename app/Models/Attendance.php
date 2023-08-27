<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Attendance extends Model
{
    use HasFactory;
    protected $table = 'attendance';
    protected $fillable = [
        'start_date',
        'end_date',
        'admin_id' , 
        'details'
    ];

    public function admin()
    {
        return $this->belongsTo(Admin::class , 'admin_id');
    }
}
