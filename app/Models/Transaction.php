<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    protected $fillable = [
        'order_id', 'amount', 'tax_amount', 'voucher_discount', 'voucher_code', 'payment_method',
        'reference_number', 'paid_at', 'received_by',
        'cash_received', 'change_amount'
    ];

    protected $casts = [
        'amount'  => 'decimal:2',
        'paid_at' => 'datetime',
    ];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function receiver()
    {
        return $this->belongsTo(User::class, 'received_by');
    }
}