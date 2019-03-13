<?php

use Slim\Http\Request;
use Slim\Http\Response;
//
use Lib\See;
use Lib\Db;
use Lib\File;
//
use Ramsey\Uuid\Uuid;
// Routes

$app->get('/', function (Request $request, Response $response, array $args) {
    // Sample log message
    $this->logger->info("Slim-Skeleton '/' route");

    // Render index view
    return $this->renderer->render($response, 'index.phtml', $args);
});

$app->group("/fe-server/v1", function(\Slim\App $app){

    $app->post('/comprobante', function(Request $request, Response $response, $args) {
        $result = array("success" => true, "response" => null, "test" => null);
        $db = new Db($this->db, $this->logger);
        //
        $uuid = Uuid::uuid1();
        $result["uuid"] = $uuid->toString();
        //
        /*$body = $request->getParsedBody();
        $result["test"] = $body["test"];
        $dataB64 = $body["data"];
        $data = json_decode(base64_decode($dataB64));*/

        /*$body = json_decode('{
            "usuario": "20000000000DEMO",
            "clave": "123",
            "test": true,
            "data": "eyJlbWlzb3IiOnsicnVjIjoiMjA1MTEwNDU1MjYiLCJyYXpvbl9zb2NpYWwiOiJET0dJQSBTLkEuQy4iLCJub21icmVfY29tZXJjaWFsIjoiT3N0ZXJpYSBkaSBHaWFuRnJhbmNvIENhZmZlIiwiZGlyZWNjaW9uIjoiQVYgQU5HQU1PUyBPRVNURTU5OCBNSVJBRkxPUkVTIExJTUEgUEVSVSIsInVyYmFuaXphY2lvbiI6ImxhcyBtYWVyaWNhcyIsImRlcGFydGFtZW50byI6Ii0iLCJwcm92aW5jaWEiOiItIiwiZGlzdHJpdG8iOiItIiwidWJpZ2VvIjoiMDEwMDAwIiwidGVsZWZvbm8iOiIwMSA0NDYgOTUgMTgiLCJlbWFpbCI6ImdpYW5jYWZmZUB5YWhvby5jb20iLCJjb2RpZ29fYXNpZ19zdW5hdCI6IjAwMDEifSwicmVjZXB0b3IiOnsidWJpZ2VvIjoiMDEwMTAxIiwiZGlzdHJpdG8iOiItIiwicHJvdmluY2lhIjoiLSIsImRlcGFydGFtZW50byI6Ii0iLCJkaXJlY2Npb24iOiJjbGllbnRzLmFkZHJlc3MiLCJ0aXBvX2NsaWVudGUiOiI2IiwicnVjIjoiMjAxMDAwNDc2NDEiLCJyYXpvbl9zb2NpYWwiOiJQQVBFTEVSQSBOQUNJT05BTCBTIEEiLCJlbWFpbCI6Ii0ifSwiY2FiZWNlcmEiOnsidGlwb19vcGVyYWNpb24iOiIwMTAxIiwiZmVjaGFfZW1pc2lvbiI6IjIwMTktMDItMTIgMTk6MjQ6MDUiLCJ0aXBvX2RvY3VtZW50byI6IjA3Iiwic2VyaWUiOiJGTjAxIiwibnVtZXJvIjoiMSIsInRpcG9fbW9uZWRhIjoiUEVOIiwib3BlcmFjaW9uZXNfZ3JhdmFkYXMiOiIxMDYuNjQiLCJpZ3YiOiIxOS4yMCIsInNlcnZpY2lvIjoiMTAuNjYiLCJpbXBvcnRlX3RvdGFsIjoiMTM2LjUwIiwiZG9jdW1lbnRfcmVsIjoxMDU4NSwidGlwb19kb2N1bWVudG9fYWZlY3RhZG8iOiIwMSIsIm51bWVyb19kb2N1bWVudG9fYWZlY3RhZG8iOiJGQTAzLTE0NzAiLCJjb2RpZ29fbW90aXZvIjoiMDEiLCJkZXNjcmlwY2lvbl9tb3Rpdm8iOiJBTlVMQUNJT04gREUgTEEgT1BFUkFDSU9OIn0sImRldGFsbGUiOlt7InVuaWRhZF9tZWRpZGEiOiJOSVUiLCJjYW50aWRhZCI6IjMuMDAiLCJkZXNjcmlwY2lvbiI6IlNBTiBNQVRFTyBDXC9HIDYwME1MIiwidmFsb3JfdmVudGEiOiIxMi45MCIsImlndl9wZXIiOiIxOC4wMCIsImlndiI6IjIuMzEiLCJ0aXBvX2FmZWN0YWNpb25faWd2IjoiMTAiLCJzZXJ2aWNpb19wZXIiOiIxMC4wMCIsInNlcnZpY2lvIjoiMS4yOSIsInZhbG9yX3VuaXRhcmlvIjoiNC4zMCIsInByZWNpb191bml0YXJpbyI6IjUuNTAifSx7InVuaWRhZF9tZWRpZGEiOiJOSVUiLCJjYW50aWRhZCI6IjQuMDAiLCJkZXNjcmlwY2lvbiI6IlBJWlpBIENVQVRSTyBHVVNUT1MiLCJ2YWxvcl92ZW50YSI6IjkzLjc2IiwiaWd2X3BlciI6IjE4LjAwIiwiaWd2IjoiMTYuODgiLCJ0aXBvX2FmZWN0YWNpb25faWd2IjoiMTAiLCJzZXJ2aWNpb19wZXIiOiIxMC4wMCIsInNlcnZpY2lvIjoiOS4zNiIsInZhbG9yX3VuaXRhcmlvIjoiMjMuNDQiLCJwcmVjaW9fdW5pdGFyaW8iOiIzMC4wMCJ9XX0="
            }');
        $dataB64 = $body->data;
        $data = json_decode(base64_decode($body->data));*/
        list($usuario, $clave) = $db->getUsuarioSol($data->emisor->ruc);

        $see = new See($usuario, $clave, $body["test"] = "true");
        $result["response"] = $see->enviar($data);
        if($result["response"]["success"]){
            $row = [
                "tipo_comprobante" => $data->cabecera->tipo_documento,
                "fecha" => $data->cabecera->fecha_emision,
                "serie" => $data->cabecera->serie,
                "numero" => $data->cabecera->numero,
                "ruc" => $data->receptor->ruc,
                "moneda" => $data->cabecera->tipo_moneda,
                "base" => $data->cabecera->operaciones_gravadas,
                "igv" => $data->cabecera->igv,
                "servicio" => 0,
                "total" => $data->cabecera->importe_total,
                "data" => $dataB64,
                "uuid" => $result["uuid"]
            ];
            if($data->cabecera->servicio > 0){
                $row["servicio"] = $data->cabecera->servicio;
            }
            $db->save($row);
        }

        return $response->withJson($result);
        //print_r($result["message"]);
    });

    $app->post('/baja', function (Request $request, Response $response) {
        $res = array("success" => true, "message" => "", "test" => null);

        $body = $request->getParsedBody();
        $result["test"] = $body["test"];
        $this->logger->info(base64_decode($body["data"]));
        $dataJson = json_decode(base64_decode($body["data"]));
        $data = (object) $dataJson;

        $see = new See($body["usuario"], $body["clave"], $body["test"] = "true");

        $result["message"] = $see->baja($data);

        return $response->withJson($result);
    });

    $app->get("/ver/{token}/{tipo}", function(Request $request, Response $response, array $args){
        $token = $args['token'];
        $tipo = $args['tipo'];
    
        $documento_nombre = "";
        $file = new File();

        if($tipo == "pdf"){
            $data = $file->getPdf($documento_nombre);
            $response = $response->withHeader("Content-type", "application/pdf")
                                ->withHeader("Content-Disposition", "inline; filename='" . $documento_nombre . "'")
                                ->withHeader("Content-Transfer-Encoding", "binary")
                                ->withHeader("Content-Length", strlen($data));
        }else if($tipo == "xml"){
            $data = $file->getXml($documento_nombre);
            $response = $response->withHeader("Content-type", "text/xml")
                                ->withHeader("Content-Disposition", "inline; filename='" . $documento_nombre . "'");

        }else if($tipo == "cdr"){
            $data = $file->getCdr($documento_nombre);
            $response = $response->withHeader("Content-type", "application/zip")
                                ->withHeader("Content-Disposition", "inline; filename='" . $documento_nombre . "'")
                                ->withHeader("Content-Transfer-Encoding", "binary")
                                ->withHeader("Content-Length", strlen($data));
        }

        return $response->write($data);    
    });

});

$app->group("/dogia-server/v1", function(\Slim\App $app){

    $app->get('/', function(Request $request, Response $response, $args){
        return $response->withJson(array("success" => true));
    });

    $app->post('/comprobante', function(Request $request, Response $response, $args) {
        $result = array("success" => true, "response" => null);
        $db = new Db($this->db, $this->logger);
        //
        $body = $request->getParsedBody();
        $dataB64 = $body["data"];
        $data = json_decode(base64_decode($dataB64));
        //
        $doc = isset($data->factura) ? $data->factura : $data->boleta;
        //
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
        //$result["response"] = $db->existe($row["sede"], $row["tipo_comprobante"], $row["serie"], $row["numero"]);
        if(!$db->existe($row["sede"], $row["tipo_comprobante"], $row["serie"], $row["numero"])){
            $id = $db->save($row);
            $result["response"] = array("id" => $id);
        }else{
            $result["response"] = array("id" => 0, "message" => "El comprobante existe");
        }
        //
        return $response->withJson($result);
    });

});