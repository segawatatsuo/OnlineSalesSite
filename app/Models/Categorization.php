<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Categorization extends Model
{
    use HasFactory;

    protected $table = 'categorizations';

    protected $fillable = [
        'category',
        'major_classification',
        'classification',
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }
}
