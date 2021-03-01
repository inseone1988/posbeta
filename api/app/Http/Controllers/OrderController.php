<?php


namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OrderController
{

    public function getCajaOrders($cajaid){
        $r = DB::select("SELECT * FROM orders o  WHERE o.cajaid = ? ORDER BY o.OrderId DESC",[$cajaid]);
        foreach ($r as $item) {
            $oid = $item->OrderId;
            $det = DB::select("SELECT * FROM orderdetails od WHERE OrderId = ? AND od.ProductId IS NOT NULL",[$oid]);
            foreach ($det as $detItem) {
                $pid = $detItem->ProductId;
                $prod = DB::select("SELECT * FROM products p WHERE p.ItemId = ?",[$pid]);
                $detItem->product = $prod[0];
            }
            $paym = DB::select("SELECT * FROM payment p WHERE p.OrderId = ?",[$oid]);
            $item->details = $det;
            $item->payment = $paym;
        }
        return response()->json(["success" => true,"payload"=>$r,"count"=>count($r)]);
    }

}
