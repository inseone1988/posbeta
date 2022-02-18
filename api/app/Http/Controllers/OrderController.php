<?php


namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Database\QueryException;
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

    public function newOrder(){
        try{
            $r = DB::table("orders")
                ->insertGetId(["OrderId"=>null]);
            if ($r != 0){
                $response = ["success"=>true,"message"=>"","payload"=>["orderId"=>$r]];
                return response()->json($response);
            }
        }catch(QueryException $ex){
            $response = [
                "success"=>false,
                "message"=>$ex,
                "payload"=>[]
            ];
            return response()->json($response);
        }


    }

    public function addItemToOrder(Request $request){
        $response = [];
        try{
            $orderId = $request->input("orderId");
            $itemId = $request->input("itemId");
            $item = $request->has("sbc") ? DB::select("SELECT * FROM products p WHERE p.code = ? OR p.alias = ? LIMIT 1",[$itemId,$itemId]) : DB::select("SELECT * FROM products p WHERE p.ItemId = ? OR p.code = ? LIMIT 1",[$itemId,$itemId]);
            var_dump($item);
            if ($request->has("orderId")&& $request[$orderId]!=0){
                $order = DB::select("SELECT * FROM orders o WHERE o.OrderId = ? ",[$orderId]);
                if (count($order)>0){
                    var_dump($item);
//                    $ir = DB::insert("INSERT INTO orderdetails() VALUES()");
                }else{
                    response()->json(["success"=>false, "message"=>"Orden inexistente"]);
                }
                return response()->json($order);
            }else{
                $caja = DB::select("SELECT * FROM caja c WHERE c.Status = 'ACTIVE' LIMIT 1");
                if (count($caja) > 0){
                    //TODO: Resolve client id or default "Venta al publico en general"
                    $r = DB::table("orders")
                        ->insertGetId(["OrderId"=>null,"cajaid"=>$caja[0]->idcaja,"JobType"=>"Venta al publico","EmployeeId"=>$_SESSION["auth_user_id"], "CustomerId"=>"1"]);
                    if ($r != 0){
                        $ir = DB::table("orderdetails")
                            ->insertGetId(["OrderId"=>$r,"ProductId"=>$item[0]->ItemId,"Quantity"=>($request->has("quantity") ? $request["quantity"] : 1),"price"=>$item[0]->Price,"cajaid"=>$caja[0]->idcaja]);
                    }
                }else{
                    $response["success"] = false;
                    $response["message"] = "No hay cajas activas";
                    $response["payload"] = [];
                    return response()->json($response);
                }
            }
        }catch (Exception $ex){
            return response()->json(["success"=>false,"message"=>$ex->getMessage()]);
        }

    }

    private function getProductPrice($ItemId)
    {

    }

}
