<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CorporateCustomer extends Model
{
    use HasFactory;

    protected $table = 'corporate_customers';

    protected $guarded = ['id'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function addresses()
    {
        return $this->hasMany(CorporateCustomerAddress::class);
    }

    public function orderAddresses()
    {
        return $this->addresses()->where('type', 'order');
    }

    public function deliveryAddresses()
    {
        return $this->addresses()->where('type', 'delivery');
    }

    public function mainOrderAddress()
    {
        return $this->orderAddresses()->where('is_main', true)->first();
    }

    public function mainDeliveryAddress()
    {
        return $this->deliveryAddresses()->where('is_main', true)->first();
    }

}
