<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Products extends Model
{

    protected $fillable = [
        'name',
        'description',
        'price',
        'currency_id',
        'tax_cost',
        'manufacturing_cost',
    ];

    protected $attributes = [
        'currency_id' => 1
    ];

    public function currency() {
        return $this->hasOne(Currencies::class, 'id', 'currency_id');
    }

    public function prices() {
        return $this->hasMany(Prices::class);
    }

}
