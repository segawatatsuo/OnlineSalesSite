<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CorporateCustomerAddress extends Model
{
    protected $fillable = [
        'corporate_customer_id',
        'type',
        'company_name',
        'department',
        'sei',
        'mei',
        'phone',
        'email',
        'zip',
        'add01',
        'add02',
        'tel',
        'fax',
        'is_main',
        'add01',
        'add02',
        'add03',
        'tel',
        'fax',
        'is_main'
    ];

    public function corporateCustomer()
    {
        return $this->belongsTo(CorporateCustomer::class);
    }
}
