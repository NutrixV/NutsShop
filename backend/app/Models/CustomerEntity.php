<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CustomerEntity extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'customer_entity';

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
        'email',
        'password_hash',
        'first_name',
        'last_name',
        'api_token',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password_hash',
        'api_token',
    ];

    /**
     * Get the addresses associated with the customer.
     */
    public function addresses(): HasMany
    {
        return $this->hasMany(CustomerAddress::class, 'customer_id', 'entity_id');
    }

    /**
     * Get the orders associated with the customer.
     */
    public function orders(): HasMany
    {
        return $this->hasMany(SalesOrder::class, 'customer_id', 'entity_id');
    }

    /**
     * Get the quotes (carts) associated with the customer.
     */
    public function quotes(): HasMany
    {
        return $this->hasMany(Quote::class, 'customer_id', 'entity_id');
    }
}
