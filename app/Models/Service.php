<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Service extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'type', 'price', 'description', 'is_active'];

    protected $casts = [
        'price'     => 'decimal:2',
        'is_active' => 'boolean',
    ];

    public function getFormattedPriceAttribute(): string
    {
        return 'Rp ' . number_format($this->price, 0, ',', '.');
    }

    public function getPriceUnitLabelAttribute(): string
    {
        return $this->type === 'per_kg' ? '/kg' : '/item';
    }

    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }
}
