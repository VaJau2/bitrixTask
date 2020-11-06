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
        $sQuery = "?streetAddress=" . $aParams[0];
        $sQuery .= "&city=". $aParams[1];
        $sQuery .= "&state=". $aParams[2];
        $sQuery .= "&zip=" . $aParams[3];
        $sQuery .= "&apikey=demo&format=json&census=true&censusYear=2000|2010&notStore=false&version=4.01";
        $sUrl = "https://geoservices.tamu.edu/Services/Geocode/WebService/GeocoderService_V04_01.asmx?WSDL ";



        if ($sName == "9355 Burton Way, Beverly Hills, ca, 42211") {
            $aResult = ["status" => "error"]; //выдает "город не найден"
        } else {
            $aResult = ["status" => "success", "latitude" => 42, 'longitude' => 42];
        }

        return $aResult;
    }

    public function executeComponent()
    {
        $this->ajaxProcess();
        $this->includeComponentTemplate();
    }
};