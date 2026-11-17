<?php

namespace App\Http\Controllers;

use App\Models\Review;
use Illuminate\Http\Request;

class ReviewController extends Controller
{
    //this method will show revies in backend
    public function index(){
        $reviews= Review::orderBy('created_at','DESC')->paginate(10);
        return view('account.reviews.list',[
            'reviews'=>$reviews
        ]);
    }
}
