<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Carbon\Carbon;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_number', 'customer_id', 'user_id', 'delivery_type',
        'delivery_address', 'delivery_phone', 'estimated_done',
        'status', 'payment_status', 'payment_method',
        'total_amount', 'notes', 'picked_up_at',
    ];

    protected $casts = [
        'estimated_done' => 'date',
        'picked_up_at'   => 'datetime',
        'total_amount'   => 'decimal:2',
    ];

    // Auto-generate order number
    protected static function boot()
    {
        parent::boot();
        static::creating(function ($order) {
            $order->order_number = static::generateOrderNumber();
        });
    }

    public static function generateOrderNumber(): string
    {
        $prefix = 'LDR-' . now()->format('Ymd') . '-';
        $last = static::where('order_number', 'like', $prefix . '%')
                      ->orderByDesc('id')->first();
        $seq = $last ? intval(substr($last->order_number, -3)) + 1 : 1;
        return $prefix . str_pad($seq, 3, '0', STR_PAD_LEFT);
    }

    // Relationships
    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function transaction()
    {
        return $this->hasOne(Transaction::class);
    }

    // Helpers
    public function getStatusLabelAttribute(): string
    {
        return match($this->status) {
            'pending'    => 'Menunggu',
            'processing' => 'Sedang Dicuci',
            'done'       => 'Selesai',
            'picked_up'  => 'Sudah Diambil',
            default      => $this->status,
        };
    }

    public function getStatusColorAttribute(): string
    {
        return match($this->status) {
            'pending'    => 'yellow',
            'processing' => 'blue',
            'done'       => 'green',
            'picked_up'  => 'gray',
            default      => 'gray',
        };
    }

    public function getDeliveryTypeLabelAttribute(): string
    {
        return $this->delivery_type === 'delivery' ? 'Diantar' : 'Ambil Sendiri';
    }

    public function isOverdue(): bool
    {
        return $this->estimated_done->isPast()
            && !in_array($this->status, ['done', 'picked_up']);
    }

    public function recalculateTotal(): void
    {
        $this->total_amount = $this->items->sum('subtotal');
        $this->save();
    }
}