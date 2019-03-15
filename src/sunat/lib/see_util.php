<?PHP

namespace Sunat\Lib;

use Greenter\XMLSecLibs\Certificate\X509Certificate;
use Greenter\XMLSecLibs\Certificate\X509ContentType;

class SeeUtil {

    public function convert() {
        $pfx = file_get_contents(__DIR__ . '/20511045526.pfx');
        $password = 'SOLARIS00++';
    
        $certificate = new X509Certificate($pfx, $password);
        $pem = $certificate->export(X509ContentType::PEM);
            
        file_put_contents(__DIR__ . '/certificate.pem', $pem);
    
        return array("success" => true);
    }

}
