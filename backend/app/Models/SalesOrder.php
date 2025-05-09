<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * SalesOrder Model
 * 
 * Represents an order in the system.
 */
class SalesOrder extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'sales_order';

    /**
     * The primary key for the model.
     *
     * @var string
     */
    protected $primaryKey = 'entity_id';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'increment_id',
        'customer_id',
        'status',
        'subtotal',
        'grand_total',
        'currency',
        'shipping_address',
        'payment_method'
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'shipping_address' => 'array',
    ];

    /**
     * Get the order items for this order.
     */
    public function items(): HasMany
    {
        return $this->hasMany(SalesOrderItem::class, 'order_id', 'entity_id');
    }

    /**
     * Get the customer that owns the order.
     */
    public function customer(): BelongsTo
    {
        return $this->belongsTo(CustomerEntity::class, 'customer_id', 'entity_id');
    }

    /**
     * Generate a unique increment ID.
     *
     * @return string
     */
    public static function generateIncrementId(): string
    {
        $prefix = 'ORD';
        $date = now()->format('Ymd');
        $lastOrder = self::orderBy('entity_id', 'desc')->first();
        
        $sequence = 1;
        if ($lastOrder) {
            // Extract the sequence from the last order ID
            if (preg_match('/' . $prefix . $date . '(\d+)/', $lastOrder->increment_id, $matches)) {
                $sequence = (int)$matches[1] + 1;
            }
        }
        
        return $prefix . $date . str_pad($sequence, 4, '0', STR_PAD_LEFT);
    }
}
