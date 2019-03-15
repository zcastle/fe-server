<?php

namespace Comprobante;

use Psr\Container\ContainerInterface;
//
use Comprobante\Db;

class Action {

   protected $container;

   public function __construct(ContainerInterface $container) {
       $this->container = $container;
   }

   public function __invoke($request, $response, $args) {
        $result = array("success" => true, "error" => false, "response" => "");
        if($request->isPost()){
            $body = $request->getParsedBody();
            $dataB64 = $body["data"];
            $data = json_decode(base64_decode($dataB64));
            //
            $doc = isset($data->factura) ? $data->factura : $data->boleta;
            $row = [
                "sede" => $doc->EMI->codigoAsigSUNAT,
                "tipo_comprobante" => $doc->IDE->codTipoDocumento,
                "fecha" => $doc->IDE->fechaEmision . " " . $doc->IDE->horaEmision,
                "serie" => explode("-", $doc->IDE->numeracion)[0],
                "numero" => explode("-", $doc->IDE->numeracion)[1],
                "ruc" => $doc->REC->numeroDocId,
                "moneda" => $doc->IDE->tipoMoneda,
                "base" => $doc->CAB->gravadas->totalVentas,
                "igv" => $doc->CAB->montoTotalImpuestos,
                "servicio" => $doc->CAB->inafectas->totalVentas,
                "total" => $doc->CAB->importeTotal,
                "data" => $dataB64
            ];
            $db = new Db($this->container->get("db"), $this->container->get("logger"));
            if(!$db->existe($row["sede"], $row["tipo_comprobante"], $row["serie"], $row["numero"])){
                $result["response"] = array("id" => $db->save($row));
            }else{
                $result["error"] = true;
                $result["response"] = array("id" => 0, "message" => "El comprobante existe");
            }
        }else{
            $result["error"] = true;
            $result["response"] = $request->getUri() . " solo permite el metodo POST";
        }
        
        return $response->withJson($result);
   }
}

?>