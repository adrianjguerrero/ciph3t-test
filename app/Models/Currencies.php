<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Currencies extends Model
{
    public function product()
    {
        return $this->belongsTo(Product::class, 'currency_id', 'id');
    }

    public function prices() {
        return $this->hasMany(Prices::class);
    }
}
