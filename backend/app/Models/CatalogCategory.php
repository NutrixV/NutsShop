<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CatalogCategory extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'catalog_category';

    /**
     * The primary key for the model.
     *
     * @var string
     */
    protected $primaryKey = 'category_id';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'parent_id',
        'name',
        'url_key',
        'is_active',
        'position',
        'image',
        'image_alt',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'is_active' => 'boolean',
        'position' => 'integer',
    ];

    /**
     * Get the parent category that owns the category.
     */
    public function parent(): BelongsTo
    {
        return $this->belongsTo(CatalogCategory::class, 'parent_id', 'category_id');
    }

    /**
     * Get the subcategories for the category.
     */
    public function children(): HasMany
    {
        return $this->hasMany(CatalogCategory::class, 'parent_id', 'category_id');
    }

    /**
     * Get the products for the category.
     */
    public function products(): BelongsToMany
    {
        return $this->belongsToMany(
            CatalogProduct::class,
            'catalog_category_product',
            'category_id',
            'product_id'
        )->withPivot('position');
    }
}
