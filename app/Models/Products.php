<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Products extends Model
{
    
    public function currency() {
        return $this->hasOne(Currencies::class, 'id', 'currency_id');
    }

    public function prices() {
        return $this->hasMany(Prices::class);
    }

}
