<?php

namespace App\Http\Controllers;

use App\Models\Author;
use Illuminate\Http\Request;

class AuthorController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        try {
            $input= $request->query("input","");
            $guardians=Author::select('id','author_name');
            if(!empty($input))
            {
                $guardians->where('author_name', 'like', '%' . $input . '%');
            }
            $guardians->orderBy("author_name","asc")->get();
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
            "author_name" => 'required|unique:authors',
        ]);
        /*$data=Author::where('author_name',$request->input('author_name'))->get();
        if (!$data->isEmpty()) {
            return response()->json("data alreay exist", 200);
        }*/

        $temp=new Author;
        $temp->author_name=$request->input('author_name');
        $author = Author::Create($temp->toArray());
        //$pdf =base64_encode(self::guardianIdCard($guardian->id));
        return response()->json(['status'=>'data insereted successflly','newAdd'=>$author],201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Author $author)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Author $author)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Author $author)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Author $author)
    {
        //
    }
}
