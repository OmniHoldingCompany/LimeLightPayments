<?php

namespace OmniHolding\LimeLightPayments;

use Illuminate\Database\Eloquent\Model;

/**
 * Class Subscription
 *
 * @package App
 */
class Subscription extends Model
{
    protected $fillable = [
        'user_id',
        'll_customer_id',
        'll_order_id',
        'll_subscription_id',
    ];

    /**
     * @return mixed
     */
    public function users()
    {
        return $this->hasOne('User');
    }
}
