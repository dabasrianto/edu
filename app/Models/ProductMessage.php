<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductMessage extends Model
{
    protected $fillable = ['user_id', 'product_id', 'message', 'is_read'];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
