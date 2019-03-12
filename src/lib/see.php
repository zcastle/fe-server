<?PHP

namespace Lib;

use Greenter\Ws\Services\SunatEndpoints;
//
use Greenter\Validator\SymfonyValidator;
use Greenter\Ubl\UblValidator;
//
use Lib\Factura;
use Lib\Nota;
use Lib\Baja;
use Lib\ResumenDiario;
//
use Lib\File;
use Lib\SeeUtil;
use Lib\Pdf;

class See {

    //const EMISOR_RUC = "20511045526";
    //const TIPO_DOCUMENTO_FACTURA = 6; // RUC
    //const TIPO_DOCUMENTO_BOLETA = 1; // DNI
    //
    const FACTURA = '01';
    const BOLETA = '03';
	const NOTA_CREDITO = '07';
    const GUIA_REMISION = '09';
    //
	const BAJA = "RA";
	//
	//const UNIDAD_MEDIDA = 'NIU';
	//const UNIDAD_MEDIDA_SERVICIO = 'ZZ';
	//
	//const IGV = 18.00;
	//
	const GRAVADO = 10; // "Gravado - Operación Onerosa"
	const PREMIO = 11; // "Gravado – Retiro por premio"
	const DONACION = 12; // "Gravado – Retiro por donación"
	const GRATUITO = 21; // "Exonerado – Transferencia Gratuita",
    const GRATIS = See::DONACION;
    //
    const MONEDA_SOLES = "Soles ";
	const MONEDA_DOLARES = "Dolares Americanos ";
    //
    //const RUTA_XML = __DIR__ . "/../xml/";
    //
    private $see;

    public function __construct($usuario = "", $clave = "", $test = true) {
        $this->see = new \Greenter\See();

        // TEST
        //if($usuario == "" || $clave == "" || $test){
            $this->see->setService(SunatEndpoints::FE_BETA);
            $this->see->setCertificate(file_get_contents(__DIR__ . "/certificate.pem"));
            $this->see->setCredentials('20000000001MODDATOS', 'moddatos');
        //}
        
        // PRODUCCION
        //if($usuario != "" && $clave != "" && !$test){
            //$this->see->setService(SunatEndpoints::FE_PRODUCCION);
            /*$this->see->setCertificate(file_get_contents(__DIR__ . "/dogia2019.pem"));
            $this->see->setCredentials('20511045526FE082018', 'SOLARIS00');*/

            /*$this->see->setCertificate(file_get_contents(__DIR__ . "/../../../fe-certificados/$usuario/$usuario.pem"));
            $this->see->setCredentials($usuario, $clave);*/
            
        //}
        //
        //$this->see->setService("https://www.escondatagate.net/wsValidator_2_1/ol-ti-itcpe/billService");
        //$this->see->setCertificate(file_get_contents(__DIR__ . "/dogia2019.pem"));
        //$this->see->setCredentials('dogia02', 'Dogia2018*');
        //$this->see->setCredentials('FE082018', 'SOLARIS00');
    }

    public function enviar($data){
        $response = array("code" => 0, "descripcion" => null);
        $tipo_documento = $data->cabecera->tipo_documento;
        if($tipo_documento == See::FACTURA || $tipo_documento == See::BOLETA){
            $builder = new Factura();
        } else if($tipo_documento == See::NOTA_CREDITO){
            $builder = new Nota();
        }

        $document = $builder->build($data);
        //
        $validator = new SymfonyValidator();
        $errors = $validator->validate($document);
        $response["validate_document"] = $errors;
        $response["validate_document_count"] = $errors->count();
        //
        $file = new File($document->getCompany()->getRuc());
        $xmlSigned = $this->see->getXmlSigned($document);
        //
        $validator = new UblValidator();
        if ($validator->isValid($xmlSigned)) {
            $response["validate_xml"] = "success";
            $file->writeXml($document->getName(), $xmlSigned);
            $response["hash"] = (new \Greenter\Report\XmlUtils())->getHashSign($xmlSigned);
            try{
                $pdf = new Pdf();
                $data = $pdf->get($document);
                $file->writePdf($document->getName(), $data);
            } catch (Exception $e) {
                $response["error_pdf"] = $e->getMessage();
            }
            
            $result = $this->see->send($document);
            
            $response["success"] = $result->isSuccess();
            if ($response["success"]) {
                $file->writeCdr($document->getName(), $result->getCdrZip());
                $cdr = $result->getCdrResponse();
                $response["code"] = $cdr->getCode();
                $response["descripcion"] = $cdr->getDescription();
                $response["notas"] = $cdr->getNotes();
            } else {
                $error = $result->getError();
                $response["code"] = $error->getCode();
                $response["descripcion"] = $error->getMessage();
            }
        } else {
            $response["validate_xml"] = $validator->getError();
        }
        //
        return $response;
    }

    public function baja($data){
        $builder = new Baja();
        $document = $builder->build($data);

        $file = new File($document->getCompany()->getRuc());
        $file->writeXml($document->getName(), $this->see->getXmlSigned($document));
        
        $result = $this->see->send($document);

        if ($result->isSuccess()) {
            $status = $this->see->getStatus($result->getTicket());
            if ($status->isSuccess()) {
                $file->writeCdr($document->getName(), $status->getCdrZip());
                $cdr = $status->getCdrResponse();
                return array("code" => $cdr->getCode(), "descripcion" => $cdr->getDescription());
            } else {
                $error = $status->getError();
                return array("code" => $error->getCode(), "descripcion" => $error->getMessage());
            }
        } else {
            $error = $result->getError();
            return array("code" => $error->getCode(), "descripcion" => $error->getMessage());
        }
    }

    public function enviar_resumen_boletas($data, $fechaHora){
        $builder = new ResumenDiario();

        $resumen = $builder->build($data, $fechaHora);
        $resumen->setCompany($this->getCompany());

        $result = $this->see->send($resumen);
        $this->writeXml($resumen, $this->see->getFactory()->getLastXml());

        if ($result->isSuccess()) {
            $ticket = $result->getTicket();
            $status = $this->see->getStatus($ticket);
            if ($status->isSuccess()) {
                $this->writeCdr($resumen, $status->getCdrZip());
                $cdrResponse = $status->getCdrResponse();
                return array("code" => $cdrResponse->getCode(), "descripcion" => $cdrResponse->getDescription(), "ticket" => $ticket, "nombre" => $resumen->getName());
            } else {
                $error = $status->getError();
                return array("code" => $error->getCode(), "descripcion" => $error->getMessage(), "place" => "status");
            }
        } else {
            $error = $result->getError();
            return array("code" => $error->getCode(), "descripcion" => $error->getMessage(), "place" => "send");
        }
    }

}