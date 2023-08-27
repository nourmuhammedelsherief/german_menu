<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PartyDay extends Model
{
    use HasFactory;
    protected $table = 'restaurant_party_days';
    protected $fillable = [
        'date', 'party_id'
    ];
    public $timestamps = false;

    public function party()
    {
        return $this->belongsTo(Party::class, 'party_id');
    }
    public function periods()
    {
        return $this->hasMany(PartyDayPeriod::class, 'party_day_id');
    }
}
