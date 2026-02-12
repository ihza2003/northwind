<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Customer_demographics extends Model
{
    use HasFactory;

    protected $fillable = [
        'customer_desc',
    ];
}
