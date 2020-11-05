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
     */
    public function searchAction($sName)
    {
        $aResult = ["status" => "success", "data" => $sName];
        return $aResult;
    }

    public function executeComponent()
    {
        $this->ajaxProcess();
        $this->includeComponentTemplate();
    }
};