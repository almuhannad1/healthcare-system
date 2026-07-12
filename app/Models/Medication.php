<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Medication extends Model
{
    use HasFactory;

    protected $primaryKey = 'medication_id';

    protected $fillable = [
        'name', 'strength', 'stock_quantity', 'low_stock_threshold', 'price_cents',
    ];

    // a tiny business-logic method living on the model. 
    public function isLowStock(): bool
    {
        return $this->stock_quantity <= $this->low_stock_threshold;
    }
}
