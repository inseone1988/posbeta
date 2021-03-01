<?php


namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProductController
{

    public function getProducts(){
        $r = DB::select("SELECT * FROM products p LIMIT 1000");
//        foreach ($r as $item) {
//            $p = DB::select("SELECT * FROM mayoreo m WHERE m.productid = ?",[$item->ItemId]);
//            $item->price = $p;
//        }
        return response()->json(["success"=>true,"payload"=>$r]);
    }

    public function editProduct(Request $request){
        $bodyParams = $request->input();
        if (isset($bodyParams["productid"])){
            $p = DB::select("SELECT * FROM products p WHERE p.ItemId = ?",[$bodyParams["productid"]]);
            if (count($p)){
                $r = DB::update("UPDATE products SET Name = ?,Price = ?,code = ?,alias = ? WHERE products.ItemId = ?",[
                    $bodyParams["name"],
                    $bodyParams["price"],
                    $bodyParams["code"],
                    $bodyParams["alias"],
                    $bodyParams["productid"]
                ]);
                return response()->json(["success"=>true,"payload"=>["updated"=>$r],"input"=>$bodyParams]);
            }
            return response()->json(["success"=>false,"message"=>"Producto inexistente"]);
        }
        return response()->json(["success"=>false,"message"=>"Producto inexistente"]);
    }
}
