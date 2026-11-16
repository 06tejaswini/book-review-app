<?php

namespace App\Http\Controllers;

use App\Models\Book;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    //This method will show home page
    public function index(Request $request){
        $books= Book::orderBy('created_at','DESC');
        if(!empty($request->keyword)){
            $books->where('title', 'like', '%'.$request->keyword.'%');
        }
       $books= $books->where('status',1)->paginate(4);

        return view('home',[
            'books'=>$books
        ]);
    }
    // This method wil show book detail page 
    public function detail($id){
        $book = Book::findOrFail($id);
        if($book->status==0){
            abort(404);
        }
        $relatedBooks = Book::where('status',1)->take(3)->where('id','!=',$id)->inRandomOrder()->get();
        // dd($relatedBooks);

        return view('book-detail',[
            'book'=>$book,
            'relatedBooks'=>$relatedBooks
        ]);
    }
} 
