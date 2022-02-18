<?php


namespace App\Http\Controllers;

use http\Env\Response;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProductController
{

    public function getProducts()
    {
        $r = DB::select("SELECT * FROM products p LIMIT 1000");
//        foreach ($r as $item) {
//            $p = DB::select("SELECT * FROM mayoreo m WHERE m.productid = ?",[$item->ItemId]);
//            $item->price = $p;
//        }
        return response()->json(["success" => true, "payload" => $r]);
    }

    public function editProduct(Request $request, $pid)
    {
        $bodyParams = $request->input();
        if (isset($bodyParams["productid"])) {
            $p = DB::select("SELECT * FROM products p WHERE p.ItemId = ?", [$bodyParams["productid"]]);
            if (count($p)) {
                $r = DB::update("UPDATE products SET Name = ?,Price = ?,code = ?,alias = ? WHERE products.ItemId = ?", [
                    $bodyParams["name"],
                    $bodyParams["price"],
                    $bodyParams["code"],
                    $bodyParams["alias"],
                    $bodyParams["productid"]
                ]);
                return response()->json(["success" => true, "payload" => ["updated" => $r], "input" => $bodyParams]);
            }
            return response()->json(["success" => false, "message" => "Producto inexistente"]);
        }
        return response()->json(["success" => false, "message" => "Producto inexistente"]);
    }

    public function getProduct(Request $request, $pid)
    {
        if ($pid != null) {
            $p = DB::select("SELECT * FROM products p WHERE p.code = ? OR p.alias = ? LIMIT 1", [$pid, $pid]);
            if (count($p) > 0) {
                $p = json_decode(json_encode($p[0], true));
                $i = DB::select("SELECT * FROM inventarios i WHERE i.pruductid = ?", [$p->ItemId]);
                if ($p->stockable) {
                    if (count($i) == 0) {
                        $invId = DB::table("inventarios")
                            ->insertGetId(["pruductid" => $p->ItemId, "ammount" => 0]);
                        $i = DB::select("SELECT * FROM inventarios i WHERE i.idinventarios = ?", [$invId]);
                    }
                    $us = DB::select("SELECT * FROM provider_bill_detail m WHERE m.product_id = ? ORDER BY m.created_at DESC LIMIT 1", [$p->ItemId]);
                    if (count($us)) {
                        $p->lim = $us[0];
                    }
                    $p->inventory = $i[0];
                    return response()->json(["success" => true, "payload" => $p]);
                } else {
                    return response()->json(["success" => false, "message" => "Producto no inventariable"]);
                }
            } else {
                return response()->json(["success" => false, "message" => "Codigo de producto inexistente"]);
            }
        } else {
            return response()->json(["success" => false, "message" => "Se necesita el codigo de producto"]);
        }
    }
}
