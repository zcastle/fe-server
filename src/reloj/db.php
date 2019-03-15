<?PHP

namespace Reloj;

class Db {

  private $db;
  private $logger;

  public function __construct($db, $logger){
    $this->db = $db;
    $this->logger = $logger;
  }

  public function insertarRegistro($codigo, $reloj_serie, $fecha_hora){
    $this->db->table('marcacion')->insert([
      "codigo" => $codigo,
      "reloj_serie" => $reloj_serie,
      "fecha_hora" => $fecha_hora
    ]);
  }
}

?>
