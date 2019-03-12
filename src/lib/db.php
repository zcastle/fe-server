<?PHP

namespace Lib;

class Db {

  private $db;
  private $logger;

  public function __construct($db, $logger){
    $this->db = $db;
    $this->logger = $logger;
  }

  public function save($data, $dataB64, $uuid){
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
      "uuid" => $uuid
    ];
    if($data->cabecera->servicio > 0){
      $row["servicio"] = $data->cabecera->servicio;
    }
    $this->db->table('comprobante')->insert($row);
  }

  public function getUsuarioSol($ruc){
    return $this->db->table('cliente')->select("sol_usuario", "sol_clave")->where("ruc", $ruc)->first();
  }

}

?>
