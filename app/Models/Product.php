<?php

namespace App\Models;

use App\Models\Categorie;
use App\Models\OrderDetail;
use App\Models\Suppliers;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_name',
        'supplier_id',
        'category_id',
        'quantity_per_unit',
        'unit_price',
        'units_in_stock',
        'units_on_order',
        'reorder_level',
        'discontinued'
    ];

    public function categories()
    {
        return $this->belongsTo(Categorie::class);
    }

    public function suppliers()
    {
        return $this->belongsTo(Suppliers::class);
    }

    public function orderDetails()
    {
        return $this->hasMany(OrderDetail::class, 'product_id');
    }
}
