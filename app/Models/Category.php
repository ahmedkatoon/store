<?php

namespace App\Models;

use App\Models\Product;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Category extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $guarded = [];
    public function products(): HasMany
    {
        return $this->hasMany(Product::class, "category_id", "id");
    }
    public function parent(): BelongsTo
    {
        return $this->belongsTo(Category::class, "parent_id", "id")
        ->withDefault([
            "name"=>"main"
        ]
        );
    }
    public function children(): HasMany
    {
        return $this->hasMany(Category::class, "parent_id", "id");
    }
    public function scopeActive(Builder $builder)
    {
        $builder->where("status", "active");
    }
    public function scopeStatus(Builder $builder, $status)
    {
        $builder->where("status", $status);
    }
    public function scopeFilter(Builder $builder, $filters)
    {
        if ($filters["name"] ?? false) {
            $builder->where("categories.name", "LIKE", "%{$filters["name"]}%");
        }
        if ($filters["status"] ?? false) {
            $builder->where("categories.status", "=", $filters["status"]);
        }
    }
    public static function rules($id = 0)
    {
        return [
            "name" => [
                "required",
                "string",
                "unique:categories,name,$id",
                "min:3",
                "max:255",
                // function ($attribute, $value, $fails) {
                //     if (strtolower($value) == "laravel") {
                //         $fails("this name is forbidden !");
                //     }
                // }
                "filter:php,laravel"               //in providers app
            ],
            "parent_id" => "nullable|int|exists:categories,id",
            "status" => "required|in:active,archived",
            "image" => "image|mimes:png,jpg",
            "description" => "nullable|string",

        ];
    }
}
