<?php

use Bitrix\Main\Context;
use Bitrix\Main\Data\Cache;

/**
 * Класс отправляет запрос на геокодер и возвращает отттуда найденные координаты
 *
 * Class CDemoSqr
 *
 * @package BitrixTasks
 */

class GeocodeSearch extends CBitrixComponent
{
    const CACHE_TIME = 7200;

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
    public function searchAction(string $sName)
    {
        $oCache = Cache::createInstance();
        if ($oCache->initCache(self::CACHE_TIME, $sName)) {
            return $oCache->getVars();
        } elseif($oCache->startDataCache(self::CACHE_TIME, $sName)) {
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

            if ($fLat == null || $fLong == null) {
                $aResult = ["status" => "error"]; //выдает "город не найден"
            } else {
                $aResult = ["status" => "success", "latitude" => $fLat, 'longitude' => $fLong];
            }

            $oCache->endDataCache($aResult);
            return $aResult;
        }
        return ["error in searchAction"];
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
}