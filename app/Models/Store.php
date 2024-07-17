<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Notifications\Notifiable;

class Store extends Model
{
    use HasFactory , Notifiable;
    // protected $guarded = [];

    public function products(): HasMany
    {
        return $this->hasMany(Product::class, "store_id", "id");
    }
}
