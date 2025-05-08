<?php
/**
 * CatalogProductAttributeOption Model
 * 
 * Represents options for selectable product attributes.
 * These options are available choices for attributes of type select/multiselect.
 * 
 * @package App\Models
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * CatalogProductAttributeOption Model
 * 
 * @property int $option_id Primary key
 * @property int $attribute_id Foreign key to the attribute
 * @property string $value Option display value
 * @property int $position Display position
 */
class CatalogProductAttributeOption extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'catalog_product_attribute_option';

    /**
     * The primary key for the model.
     *
     * @var string
     */
    protected $primaryKey = 'option_id';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'attribute_id',
        'value',
        'position',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'position' => 'integer',
    ];

    /**
     * Get the attribute that owns the option.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function attribute(): BelongsTo
    {
        return $this->belongsTo(CatalogProductAttribute::class, 'attribute_id', 'attribute_id');
    }
} 