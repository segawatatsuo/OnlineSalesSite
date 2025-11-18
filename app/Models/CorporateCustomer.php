<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CorporateCustomer extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    // --- リレーション ---
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function deliveryAddresses()
    {
        return $this->hasMany(DeliveryAddress::class);
    }

    public function defaultDeliveryAddress()
    {
        return $this->hasOne(DeliveryAddress::class)->where('is_default', true);
    }

    public function orders()
    {
        return $this->hasMany(Order::class, 'corporate_customer_id');
    }

    public function addresses()
    {
        return $this->hasMany(CorporateCustomerAddress::class);
    }




    // --- 関連ユーザー削除処理 ---
    protected static function boot()
    {
        parent::boot();

        static::deleting(function ($customer) {
            if ($customer->user) {
                $customer->user->delete();
            }
        });
    }
}
