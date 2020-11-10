<?

use Bitrix\Iblock\ElementTable;
use Bitrix\Main\Context;
use Bitrix\Main\Data\Cache;
use Bitrix\Main\Entity\ExpressionField;
use Bitrix\Main\Loader;
use Bitrix\Main\UI\PageNavigation;

class IblocksList extends CBitrixComponent
{
    private $oNavigation;

    /**
     * Get IblockID by code (взято из Helper.php из прошлого проекта с:)
     *
     * @param $sCode
     *
     * @return int
     *
     * @throws \Bitrix\Main\LoaderException
     */
    public static function getIblockId($sCode = '')
    {
        Loader::includeModule("iblock");
        $iID    = null;
        $oCache = Cache::createInstance();
        if ($oCache->initCache(86400, 'iblock_id_g'.$sCode, '/')) {
            $iID = $oCache->getVars();
        } elseif ($oCache->startDataCache()) {
            $res = \CIBlock::GetList([], ['CODE' => $sCode, 'CHECK_PERMISSIONS' => 'N'], false);
            $ob  = $res->GetNext();
            if ($ob) {
                $iID = (int) $ob['ID'];
            }

            $oCache->endDataCache($iID);
        }

        return (int) $iID;
    }


    /**
     * Сокращаем "Иванов Иван Иванович" до "Иванов И.И."
     *
     * @param string $sFio
     *
     * @return string
     */
    public static function getShortFio(string $sFio)
    {
        $sPattern = "/(?<=[\s][А-Я])[а-я]+/u";
        return preg_replace($sPattern, ".", $sFio);
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

            if (method_exists($this, $functionName)) {
                $APPLICATION->RestartBuffer();
                $aResult = $this->$functionName();
                echo json_encode($aResult);
                die();
            }
        }
    }


    /**
     * Обрабатываем запрос на показ новой страницы
     *
     * @param int $sPage
     *
     * @return void
     */
    public function pageAction() {
        $this->includeComponentTemplate();
    }

    /**
     * Вытаскиваем массив элементов выбранных кастомных свойств инфоблока
     *
     * @param int $iBlockId
     * @param array $aProps
     *
     * @return array
     */
    public static function getCustomProperties(int $iBlockId, array $aProps)
    {
        $aTempResult = [];
        $aResult = [];
        CIBlockElement::GetPropertyValuesArray($aTempResult, $iBlockId, ['PROPERTIES' => $aProps]);
        //если массив пуст, оно вытаскивает NULL или false, а должно вытаскивать []
        foreach($aTempResult as $iId => $aTempProperty) {
            $aResult[$iId] = [
                "USERS" => $aTempProperty["USERS"]["VALUE"] ?: [],
                "ELEMENTS" => $aTempProperty["ELEMENTS"]["VALUE"] ?: []
            ];
        }
        return $aResult;
    }


    /**
     * Выводим количество элементов инфоблока
     *
     * @return int
     */
    public function getIblocksCount()
    {
        $oCache = Cache::createInstance();
        if ($oCache->initCache(7200, "iblocksCount")) {
            return $oCache->getVars();
        } elseif ($oCache->startDataCache()) {
            Loader::includeModule('iblock');
            $iBlockId = self::getIblockId($this->arParams["IBLOCK_CODE"]);

            $iBlockElemensCount = current(ElementTable::getList([
                'select' => [new ExpressionField("COUNT", 'COUNT(%s)', ['id'])],
                'filter' => ["IBLOCK_ID" => $iBlockId]
            ])->fetch());

            $oCache->endDataCache($iBlockElemensCount);
            return $iBlockElemensCount;
        }
        return 0;
    }


    /**
     * Выводим массив элементов инфоблока
     *
     * @return array
     */
    public function getIblockElements()
    {
        Loader::includeModule('iblock');
        $iBlockId = self::getIblockId($this->arParams["IBLOCK_CODE"]);
        $iOtherBlockId = self::getIblockId($this->arParams["OTHER_IBLOCK_CODE"]);

        //вытаскиваем элементы первого инфоблока
        $oQuery = ElementTable::getList([
            'select' => ["ID", "NAME", "DATE_CREATE"],
            'filter' => ["IBLOCK_ID" => $iBlockId],
            'limit' => $this->oNavigation->getLimit(),
            "offset" => $this->oNavigation->getOffset()
        ]);
        $aCustomProps = self::getCustomProperties($iBlockId, ["ELEMENTS", "USERS"]);
        $aIblockElements = [];

        while($aIblockElement = $oQuery->fetch()) {
            $aElements = $aCustomProps[$aIblockElement["ID"]]["ELEMENTS"];
            $aUsers = $aCustomProps[$aIblockElement["ID"]]["USERS"];

            $aIblockElements[$this->arParams["IBLOCK_CODE"]][$aIblockElement["ID"]] = [
                "NAME" => $aIblockElement["NAME"],
                "DATE" => $aIblockElement["DATE_CREATE"]->format("d.m.Y"),
                "ARRAY_COUNT" => count($aElements + $aUsers),
                "ELEMENTS" => $aElements,
                "USERS" => $aUsers
            ];
        }

        //вытаскиваем элементы второго инфоблока
        $oQuery2 = ElementTable::getList([
            'select' => ["ID", "NAME"],
            'filter' => ["IBLOCK_ID" => $iOtherBlockId],
        ]);
        while($aBlockData = $oQuery2->fetch()) {
            if ($aBlockData != null) {
                $aIblockElements[$this->arParams["OTHER_IBLOCK_CODE"]][$aBlockData["ID"]] =
                    $aBlockData["ID"] . ', ' . $aBlockData["NAME"];
            }
        }

        //вытаскиваем ФИО юзеров
        $userBy = "id";
        $userOrder = "asc";
        $oUsers = CUser::GetList($userBy, $userOrder);
        while($aUser = $oUsers->Fetch()) {
            if ($aUser != null) {
                $sUserFio = $aUser["SECOND_NAME"] . ' ' . $aUser["NAME"] . ' ' . $aUser["LAST_NAME"];
                $aIblockElements["USERS"][$aUser["ID"]] = self::getShortFio($sUserFio);
            }
        }

        return $aIblockElements;
    }


    /**
     * Собираем шаблон для постраничной навигации
     *
     * @return string
     */
    public function getNavString() {

        global $APPLICATION;
        ob_start();
        $APPLICATION->IncludeComponent(
            "bitrix:main.pagenavigation",
            "onebutton",
            array(
                "NAV_OBJECT" => $this->oNavigation,
                "SEF_MODE" => "N",
            ),
            false
        );

        $result = ob_get_contents();
        ob_end_clean();
        return $result;
    }


    public function executeComponent()
    {
        $this->oNavigation = new PageNavigation('page');
        $this->oNavigation->setRecordCount($this->getIblocksCount());

        $this->oNavigation->setPageSize($this->arParams["ELEMENTS_COUNT"]);

        $this->oNavigation->initFromUri();

        $this->arResult['NAV'] = $this->getNavString();

        $this->arResult['IBLOCKS'] = $this->getIblockElements();

        $this->ajaxProcess();

        $this->includeComponentTemplate();
    }
}



