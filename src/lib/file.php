<?PHP

namespace Lib;

class File {

    const MAIN_PATH = "/var/www/html/fe-archivos/";
    public $path = array();

    public function __construct($ruc) {
        $tmpPath = File::MAIN_PATH . $ruc;
        if(!file_exists($tmpPath)){
            mkdir($tmpPath, 0777, true);
        }
        foreach (array("XML", "CDR", "PDF") AS $valor) {
            $folder = $tmpPath . "/" . $valor;
            if(!file_exists($folder)){
                mkdir($folder, 0777);
            }
            $this->path[$valor] = $folder . "/";
        }
    }

    public function getXml($name){
        return file_get_contents($this->path["XML"] . $name . '.xml');
    }

    public function writeXml($name, $data){
        file_put_contents($this->path["XML"] . $name . '.xml', $data);
    }

    public function getCdr($name){
        return file_get_contents($this->path["CDR"] . "R-" . $name . '.zip');
    }

    public function writeCdr($name, $data){
        file_put_contents($this->path["CDR"] . "R-" . $name . '.zip', $data);
    }

    public function getPdf($name){
        return file_get_contents($this->path["PDF"] . $name . '.pdf');
    }

    public function writePdf($name, $data){
        file_put_contents($this->path["PDF"] . $name . '.pdf', $data);
    }

}
