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
    //return $this->renderer->render($response, 'index.phtml', $args);
    //print_r($this->allRoutes);
    return $this->router->routes;
});

// Recibe un comprobante en formato JSON enviado por POST y lo envia a la SUNAT y devuelve un token para consultarlo
$app->any('/sunat/v1/enviar/', \Sunat\Action::class . ':enviar');
// Recibe una baja en formato JSON enviado por POST y lo envia a la SUNAT y devuelve un token para consultarlo
$app->any('/sunat/v1/baja/', \Sunat\Action::class . ':baja');
// Recibe como parametro el token del coprobante y el tipo de documento que se desea consultar [xml, pdf, cdr]
$app->any('/sunat/v1/consultar/{token}/{tipo}', \Sunat\Action::class . ':consultar');

// Recibe los comprobantes y los almacen ane Titan
$app->any('/comprobantes/v1/', \Comprobantes\Action::class);

// Recibe las marcaciones y las registra en Titan
$app->any('/reloj/v1/', \Reloj\Action::class);