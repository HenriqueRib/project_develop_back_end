<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use App\Models\Book;

class BookController extends Controller
{
    public function all(Request $request){
      try {
        $books = Book::with('store')->get();

        return response()->json(['success' => true, 'books' => $books], 200); 
      } catch (\Exception $e) {
        $errorData = [
          'arquivo' => $e->getFile(),
          'linha' => $e->getLine(),
          'erro' => $e->getMessage(),
        ];
        Log::channel("emergency")->error("Erro > book all", $errorData);
        $responseData = [
            'status' => false,
            'mensage' => 'An error occurred when querying all stores.',
            'error' => $errorData,
        ];
        return response()->json(['success' => false, 'responseData' => $responseData], 500);
      }
    }
  
    public function create(Request $request)
    {
      try {
        $params = $request->all();
        $v = Validator::make($request->all(), [
            'name' => 'required',
        ]);
        if ($v->fails()) {
            return response()->json([
                'status' => 'error',
                'errors' => $v->errors(),
            ], 422);
        }
    
        DB::beginTransaction();
        $book = Book::create($params);
        DB::commit();
        $responseData = [
          'status' => true,
          'mensage' => 'Your book has been successfully registered!',
          'dados' => $params,
          'book' => $book
        ];
        return response()->json(['success' => true, 'responseData' => $responseData], 200);
      } catch (\Exception $e) {
        DB::rollback();
        $errorData = [
          'arquivo' => $e->getFile(),
          'linha' => $e->getLine(),
          'erro' => $e->getMessage(),
        ];
        Log::channel("emergency")->error("Erro > book create", $errorData);
        $responseData = [
            'status' => false,
            'mensage' => 'An error occurred when registering a book.',
            'error' => $errorData,
        ];
        return response()->json(['success' => false, 'responseData' => $responseData], 500);
      }
    }
  
    public function show(Request $request, $id){
      try {
        $book = Book::with('store')->find($id);
        if(isset($book)){
          return response()->json(['success' => true, 'book' => $book], 200); 
        }
        return response()->json(['success' => false, 'book' => $book, 'message' => "No books were found with the id $id."], 404); 
      } catch (\Exception $e) {
        $errorData = [
          'arquivo' => $e->getFile(),
          'linha' => $e->getLine(),
          'erro' => $e->getMessage(),
        ];
        Log::channel("emergency")->error("Erro > book show", $errorData);
        $responseData = [
            'status' => false,
            'message' => "An error occurred when querying the book with id $id.",
            'error' => $errorData,
        ];
        return response()->json(['success' => false, 'responseData' => $responseData], 500);
      }
    }
  
    public function update(Request $request, $id){
      try {
        $book = Book::find($id);
        if(isset($book)){
          $params = $request->all();
          $book->update($params);
          return response()->json(['success' => true, 'book' => $book], 200); 
        }
        return response()->json(['success' => false, 'book' => $book, 'message' => "No books were found with the id $id."], 404); 
      } catch (\Exception $e) {
        $errorData = [
          'arquivo' => $e->getFile(),
          'linha' => $e->getLine(),
          'erro' => $e->getMessage(),
          'data' => $request->all(),
        ];
        Log::channel("emergency")->error("Erro > book update", $errorData);
        $responseData = [
            'status' => false,
            'message' => "An error occurred when updating the book with id $id.",
            'error' => $errorData,
        ];
        return response()->json(['success' => false, 'responseData' => $responseData], 500);
      }
    }
    
    public function delete(Request $request){
      try {
        $params = $request->all();
        $id = $params['id'];
        $book = Book::find($id);
        if(isset($book)){
          $book->delete();
          return response()->json(['success' => true, 'message' => "Book $book->name has been successfully deleted!"], 200); 
        }
        return response()->json(['success' => false, 'book' => $book, 'message' => "No books were found with the id $id."], 404); 
      } catch (\Exception $e) {
        $errorData = [
          'arquivo' => $e->getFile(),
          'linha' => $e->getLine(),
          'erro' => $e->getMessage(),
          'data' => $request->all(),
        ];
        Log::channel("emergency")->error("Erro > Book delete", $errorData);
        $responseData = [
            'status' => false,
            'message' => "An error occurred while deleting book",
            'error' => $errorData,
        ];
        return response()->json(['success' => false, 'responseData' => $responseData], 500);
      }
    }
  }
