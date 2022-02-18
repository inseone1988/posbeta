<?php


namespace App\Http\Controllers;


use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProviderBillController
{

    private static function getBillTotal($products)
    {
        $total = 0;
        foreach ($products as $product) {
            $total += (round(floatval($product["surtimiento"]["cost"]), 2) * intval($product["surtimiento"]["quantity"]));
        }
        return $total;
    }

    public function saveNewBill(Request $request)
    {
        $nota = $request->all();
//        var_dump($nota);
        //1.- Actualizamos precio y costo de los productos
        $products = $nota["products"];
        foreach ($products as $product) {
            DB::update("UPDATE products SET Price = ?, costo = ? WHERE ItemId = ?", [$product["surtimiento"]["pv"], $product["surtimiento"]["cost"],$product["ItemId"]]);
        }
        //2.- Ingresamos la nota del provedor
        $billtotal = $this::getBillTotal($products);
        $billId = $nota["billid"] ?? time();
        DB::insert("INSERT INTO provider_bills(provider_id, amount, balance,billId) VALUES (?,?,?,?)", [$nota["provid"], $billtotal, $nota["btype"] == "contado" ? 0 : $billtotal,$billId   ]);
        //3.- Ingresamos los detalles de la nota
        foreach ($products as $product) {
            DB::insert("INSERT INTO provider_bill_detail(provider_bill_id, product_id, quantity, price) VALUES(?,?,?,?)", [$billId, $product["ItemId"], $product["surtimiento"]["quantity"], $product["surtimiento"]["cost"]]);
            //4.- Anotamos el movimiento de inventario
            $i = DB::select("SELECT * FROM inventarios i WHERE i.pruductid =?",[$product["ItemId"]]);
            if (count($i)>0){
                $nt = $i[0]->ammount += intval($product["surtimiento"]["quantity"]);
                DB::update("UPDATE inventarios SET ammount = ? WHERE pruductid = ?",[$nt,$product["ItemId"]]);
                DB::insert("INSERT INTO inventory_movements(provider_id, movement_type, quantity, reference, product_id) VALUES (?,?,?,?,?)",[$nota["provid"],"supply",$product["surtimiento"]["quantity"],$billId,$product["ItemId"]]);
            }
        }
        return response()->json(["success" => true,"payload"=>["billId"=>$billId]]);
    }

}
