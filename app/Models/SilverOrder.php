<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SilverOrder extends Model
{
    use HasFactory;
    protected $table = 'silver_orders';
    protected $fillable = [
        'user_id',
        'status',
        'product_id',
        'product_size_id',
        'notes',
        'product_count',
        'order_price',
        'product_price',
        'tax',
        'total_price',
        'payment_type',
        'order_type',
        'previous_order_type',
        'invoice_id',
        'foodics_branch_id',
        'period_id',
        'day_id',
        'discount_id',
        'discount_value',
        'order_id',
        'foodics_branch_name_ar', 'foodics_branch_name_ar', 'product_name_ar', 'product_name_en', 'size_name_ar', 'size_name_en'
    ];
    protected $appends = [
        'product_name' , 'size_name' , 'foodics_branch_name'
    ];
    public function getFoodicsBranchNameAttribute(){
        return app()->getLocale() == 'ar' ? $this->foodics_branch_name_ar : $this->foodics_branch_name_en;
    }
    public function getProductNameAttribute(){
        return app()->getLocale() == 'ar' ? $this->product_name_ar : $this->product_name_en;
    }
    public function getSizeNameAttribute(){
        return app()->getLocale() == 'ar' ? $this->size_name_ar : $this->size_name_en;
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id');
    }
    public function foodicsOrder()
    {
        return $this->belongsTo(SilverOrderFoodics::class, 'order_id');
    }
    public function product_size()
    {
        return $this->belongsTo(ProductSize::class, 'product_size_id');
    }
    public function silver_order_options()
    {
        return $this->hasMany(SilverOrderOption::class, 'silver_order_id');
    }
    public function foodics_branch()
    {
        return $this->belongsTo(RestaurantFoodicsBranch::class, 'foodics_branch_id');
    }
    public function discount()
    {
        return $this->belongsTo(FoodicsDiscount::class, 'discount_id');
    }
}
