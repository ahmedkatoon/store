<?php

namespace App\Http\Controllers\Dashboard;

use App\Models\Tag;
use App\Models\Product;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class ProdouctController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $name = $request->name;
        $products = Product::paginate(4);
        // $products = Product::with(["category","store"])->paginate(4);
        return view("dashboard.produtcs.index", compact("products", "name"));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $product = Product::findOrFail($id);
        $tags = implode(",", $product->tags()->pluck("name")->toArray());
        // dd($tags);
        return view("dashboard.produtcs.edit", compact("product", "tags"));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Product $product)
    {
        $product->update($request->except("tags"));
        $tags = json_decode($request->tags);
        $tag_ids = [];
        $saved_tags = Tag::all();
        foreach ($tags as $item) {
            $slug = Str::slug($item->value);
            $tag = $saved_tags->where("slug", $slug)->first();
            if (!$tag) {
                $tag = Tag::create([
                    "name" => $item->value,
                    "slug" => $slug
                ]);
            }
            $tag_ids[] = $tag->id;
        }
        $product->tags()->sync($tag_ids);
        return redirect(route("products.index"))->with("success", "producted updated");
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
