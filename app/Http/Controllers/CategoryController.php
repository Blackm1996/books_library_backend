<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        try {
            $input= $request->query("input","");
            $guardians=Category::select('id','category_name');
            if(!empty($input))
            {
                $guardians->where('category_name', 'like', '%' . $input . '%');
            }
            $guardians->orderBy("category_name","asc")->get();
        } catch (\Exception $e) {
            return response()->json('something went wrong', 404);
       }
       return json_encode($guardians);
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
        $validation=$request->validate([
            "category_name" => 'required|unique:category',
        ]);
        /*$data=Category::where('category_name',$request->input('category_name'))->get();
        if (!$data->isEmpty()) {
            return response()->json("data alreay exist", 200);
        }*/

        $temp=new Category;
        $temp->category_name=$request->input('category_name');
        $author = Category::Create($temp->toArray());
        //$pdf =base64_encode(self::guardianIdCard($guardian->id));
        return response()->json(['status'=>'data insereted successflly','newAdd'=>$author],201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Category $category)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Category $category)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Category $category)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Category $category)
    {
        //
    }
}
