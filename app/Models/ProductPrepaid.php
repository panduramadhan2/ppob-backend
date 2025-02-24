<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductPrepaid extends Model
{
    use HasFactory;

    protected $table = 'product_prepaid';
    protected $primaryKey = 'id';
    protected $fillable = [
        'product_name',
        'product_desc',
        'product_category',
        'product_provider',
        'product_type',
        'product_seller',
        'product_seller_price',
        'product_buyer_price',
        'product_sku',
        'product_unlimited_stock',
        'product_stock',
        'product_multi',
    ];
}
