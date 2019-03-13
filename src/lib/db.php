<?PHP

namespace Lib;

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
