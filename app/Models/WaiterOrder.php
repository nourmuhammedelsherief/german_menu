<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WaiterOrder extends Model
{
    use HasFactory;
    protected $table = 'restaurant_waiter_orders';
    protected $fillable = [
        'restaurant_id' , 'branch_id' , 'table_id' ,'user_id', 'note'  , 'phone' , 
        'status' , // enum [pending , in_progress , 'completed' , 'canceled']
    ];

    public function restaurant(){
        return $this->belongsTo(Restaurant::class , 'restaurant_id');
    }
    public function branch(){
        return $this->belongsTo(Branch::class , 'branch_id');
    }
    public function table(){
        return $this->belongsTo(Table::class , 'table_id');
    }
    public function items(){
        return $this->hasMany(WaiterOrderItem::class , 'order_id');
    }
}
