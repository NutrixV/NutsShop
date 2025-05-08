<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CatalogProduct extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'catalog_product';

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
        'sku',
        'name',
        'description',
        'short_description',
        'price',
        'base_currency',
        'weight',
        'qty',
        'is_in_stock',
        'visibility',
        'status',
        'nut_type',
        'sweetness_level',
        'cocoa_pct',
        'salted',
        'roasted',
        'gluten_free',
        'organic',
        'origin_country',
        'weight_g',
        'expiry_date',
        'image',
        'image_alt',
        'gallery',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'price' => 'decimal:2',
        'weight' => 'decimal:3',
        'qty' => 'decimal:2',
        'is_in_stock' => 'boolean',
        'visibility' => 'integer',
        'status' => 'integer',
        'sweetness_level' => 'integer',
        'cocoa_pct' => 'decimal:2',
        'salted' => 'boolean',
        'roasted' => 'boolean',
        'gluten_free' => 'boolean',
        'organic' => 'boolean',
        'weight_g' => 'integer',
        'expiry_date' => 'date',
        'gallery' => 'array',
    ];

    /**
     * Get the categories for the product.
     */
    public function categories(): BelongsToMany
    {
        return $this->belongsToMany(
            CatalogCategory::class,
            'catalog_category_product',
            'product_id',
            'category_id'
        )->withPivot('position');
    }

    /**
     * Get the order items for the product.
     */
    public function orderItems(): HasMany
    {
        return $this->hasMany(SalesOrderItem::class, 'product_id', 'entity_id');
    }

    /**
     * Get the quote items for the product.
     */
    public function quoteItems(): HasMany
    {
        return $this->hasMany(QuoteItem::class, 'product_id', 'entity_id');
    }
}
