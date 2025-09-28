<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory;

    protected $fillable = ['brand', 'brand_name'];

    public function products()
    {
        return $this->hasMany(Product::class);
    }

    public function productJas()
    {
        return $this->hasMany(ProductJa::class, 'category', 'brand');
    }
    public function categorizations()
    {
        return $this->hasMany(Categorization::class);
    }
}
