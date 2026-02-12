<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Territories extends Model
{
    use HasFactory;

    protected $fillable = [
        'territory_description',
        'region_id',
    ];
}
