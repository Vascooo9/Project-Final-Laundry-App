<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderItem extends Model
{
    protected $fillable = [
        'order_id', 'service_id', 'quantity',
        'price_per_unit', 'subtotal', 'item_note',
    ];

    protected $casts = [
        'quantity'       => 'decimal:2',
        'price_per_unit' => 'decimal:2',
        'subtotal'       => 'decimal:2',
    ];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function service()
    {
        return $this->belongsTo(Service::class);
    }
}
