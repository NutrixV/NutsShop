<?php
/**
 * CatalogProductAttribute Model
 * 
 * Represents attributes that can be assigned to catalog products.
 * These attributes are used for filtering, searching, and product display.
 * 
 * @package App\Models
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * CatalogProductAttribute Model
 * 
 * @property int $attribute_id Primary key
 * @property string $attribute_code Unique code for the attribute
 * @property string $frontend_label Label for frontend display
 * @property string $frontend_input Type of input (select, text, etc.)
 * @property string|null $attribute_group Group that the attribute belongs to
 * @property int $position Display position
 * @property bool $is_required Whether attribute is required
 * @property bool $is_filterable Whether attribute can be used in filters
 * @property bool $is_searchable Whether attribute can be used in search
 */
class CatalogProductAttribute extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'catalog_product_attribute';

    /**
     * The primary key for the model.
     *
     * @var string
     */
    protected $primaryKey = 'attribute_id';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'attribute_code',
        'frontend_label',
        'frontend_input',
        'attribute_group',
        'position',
        'is_required',
        'is_filterable',
        'is_searchable',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'is_required' => 'boolean',
        'is_filterable' => 'boolean',
        'is_searchable' => 'boolean',
        'position' => 'integer',
    ];

    /**
     * Get the options for the attribute.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function options(): HasMany
    {
        return $this->hasMany(CatalogProductAttributeOption::class, 'attribute_id', 'attribute_id');
    }
} 