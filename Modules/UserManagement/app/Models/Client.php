<?php

namespace Modules\UserManagement\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\UserManagement\Database\factories\ClientFactory;

class Client extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that should be cast to native types.
     * @var array
     */
    protected $cast = [
        'active_properties' => 'integer',
    ];

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [];

    /**
     * Create a new factory instance for the model.
     *
     * @return \Illuminate\Database\Eloquent\Factories\Factory
     */
    protected static function newFactory(): ClientFactory
    {
        return ClientFactory::new();
    }

    /**
     * Get the account that owns the client.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function account(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
