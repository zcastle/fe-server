<?PHP

namespace Sunat;

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

  public function getUsuarioSol($ruc){
    return $this->db->table('cliente')->select("sol_usuario", "sol_clave")->where("ruc", $ruc)->first();
  }

}

?>
