<?php

namespace App\Http\Controllers;

use App\Models\Author;
use App\Models\Book;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Shuchkin\SimpleXLSX;
use ZipArchive;

class BookController extends Controller
{
    public function index(Request $request)
    {
        $sort = json_decode(urldecode($request->query("sorting","")),true);
        $globalSearch = $request->query("globalFilter","");
        //$filter = $request->query("filters","");
        $size = intval($request->query("size",10));
        $page = $request->query("page",0);
        $books = DB::table('book')
                ->select('book.id','book_code','book_name', 'author_name', 'category_name','nbPages', 'description', 'owner', 'owner_phone', DB::raw("IFNULL(coverUrl,'public/coverURL/Placeholder.png') as coverUrl"))
                ->join('authors','authors.id', '=','book.author')
                ->join('category','category.id', '=','book.category')
                ->where('state','=',1);

        // Apply global search
        if (!empty($globalSearch)) {
            $books->where(function ($query) use ($globalSearch) {
                $query->where('book_name', 'like', '%' . $globalSearch . '%')
                    ->orWhere('author_name', 'like', '%' . $globalSearch . '%')
                    ->orWhere('category_name', 'like', '%' . $globalSearch . '%')
                    ->orWhere('book_code', 'like', '%' . $globalSearch . '%');
            });
        }

        if (!empty($sort)) {
            foreach ($sort as $sortItem) {
                $column = $sortItem['id'];
                $direction = $sortItem['desc'] ? 'desc' : 'asc';
                $books->orderBy($column, $direction);
            }
        }
        else
        {
            $books->orderByRaw("CASE
            WHEN book_code IN ('C0005', 'C0008', 'a0025', 'b0013', 'C0012', 'C0011', 'C0026', 'C0018', 'a0085') THEN 1
                ELSE CASE WHEN book_code LIKE 'C%' THEN 2 ELSE 3 END
            END");
        }
        $books = $books->paginate($size);

        return response()->json($books,200);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function bulk(Request $request)
    {
        /*try {*/
            if(isset($_FILES['files']))
            {
                $path=Storage::put('public/excel',$request->file('files'));
                $url=dirname(__DIR__, 3).'/storage/app/';
                $files=Storage::files('public/coverURL');
                $fileMap = [];

                foreach ($files as $key => $file) {
                    $img =explode("/",$file)[2];
                    $imgname=explode(".",$img)[0];
                    $fileMap[$imgname] = $key;
                }
                //return response()->json(["map"=>$fileMap, "files"=>$files, "path"=>$url.$path], 201);
                if ( $xlsx = SimpleXLSX::parse($url.$path))
                {
                    $allRows = $xlsx->rows();
                    for ( $i=1; $i < count($allRows); $i++) {
                        $temp=new Book();
                        $temp->book_code = trim($allRows[$i][0]);
                        $temp->book_name = trim($allRows[$i][1]);

                        $dt = Author::where('author_name','=',trim($allRows[$i][2]))->get();
                        if($dt->isEmpty())
                        {
                            $autht=new Author();
                            $autht->author_name=trim($allRows[$i][2]);
                            $auth=Author::Create($autht->toArray());
                            $id=$auth->id;
                        }
                        else
                            $id=$dt[0]['id'];
                        $temp->author =  $id;

                        $dt = Category::where('category_name','=',trim($allRows[$i][3]))->get();
                        if($dt->isEmpty())
                        {
                            $autht=new Category();
                            $autht->category_name=trim($allRows[$i][3]);
                            $auth=Category::Create($autht->toArray());
                            $id=$auth->id;
                        }
                        else
                            $id=$dt[0]['id'];
                        $temp->category =  $id;

                        $temp->nbPages = intval($allRows[$i][4]);
                        $temp->owner = trim($allRows[$i][6]);
                        $temp->owner_phone = trim($allRows[$i][7]);
                        $temp->added_by = intval($allRows[$i][9]);
                        $temp->description = trim($allRows[$i][5]);
                        if(array_key_exists(trim($allRows[$i][0]),$fileMap)){
                            $temp->coverURL = $files[$fileMap[trim($allRows[$i][0])]];
                        }
                        $book = Book::create($temp->toArray());
                    }
                    Storage::delete($path);
                    return response()->json(["map"=>$fileMap, "files"=>$files, "path"=>$url.$path], 201);
                }
                else
                    return response()->json(['error parse' => SimpleXLSX::parseError()], 422);
            }
            else
            return response()->json(['error isfiles' => $_FILES], 422);
        /*} catch (\Exception $th) {
            return response()->json(["error"=>$th], 500);
        }*/
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
             $request->validate([
                'book_code' =>'required|unique:book',
                'coverURL' =>  'sometimes|file|image|mimes:jpeg,png,gif,jpg|max:2048',
                'author' => 'required',
                'category' => 'required',
                'nbPages' => 'required|numeric',
                'book_name' => 'required',
                'owner' => 'required',
                'owner_phone' => 'required',
                'added_by' => 'required',
            ]);
            // if not inserted then save it
                $temp = new Book;
                $temp->book_code = $request->input('book_code');
                $temp->book_name = $request->input('book_name');
                $temp->author =  $request->input('author');
                $temp->category =  $request->input('category');
                $temp->nbPages = $request->input('nbPages');
                $temp->owner = $request->input('owner');
                $temp->owner_phone = $request->input('owner_phone');
                $temp->added_by = $request->input('added_by');
                $temp->description = $request->input('description');
                if(isset($_FILES['coverURL'])){
                $temp->coverURL = Storage::put('public/coverURL', $request->file('coverURL'));}
                $book = Book::create($temp->toArray());
        return response()->json(['status' => 'data insereted successflly', 'id' => $book->id], 201);

    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $book = DB::table('book')->find($id);
        if($book == null)
            return response()->json('Book Not Found', 404);
        $authorR = DB::table('authors')->find($book->author);
        $author = $authorR->author_name;
        $categoryR = DB::table('category')->find($book->category);
        $category = $categoryR->category_name;
        $userR = DB::table('users')->find($book->added_by);
        $user = $userR->name;
        return Response()->json(['id'=>$book->id,
        'book_code'=> $book->book_code,
        'book_name'=>$book->book_name,
        'author '=>$author,
        'category '=>$category,
        'nbPages'=>$book->nbPages,
        'owner'=>$book->owner,
        'owner_phone'=>$book->owner_phone,
        'state'=>$book->state,
        'description'=>$book->description,
        'added_by'=>$user,
        'coverUrl'=>$book->coverUrl,
        'active'=>$book->active,],200);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Book $book)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        try {
            $validation = $request->validate([
                'book_code' =>'required',
                'coverURL' =>  'file|image|mimes:jpeg,png,gif,jpg|max:2048',
                'author' => 'required',
                'category' => 'required',
                'nbPages' => 'required',
                'book_name' => 'required',
                'owner' => 'required',
                'owner_phone' => 'required',
                'added_by' => 'required',
            ]);
            //echo $request;
            $temp = Book::findOrFail($id);
            //     echo asset($object->ID_URL)."             ".$object->Vaccine_URL;
            $temp->book_name = $request->input('book_name');
            $temp->book_code = $request->input('book_code');
            $temp->author =  $request->input('author');
            $temp->category =  $request->input('category');
            $temp->nbPages = $request->input('nbPages');
            $temp->owner = $request->input('owner');
            $temp->owner_phone = $request->input('owner_phone');
            $temp->added_by = $request->input('added_by');
            $temp->description = $request->input('description');
            if(isset($_FILES["coverURL"])){
                Storage::delete($temp->coverURL);
                $temp->coverURL = Storage::put('public/coverURL', $request->file('coverURL'));}
            $temp->save();
        } catch (\Exception $e) {
            return response()->json($e, 404);
        }

        return response()->json(['status' => 'data updated successflly'], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        try{
            $book = Book::findOrFail($id);
            $book -> active = "0";
            $book -> save();
        }
        catch (\Exception $e) {
            return response()->json($e, 404);
        }

        return response()->json(['status' => 'Book deleted successflly'], 200);
    }
}
