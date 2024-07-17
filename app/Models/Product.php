<?php

namespace App\Models;

use Illuminate\Support\Str;
use App\Models\Scopes\StoryScope;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Product extends Model
{
    use HasFactory;
    // protected $guarded = [];
    protected $fillable = [
        'name', 'slug', 'description', 'image', 'category_id', 'store_id',
        'price', 'compare_price', 'status',
    ];
    protected $hidden=[
        "deleted_at","updated_at","created_at","image"
    ];
    protected $appends =[
        "image_url"
    ];
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class, "category_id", "id");
    }
    public function store(): BelongsTo
    {
        return $this->belongsTo(Store::class, "store_id", "id");
    }

    protected static function booted()
    {
        // static::addGlobalScope("store",new StoryScope());
        parent::boot();
        static::addGlobalScope(new StoryScope());
        static::creating(function(Product $product){
            $product->slug = Str::slug($product->name);
        });
    }
    public function tags(): BelongsToMany
    {
        return $this->belongsToMany(
            Tag::class,
            "product_tag",
            "product_id",
            "tag_id",
            "id",
            "id"
        );
    }
    public function scopeActive(Builder $builder)
    {
        $builder->where("status", "active");
    }
    public function getImageUrlAttribute()
    {
        if (!$this->image) {

            return "https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcTqBvYakvoiAToza2R3HkQjrGmgemRxyC3g9kGbGXyU9uUf2iLZDowSLDz0Dg&s";
        }
        if (Str::startsWith($this->image, ["http://", "https://"])) {
            return $this->image;
        }
        return asset("storage/" . $this->image);
    }
    public function getSalePercentAttribute()
    {
        if (!$this->compare_price) {
            return 0;
        }
        return number_format( 100 - (100 * $this->price / $this->compare_price),1);
    }

    public function scopeFilter(Builder $builder,$filters)
    {
        $options = array_merge([
            "store_id"=>null,
            "category_id"=>null,
            "tag_id"=>null,
            "status"=>"active",
        ],$filters);

        $builder->when($options["store_id"],function($builder,$value){
            $builder->where("store_id",$value);
        });
        $builder->when($options["category_id"],function($builder,$value){
            $builder->where("category_id",$value);
        });
        $builder->when($options["status"],function($builder,$value){
            $builder->where("status",$value);
        });
        $builder->when($options["tag_id"],function($builder,$value){
            $builder->whereExists(function($query)use ($value){
                $query->select(1)
                ->from("product_tag")
                ->whereRaw("product_id=products.id")
                ->where("tag_id",$value);
            });
        });
    }
}
