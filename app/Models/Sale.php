<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Sale extends Model
{
    public $table = "sale";
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'customers_id', 'sale_date', 'name_product', 'total_amount',
    ];

}
