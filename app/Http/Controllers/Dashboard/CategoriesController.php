<?php

namespace App\Http\Controllers\Dashboard;

use App\Models\Category;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\CategoryRequest;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\Storage;

use function Laravel\Prompts\select;

class CategoriesController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        // $query = Category::query();
        $name = $request->name;
        $status = $request->status;
        // if($name){
        //     // $query->where("name",$name);
        //     $query->where("name","LIKE","%{$name}%");
        // }
        // if($status){
        //     $query->where("status",$status);
        // }

        // $categories = $query->paginate(4);
        // $categories = Category::filter($request->all())->latest()->paginate(4);

        // $categories = Category::leftJoin("categories as parents" ,"parents.id","=","categories.parent_id")
        // ->select([
        //     "categories.*",
        //     "parents.name as parent_name"
        // ])
        // ->filter($request->all())
        // ->orderBy("categories.name")
        // ->paginate(4);


        $categories = Category::with("parent")
            ->select("categories.*")
            ->selectRaw("(SELECT COUNT(*) FROM products WHERE  category_id = categories.id) as products_count")
            // ->withCount("products")
            ->filter($request->all())
            ->orderBy("categories.name")
            ->paginate(4);
            // ->dd();

        // $categories = Category::paginate(4);
        // $categories = Category::active()->paginate();
        // $categories = Category::status("archived")->paginate();
        return view("dashboard.categories.index", compact("categories", "name"));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {

        $parents = Category::all();
        $category = new Category();
        return view("dashboard.categories.create", compact("parents", "category"));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // $data = $request->all();
        $data = $request->validate(Category::rules(), [
            "unique" => "This name  is already exists !",
            "name.required" => "This field (:attribute) is required"
        ]);
        if ($request->hasFile("image")) {
            $file = $request->image;
            // $path = $file->store("uploads","public");

            // $path = Storage::putFile('uploads', $data['image']); disk = public  in .env
            $path = $file->store("uploads", [
                "disk" => "public"
            ]);
            $data["image"] = $path;
        }
        $data["slug"] = Str::slug($request->name);
        $category = Category::create($data);
        // Category::create([
        //     "name" => $request->name,
        //     "description" => $request->description,
        //     "parent_id" => $request->parent_id,
        //     "image" => $request->image,
        //     "status" => $request->status,
        //     "slug" => Str::slug($request->name),

        // ]);

        // return redirect()->route("categories.index")->with("success","category created");
        session()->flash("success", "category created");
        return redirect("/dashboard/categories");
    }

    /**
     * Display the specified resource.
     */
    public function show(Category $category)
    {
        return view("dashboard.categories.show",compact("category"));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $category = Category::findOrFail($id);
        $parents = Category::where("id", "<>", $id)->where(function ($query) use ($id) {

            $query->whereNull("parent_id")
                ->orWhere("parent_id", "<>", $id);
        })
            ->get();
        return view("dashboard.categories.edit", compact("category", "parents"));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(CategoryRequest $request, string $id)
    {
        // $data = $request->validate(Category::rules($id));
        $category = Category::findOrFail($id);
        $old_image = $category->image;
        $data = $request->all();

        // if ($request->hasFile("image")) {
        //     $file = $request->image;
        //     // $path = $file->store("uploads","public");
        //     $path = $file->store("uploads", [
        //         "disk" => "public"
        //     ]);
        //     $data["image"] = $path;
        // }
        $new_image = $this->uploadImage($request);
        if ($new_image) {
            $data["image"] = $new_image;
        }
        if ($old_image && $new_image) {
            Storage::disk("public")->delete($old_image);
        }
        $data["slug"] = Str::slug($request->name);
        $category->update($data);

        session()->flash("success", "category updated");
        return redirect("/dashboard/categories");
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $category = Category::findOrFail($id);
        $category->delete();


        // Category::destroy($id);
        session()->flash("success", "category deleted ");
        return redirect("/dashboard/categories");
    }

    protected function uploadImage(Request $request)
    {
        if (!$request->hasFile("image")) {
            return;
        }
        $file = $request->image;
        $path = $file->store("uploads", [
            "disk" => "public"
        ]);
        return $path;
    }

    public function trash()
    {
        $categories = Category::onlyTrashed()->paginate();
        return view("dashboard.categories.trash", compact("categories"));
    }
    public function restore($id)
    {
        $category = Category::onlyTrashed()->findOrFail($id);
        $category->restore();

        return redirect(route("categories.trash"))->with("success", "category restored");
    }
    public function forceDelete($id)
    {
        $category = Category::onlyTrashed()->findOrFail($id);
        $category->forceDelete();
        if ($category->id == $category->par)
            if ($category->image) {
                Storage::disk("public")->delete($category->image);
            }

        return redirect(route("categories.trash"))->with("success", "category delete forever");
    }
}
