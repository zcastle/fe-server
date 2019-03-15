<?php

namespace Reloj;

use Psr\Container\ContainerInterface;
//
use Reloj\Db;

class Action {

   protected $container;

   public function __construct(ContainerInterface $container) {
       $this->container = $container;
   }

   public function __invoke($request, $response, $args) {
        $result = array("success" => true, "error" => false, "response" => "");
        if($request->isPost()){
            $body = $request->getParsedBody();
            $rows = json_decode(base64_decode($body["data"]));

            $db = new Db($this->container->get("db"), $this->container->get("logger"));
            foreach($rows AS $row){
                $db->insertarRegistro($row->codigo, $row->reloj_serie, $row->fecha_hora);
            }
        }else{
            $result["error"] = true;
            $result["response"] = $request->getUri() . " solo permite el metodo POST";
        }
        
        return $response->withJson($result);
   }
}

?>