<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Supplier extends Model
{
    use SoftDeletes;

    protected $primaryKey = 'Supplier_ID';

    protected $fillable = [
        'Supplier_Name', 'contact_person', 'contact_number', 'address', 'payment_terms'
    ];

    protected $dates = ['deleted_at'];

    // Optional: get products supplied
    public function products() {
        return $this->hasMany(Product::class, 'Supplier_ID');
    }

    // Relationship with supplier transactions
    public function transactions() {
        return $this->hasMany(SupplierTransaction::class, 'Supplier_ID');
    }
}