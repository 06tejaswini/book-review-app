<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class BookController extends Controller
{
    //  This method will show books listing page
    public function index(){
        return view('books.list');
    }

    // This method will show create book page
    public function create(){
        return view('books.create');
    }

    // This method will store a book in database
    public function store(){
        
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
