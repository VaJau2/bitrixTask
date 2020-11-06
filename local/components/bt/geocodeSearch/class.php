<?php

use Bitrix\Main\Context;
use Bitrix\Main\Engine\Contract\Controllerable;

/**
 * Класс отправляет запрос на геокодер и возвращает отттуда найденные координаты
 *
 * Class CDemoSqr
 *
 * @package BitrixTasks
 */

class GeocodeSearch extends CBitrixComponent
{


    public function __call(string $name, array $arguments)
    {
        echo "error: function $name does not exist";
        die();
    }

    /**
     * Обрабатываем ajax-запросы
     *
     * @return void
     */
    public function ajaxProcess()
    {
        $oRequest = Context::getCurrent()->getRequest();
        global $APPLICATION;

        if($oRequest->isAjaxRequest()) {
            $functionName = $oRequest->get('action') . 'Action';
            $aParams = $oRequest->get('name');
            $APPLICATION->RestartBuffer();

            $aResult = $this->$functionName($aParams);
            echo json_encode($aResult);
            die();
        }
    }

    /**
     * Обрабатываем запросы на геокодирование
     *
     * @param string $sName
     *
     * @return array
     * @throws SoapFault
     */
    public function searchAction($sName)
    {
        $aParams = explode(',', $sName);

        $transport = new SoapTransport();
        $aResponde = $transport->GeocodeAddressNonParsed([
            "streetAddress" => $aParams[0],
            "city" => $aParams[1],
            "state" => $aParams[2],
            "zip" => $aParams[3],
            "apiKey" => "demo",
            "version" => 4.01,
            "shouldCalculateCensus" => true,
            "censusYear" => "AllAvailable",
            "shouldReturnReferenceGeometry" => false,
            "shouldNotStoreTransactionDetails" => true,
        ]);
        $aResponde = current($aResponde)->WebServiceGeocodeQueryResults->WebServiceGeocodeQueryResult;
        $fLat = $aResponde->Latitude;
        $fLong = $aResponde->Longitude;
//        $transport->callSoap("GeocodeAddressNonParsed", [
//            "streetAddress" => "9355 Burton Way",
//            "city" => "Beverly Hills",
//            "state" => "ca",
//            "zip" => "90210",
//            "apiKey" => "demo",
//            "version" => 4.01,
//            "shouldCalculateCensus" => true,
//            "censusYear" => "AllAvailable",
//            "shouldReturnReferenceGeometry" => false,
//            "shouldNotStoreTransactionDetails" => true,
//            ]);

        if ($fLat == null || $fLong == null) {
            $aResult = ["status" => "error"]; //выдает "город не найден"
        } else {
            $aResult = ["status" => "success", "latitude" => $fLat, 'longitude' => $fLong];
        }

        return $aResult;
    }

    public function executeComponent()
    {
        $this->ajaxProcess();
        $this->includeComponentTemplate();
    }
};

/**
 * Class SoapTransport
 *
 * @method GeocodeAddressNonParsed(array $aParams)
 */
class SoapTransport
{
    private $localClient = null;
    private $wsdl = "https://geoservices.tamu.edu/Services/Geocode/WebService/GeocoderService_V04_01.asmx?WSDL";

    public function getClient() {
        if ($this->localClient == null) {
            $this->localClient = new \SoapClient($this->wsdl, ['soap_version' => SOAP_1_2]);
        }
        return $this->localClient;
    }

    public function __call($name, $params) {
        return $this->getClient()->__soapCall($name, $params);
    }

//    public function callSoap($functionName, $aParams) {
//        $responde = $this->getClient()->__soapCall($functionName, $aParams);
//        var_dump($responde);
//    }
}