<?php

namespace App\Repositories;

use App\Database\DatabaseAdapter;

class CouponRepository
{
    private DatabaseAdapter $adapter;

    public function __construct(DatabaseAdapter $adapter)
    {
        $this->adapter = $adapter;
    }

    public function findByCode(string $code): ?object
    {
        $result = $this->adapter->query(
            "SELECT * FROM Coupons WHERE code = ?",
            [$code]
        );
        
        if (empty($result)) {
            return null;
        }
        
        $coupon = (object)$result[0];
        
        // isValid metodunu ekle
        $coupon->isValid = function() use ($coupon) {
            return strtotime($coupon->expire_date) > time() && $coupon->usage_limit > 0;
        };
        
        return $coupon;
    }
}

