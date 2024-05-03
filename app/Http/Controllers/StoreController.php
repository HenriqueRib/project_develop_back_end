<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use App\Models\Store;

class StoreController extends Controller
{

  public function all(Request $request){
    try {
      $stores = Store::with('books')->get();
      return response()->json(['success' => true, 'stores' => $stores], 200); 
    } catch (\Exception $e) {
      $errorData = [
        'arquivo' => $e->getFile(),
        'linha' => $e->getLine(),
        'erro' => $e->getMessage(),
      ];
      Log::channel("emergency")->error("Erro > store all", $errorData);
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
      DB::beginTransaction();
      $store = Store::create($params);
      DB::commit();
      $responseData = [
        'status' => true,
        'mensage' => 'Your store has been successfully registered!',
        'dados' => $params,
        'store' => $store
      ];
      return response()->json(['success' => true, 'responseData' => $responseData], 200);
    } catch (\Exception $e) {
      DB::rollback();
      $errorData = [
        'arquivo' => $e->getFile(),
        'linha' => $e->getLine(),
        'erro' => $e->getMessage(),
      ];
      Log::channel("emergency")->error("Erro > store create", $errorData);
      $responseData = [
          'status' => false,
          'mensage' => 'An error occurred when registering a store.',
          'error' => $errorData,
      ];
      return response()->json(['success' => false, 'responseData' => $responseData], 500);
    }
  }

  public function show(Request $request, $id){
    try {
      $store = Store::find($id);
      if(isset($store)){
        return response()->json(['success' => true, 'store' => $store], 200); 
      }
      return response()->json(['success' => false, 'store' => $store, 'message' => "No stores were found with the id $id."], 404); 
    } catch (\Exception $e) {
      $errorData = [
        'arquivo' => $e->getFile(),
        'linha' => $e->getLine(),
        'erro' => $e->getMessage(),
      ];
      Log::channel("emergency")->error("Erro > store show", $errorData);
      $responseData = [
          'status' => false,
          'message' => "An error occurred when querying the store with id $id.",
          'error' => $errorData,
      ];
      return response()->json(['success' => false, 'responseData' => $responseData], 500);
    }
  }

  public function update(Request $request, $id){
    try {
      $store = Store::find($id);
      if(isset($store)){
        $params = $request->all();
        $store->update($params);
        return response()->json(['success' => true, 'store' => $store], 200); 
      }
      return response()->json(['success' => false, 'store' => $store, 'message' => "No stores were found with the id $id."], 404); 
    } catch (\Exception $e) {
      $errorData = [
        'arquivo' => $e->getFile(),
        'linha' => $e->getLine(),
        'erro' => $e->getMessage(),
        'data' => $request->all(),
      ];
      Log::channel("emergency")->error("Erro > store update", $errorData);
      $responseData = [
          'status' => false,
          'message' => "An error occurred when updating the store with id $id.",
          'error' => $errorData,
      ];
      return response()->json(['success' => false, 'responseData' => $responseData], 500);
    }
  }
  
  public function delete(Request $request){
    try {
      $params = $request->all();
      $id = $params['id'];
      $store = Store::find($id);
      if(isset($store)){
        $store->delete();
        return response()->json(['success' => true, 'message' => "Store $store->name has been successfully deleted!"], 200); 
      }
      return response()->json(['success' => false, 'store' => $store, 'message' => "No stores were found with the id $id."], 404); 
    } catch (\Exception $e) {
      $errorData = [
        'arquivo' => $e->getFile(),
        'linha' => $e->getLine(),
        'erro' => $e->getMessage(),
        'data' => $request->all(),
      ];
      Log::channel("emergency")->error("Erro > Store delete", $errorData);
      $responseData = [
          'status' => false,
          'message' => "An error occurred while deleting store",
          'error' => $errorData,
      ];
      return response()->json(['success' => false, 'responseData' => $responseData], 500);
    }
  }

}
