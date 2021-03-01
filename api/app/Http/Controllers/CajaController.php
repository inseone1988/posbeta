<?php


namespace App\Http\Controllers;

use App\Models\Caja;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;


class CajaController
{

    public function getActiveCaja(){
        $r = DB::select("SELECT * FROM caja c WHERE c.Status LIKE(?) ORDER BY c.idcaja DESC LIMIT 1",["ACTIVE"]);
        if (count($r) === 1){
            $rTotal = DB::select("SELECT SUM(od.Quantity * od.price) as total FROM orderdetails od WHERE od.cajaid = ? AND od.Status = 1",[$r[0]->idcaja]);
            $ret = DB::select("SELECT IFNULL(SUM(ammount),0) as retiros FROM retiros WHERE caja = ? AND `type` = 'retiro'",[$r[0]->idcaja]);
            $r[0]->total = $rTotal[0]->total;
            $r[0]->retiros = $ret[0]->retiros;
            return response()->json(["success" => true,"payload"=>$r]);
        }
        return response()->json(["success"=> false, "message"=>"No se han encontrado datos"]);;
    }

    public function getCaja($caja){
        $r = DB::select("SELECT * FROM caja WHERE idcaja = ?",[$caja]);
        if (count($r) === 1){
            $rTotal = DB::select("SELECT SUM(od.Quantity * od.price) as total FROM orderdetails od WHERE od.cajaid = ? AND od.Status = 1",[$caja]);
            $ret = DB::select("SELECT IFNULL(SUM(ammount),0) as retiros FROM retiros WHERE caja = ? AND `type` = 'retiro'",[$caja]);
            $r[0]->retiros = $ret[0]->retiros;
            $r[0]->total = $rTotal[0]->total;
            return response()->json(["success" => true,"payload"=>$r]);
        }
        return response()->json(["success"=> false, "message"=>"No se han encontrado datos"]);
    }

    public function getRetirosFromCaja($cajaid){
        $r = DB::select("SELECT * FROM retiros WHERE caja = ? AND type = 'retiro'",[$cajaid]);
        return response()->json(["success"=> true,"payload"=>$r]);
    }
}
