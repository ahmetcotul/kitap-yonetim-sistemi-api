<?php

namespace App\Http\Controllers;
use App\Models\Book;


use Illuminate\Http\Request;

class BookController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
        return response()->json(Book::all(), 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'author'=> 'required|string|max:255',
            'description'=> 'nullable|string',
        ]);
        $book = Book::create($validated);
        return response()->json($book,201);

    }
    public function show(string $id)
    {
        $book = Book::find($id);
        if(!$book){
            return response()->json(['message'=>'Book not found'],404);
        }
        return response()->json($book,200);

    }
    /*
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $book = Book::find($id);
        if(!$book){
            return response()->json(['message'=>'Book not found'],404);

        }
        $validated = $request -> validate([
            'title' => 'sometimes|required|string|max:255',
            'author' => 'sometimes|required|string|max:255',
            'description' => 'nullable|string',

        ]);
        $book -> update($validated);
        return response()->json($book,200);
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $book = Book::find($id);
        if(!$book){
            return response()->json(['message'=>'Book not found'],404);
        }
        $book -> delete();
        return response()->json(['message'=>'Book deleted Successfully'],200);
    }
}
