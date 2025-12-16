<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Customer extends Model
{
    use SoftDeletes;

    protected $primaryKey = 'Customer_ID';
    
    public $incrementing = true;

    protected $fillable = [
        'Customer_Name',
        'Contact_Number',
        'address',
        'email',
        'credit_limit',
        'payment_terms'
    ];

    protected $dates = ['deleted_at'];

    // Cast deleted_at as datetime
    protected $casts = [
        'deleted_at' => 'datetime',
    ];

    // Relationship with sales transactions
    public function salesTransactions()
    {
        return $this->hasMany(SalesTransaction::class, 'Customer_ID', 'Customer_ID');
    }

    // Relationship with sales
    public function sales()
    {
        return $this->hasMany(SalesTransaction::class, 'Customer_ID', 'Customer_ID');
    }
}