<?php

namespace App\Http\Controllers;

use App\Models\Book;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;

class BookController extends Controller
{
    //  This method will show books listing page
    public function index(Request $request){
        $books= Book::orderBy('created_at','DESC');

            if(!empty($request->keyword)){
                $books->where('title','like','%'.$request->keyword.'%');
            }

        $books= $books->paginate(10);

        return view('books.list',[
            'books' =>$books
        ]);
    }

    // This method will show create book page
    public function create(){
        return view('books.create');
    }

    // This method will store a book in database
    public function store(Request $request){
        $rules=[
            'title' => 'required|min:5',
            'author' => 'required|min:3',
            'status' => 'required'
        ];

        if(!empty($request->image)){
            $rules['image']='image';
        }

        $validator = Validator::make($request->all(),$rules);

        if($validator->fails()){
            return redirect()->route('books.create')->withInput()->withErrors($validator);
        }

        $book= new Book();
        $book->title= $request->title;
        $book->author= $request->author;
        $book->description= $request->description;
        $book->status= $request->status;
        $book->save();

        // upload book image here
        if(!empty($request->image)){
            $image=$request->image;
            $ext= $image->getClientOriginalExtension();
            $imageName= time().'.'.$ext;
            $image->move(public_path('uploads/books/'),$imageName);

            $book->image= $imageName;
            $book->save();

            // Resize using Intervention Image v3 and GD
            $manager = new ImageManager(new Driver());

            $img = $manager->read(public_path('uploads/books/'). $imageName);
            $img->resize(990);

            $thumbPath = public_path('uploads/books/thumb');

            if (!file_exists($thumbPath)) {
                mkdir($thumbPath, 0755, true);
            }

            $img->save($thumbPath . '/' . $imageName);
        }

        return redirect()->route('books.index')->with('success','Book added successfully');
    }

    // This method will edit book page
    public function edit(){
        
    }

    // This method will update a book
    public function update(){

    }

    // This method will delete a book from database
    public function destroy(){

    }
}
