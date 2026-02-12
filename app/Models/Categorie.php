<?php

namespace App\Models;

use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Categorie extends Model
{
    use HasFactory;

    protected $fillable = [
        'category_name',
        'description',
        'picture'
    ];

    public function products()
    {
        return $this->hasMany(Product::class);
    }
}
