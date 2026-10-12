<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

/**
 * BelongsToShop Trait
 *
 * Automatically scopes queries to the current user's shop.
 * Super admins bypass the scope and see all records.
 * On create, auto-assigns shop_id from the authenticated user.
 */
trait BelongsToShop
{
    public static function bootBelongsToShop(): void
    {
        // ── Global scope ───────────────────────────────────────────────
        static::addGlobalScope('shop', function (Builder $builder) {
            // Never filter the User model itself (would cause a circular
            // dependency: Auth::user() → User query → global scope → Auth::user()…)
            if (get_class($builder->getModel()) === \App\Models\User::class) {
                return;
            }

            try {
                // Skip in console / artisan context
                if (app()->runningInConsole()) {
                    return;
                }

                $user = Auth::user();

                if (!$user) {
                    return; // Unauthenticated – seeders, public routes, etc.
                }

                if ($user->role === 'super_admin') {
                    return; // Super admin sees everything
                }

                if (!empty($user->shop_id)) {
                    $builder->where(
                        $builder->getModel()->getTable() . '.shop_id',
                        $user->shop_id
                    );
                }
            } catch (\Exception $e) {
                // Silently ignore – never break the request over a missing scope
            }
        });

        // ── Auto-assign shop_id on create ──────────────────────────────
        static::creating(function ($model) {
            try {
                if (app()->runningInConsole()) {
                    return;
                }

                $user = Auth::user();

                if (!$user || $user->role === 'super_admin') {
                    return;
                }

                if (empty($model->shop_id) && !empty($user->shop_id)) {
                    $model->shop_id = $user->shop_id;
                }
            } catch (\Exception $e) {
                // Silently ignore
            }
        });
    }

    /**
     * Eager-load the shop relationship.
     */
    public function shop()
    {
        return $this->belongsTo(\App\Models\Shop::class);
    }

    /**
     * Scope a query to a specific shop, bypassing the global scope.
     */
    public function scopeForShop(Builder $query, int $shopId): Builder
    {
        return $query->withoutGlobalScope('shop')->where('shop_id', $shopId);
    }
}
