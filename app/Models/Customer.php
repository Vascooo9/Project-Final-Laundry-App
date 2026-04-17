<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Customer extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'phone', 'address', 'is_member', 'discount'];

    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    public function activeOrders()
    {
        return $this->hasMany(Order::class)
                    ->whereNotIn('status', ['picked_up']);
    }
}
