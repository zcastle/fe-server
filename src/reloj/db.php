<?PHP

namespace Reloj;

class Db {

  private $db;
  private $logger;

  public function __construct($db, $logger){
    $this->db = $db;
    $this->logger = $logger;
  }

  public function insertarRegistro($row){
    $this->db->table('marcacion')->insert([
      "codigo" => $row->codigo,
      "reloj_serie" => $row->reloj_serie,
      "estado" => $row->estado,
      "fecha_hora" => $row->fecha_hora
    ]);
  }
}

?>
