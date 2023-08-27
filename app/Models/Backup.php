<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Backup extends Model
{
    use HasFactory;

    protected $table = 'backups' ;
    protected $fillable = [
        'restaurant_id' , 'branch_id' , 'type' , 'create_status' , 'restore_status' , 'restored_at'
    ];

    public function restaurant(){
        return $this->belongsTo(Restaurant::class, 'restaurant_id');
    }
    
    public function branch(){
        return $this->belongsTo(Branch::class, 'branch_id');
    }
}
