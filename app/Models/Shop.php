<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Shop Model
 *
 * Represents a tenant shop in the multi-tenant system.
 *
 * @property int    $id
 * @property string $name
 * @property string|null $email
 * @property string $status   pending|approved|rejected|suspended
 * @property string|null $city
 * @property string|null $phone
 * @property string|null $address
 * @property int|null $owner_id
 */
class Shop extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'code',
        'email',
        'city',
        'phone',
        'address',
        'owner_name',
        'status',
        'owner_id',
    ];

    // Status constants
    public const STATUS_PENDING   = 'pending';
    public const STATUS_APPROVED  = 'approved';
    public const STATUS_REJECTED  = 'rejected';
    public const STATUS_SUSPENDED = 'suspended';

    /**
     * The user who owns/registered this shop.
     */
    public function owner()
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    /**
     * All users belonging to this shop.
     */
    public function users()
    {
        return $this->hasMany(User::class);
    }

    public function isPending(): bool   { return $this->status === self::STATUS_PENDING; }
    public function isApproved(): bool  { return $this->status === self::STATUS_APPROVED; }
    public function isRejected(): bool  { return $this->status === self::STATUS_REJECTED; }
    public function isSuspended(): bool { return $this->status === self::STATUS_SUSPENDED; }
}
