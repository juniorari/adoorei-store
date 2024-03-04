<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SalesProduct extends Model
{
    use HasFactory;

    protected $fillable = ['product_id', 'sale_id','name', 'price', 'amount'];

    protected $hidden = ['created_at', 'updated_at', 'id', 'sale_id'];

    public function product(): BelongsTo
    {
        return $this->belongsTo(SalesProduct::class, 'sale_id');
    }

    public function sale(): BelongsTo
    {
        return $this->belongsTo(Sale::class);
    }
}
