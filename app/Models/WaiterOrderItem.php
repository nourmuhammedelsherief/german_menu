<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WaiterOrderItem extends Model
{
    use HasFactory;
    protected $table = 'restaurant_waiter_order_items';
    protected $fillable = [
        'order_id' , 'item_id' , 'name' 
    ];
    public $timestamps = false;

    public function order(){
        return $this->belongsTo(WaiterOrder::class , 'order_id');
    }
    public function item(){
        return $this->belongsTo(WaiterItem::class , 'item_id');
    }
    
}
