<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Prices extends Model
{
    public function product()
    {
        return $this->belongsTo(Products::class, 'product_id', 'id');
    }

    public function currency()
    {
        return $this->belongsTo(Currencies::class, 'currency_id', 'id');
    }
}
