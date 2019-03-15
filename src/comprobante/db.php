<?PHP

namespace Comprobante;

class Db {

  private $db;
  private $logger;

  public function __construct($db, $logger){
    $this->db = $db;
    $this->logger = $logger;
  }

  public function save($row){
    return $this->db->table('comprobante')->insertGetId($row);
  }

  public function existe($sede, $tipoComprobante, $serie, $numero){
    return $this->db->table('comprobante')
        ->where("sede", $sede)
        ->where("tipo_comprobante", $tipoComprobante)
        ->where("serie", $serie)
        ->where("numero", $numero)
        ->count() > 0;
  }
}

?>
