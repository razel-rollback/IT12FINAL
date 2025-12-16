<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StockEditHistory extends Model
{
    use HasFactory;

    protected $table = 'stock_edit_history';

    protected $fillable = [
        'product_id',
        'cashier_id',
        'action',
        'field_changed',
        'old_value',
        'new_value',
        'remarks'
    ];

    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id', 'Product_ID');
    }

    public function cashier()
    {
        return $this->belongsTo(User::class, 'cashier_id');
    }
}