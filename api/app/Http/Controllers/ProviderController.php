<?php


namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProviderController
{
    private static function providerExists($social_name, $rfc)
    {
        $e = DB::select("SELECT * FROM providers p WHERE p.provider_name = ? OR p.provider_tax_id = ?", [$social_name, $rfc]);
        return count($e) > 0;
    }

    private static function saveProvider(Request $request)
    {
        $active = $request->input("active");
        if ($active == "") $active = 1;
        $struct = [
            $request->input("provider_name"),
            $request->input("provider_social_name"),
            $request->input("provider_tax_id"),
            $request->input("provider_address")

        ];
        if (!ProviderController::providerExists(
            $request->input("provider_social_name"),
            $request->input("provider_tax_id")
        )
        ) {
            $r = DB::insert("INSERT INTO providers(provider_name, provider_social_name, provider_tax_id, provider_address,active) VALUES(?,?,?,?,1)", $struct);
            return ["success" => true, "payload" => $r];
        } else {
            return ["success" => false, "message" => "Proveedor ya registrado"];
        }
    }

    public function getActiveProviders()
    {
        $r = DB::select("SELECT id,active,provider_name,provider_address,provider_social_name,provider_tax_id FROM providers p WHERE p.active = true");
        foreach ($r as $provider) {
            $bills = DB::select("SELECT * FROM provider_bills pb WHERE pb.provider_id = ? ORDER BY pb.id DESC LIMIT 10",[$provider->id]);
            foreach ($bills as $bill) {
                $detail = DB::select("SELECT * FROM provider_bill_detail bd WHERE bd.provider_bill_id = ?",[$bill->billId]);
                $bill->details = $detail;
            }
            $provider->notas = $bills;
        }
        return response()->json(["success" => true, "payload" => $r, "count" => count($r)]);
    }

    public function saveOrUpdateProvider(Request $request)
    {
        if ($request->has("id")) {
            $r = DB::select("select * from providers p where p.id = ?", [$request->input("id")]);
            if (count($r) > 0) {
                $struct = [
                    $request->input("provider_name"),
                    $request->input("provider_social_name"),
                    $request->input("provider_tax_id"),
                    $request->input("provider_address"),
                    $request->input("active"),
                    $request->input("id")
                ];
                $r = DB::update("UPDATE providers p SET p.provider_name = ?,p.provider_social_name = ?, p.provider_tax_id = ?, p.provider_address = ?, p.active = ? WHERE p.id = ?", $struct);
                return response()->json(["success" => true, "payload" => $r]);
            }
            return response()->json(["success" => false, "message" => "Proveedor inexistente"]);
        }
        return response()->json($this::saveProvider($request));
    }
}
