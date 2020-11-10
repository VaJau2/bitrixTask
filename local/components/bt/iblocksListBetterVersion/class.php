<?

use Bitrix\Iblock\ElementTable;
use Bitrix\Main\Context;
use Bitrix\Main\Data\Cache;
use Bitrix\Main\Entity\ExpressionField;
use Bitrix\Main\Entity\ReferenceField;
use Bitrix\Main\Loader;
use Bitrix\Main\ORM;
use Bitrix\Main\UI\PageNavigation;

class IblocksListBetter extends CBitrixComponent
{
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
     * Вытаскиваем запросом к БД максимальный номер страницы
     *
     * @return int
     */
    private function getMaxPage()
    {
        $oCache = Cache::createInstance();
        if ($oCache->initCache(60, "getMaxPage")) {
            return $oCache->getVars();
        } elseif($oCache->startDataCache(60, "getMaxPage")) {

            $maxPage = intval($this->getIblocksCount() / $this->arParams["ELEMENTS_COUNT"]);
            $oCache->endDataCache($maxPage);
            return $maxPage;
        }
        return 0;
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
     * Добавляем кастомное свойство инфоблока для запроса в БД
     *
     * @param $oQuery
     * @param array $aSelect
     * @param array $aPropNames
     *
     * @return array
     * @throws \Bitrix\Main\ArgumentException
     * @throws \Bitrix\Main\SystemException
     */
    public static function selectCustomProp($oQuery, array $aPropNames)
    {
        foreach ($aPropNames as $sProp) {
            $sKey         = "ref_" . $sProp;
            $sKeyProperty = $sProp . "_PROPERTY";

            $oQuery->registerRuntimeField(
                new ReferenceField(
                    $sKeyProperty,
                    \Bitrix\Iblock\PropertyTable::class,
                    ORM\Query\Join::on("this.IBLOCK_ID", "ref.IBLOCK_ID")
                        -> where(
                            "ref.CODE",
                            $sProp
                        )
                )
            )->registerRuntimeField(
                $sKey,
                [
                    "data_type" => "ElementPropertyTable",
                    "reference" => [
                        "=this.ID" => "ref.IBLOCK_ELEMENT_ID",
                        "=this.$sKeyProperty.ID" => "ref.IBLOCK_PROPERTY_ID"
                    ]
                ]
            );
            $aSelect[$sProp] = $sKey. '.VALUE';
        }
        return $aSelect;
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
            $aParams = $oRequest->get('pageNum');
            $APPLICATION->RestartBuffer();

            if (method_exists($this, $functionName)) {
                $aResult = $this->$functionName($aParams);
                echo json_encode($aResult);
                die();
            }
        }
    }


    /**
     * Обрабатываем запрос на показ новой страницы
     *
     * @param int $iNewPage
     *
     * @return string[]
     */
    public function pageAction(int $iNewPage)
    {
        $aPageData = $this->getIblockElements($iNewPage);
        $iMaxPage = $this->getMaxPage();
        $bNeedNewPage = ($iNewPage < $iMaxPage);
        return ["pageData" => $aPageData, "needNewPage" => $bNeedNewPage];
    }


    /**
     * Вытаскиваем массив элементов кастомного свойства инфоблока
     *
     * @param string $iBlockId
     * @param string $sElementId
     * @param string $sPropertyName
     *
     * @return array
     */
    public static function getCustomPropertyArray(string $iBlockId, string $sElementId, string $sPropertyName)
    {
        $oQuery  = ElementTable::query();

        $aSelect = self::selectCustomProp($oQuery, [
            $sPropertyName
        ]);

        $oQuery->setSelect($aSelect);
        $oQuery->setFilter([
            "IBLOCK_ID" => $iBlockId,
            "ID" => $sElementId
        ]);
        $oQuery = $oQuery->exec();
        //убираем лишние поля из свойства
        $aProperties = [];
        do {
            $aTempProperty = $oQuery->fetch();
            if ($aTempProperty != null) {
                $aProperties[] = $aTempProperty[$sPropertyName];
            }
        } while ($aTempProperty != null);

        return $aProperties;
    }

    /**
     * Выводим количество элементов инфоблока
     *
     * @return int
     */
    public function getIblocksCount()
    {
        Loader::includeModule('iblock');
        $iBlockId = self::getIblockId($this->arParams["IBLOCK_CODE"]);

        $iBlockElemensCount = current(ElementTable::getList([
            'select' => [new ExpressionField("COUNT", 'COUNT(%s)', ['id'])],
            'filter' => ["IBLOCK_ID" => $iBlockId]
        ])->fetch());

        return $iBlockElemensCount;
    }

    /**
     * Выводим массив элементов инфоблока
     *
     * @param int $iPage
     *
     * @return array
     */
    public function getIblockElements(int $iPage = 0)
    {
        Loader::includeModule('iblock');
        $iBlockId = self::getIblockId($this->arParams["IBLOCK_CODE"]);
        $iOtherBlockId = self::getIblockId($this->arParams["OTHER_IBLOCK_CODE"]);

        //вытаскиваем элементы первого инфоблока
        $oQuery = ElementTable::getList([
            'select' => ["ID", "NAME", "DATE_CREATE"],
            'filter' => ["IBLOCK_ID" => $iBlockId],
            'limit' => $this->arParams["ELEMENTS_COUNT"],
            "offset" => $iPage * $this->arParams["ELEMENTS_COUNT"]
        ]);

        do {
            $aIblockElement = $oQuery->fetch();

            if ($aIblockElement != null) {
                $aElements = self::getCustomPropertyArray(
                    $iBlockId,
                    $aIblockElement["ID"],
                    "ELEMENTS"
                );
                $aUsers = self::getCustomPropertyArray(
                    $iBlockId,
                    $aIblockElement["ID"],
                    "USERS"
                );

                $aIblockElements[$this->arParams["IBLOCK_CODE"]][$aIblockElement["ID"]] = [
                    "NAME" => $aIblockElement["NAME"],
                    "DATE" => $aIblockElement["DATE_CREATE"]->format("d.m.Y"),
                    "ARRAY_COUNT" => count($aElements + $aUsers),
                    "ELEMENTS" => $aElements,
                    "USERS" => $aUsers
                ];
            }
        } while($aIblockElement != null);

        //вытаскиваем элементы второго инфоблока
        $oQuery2 = ElementTable::getList([
            'select' => ["ID", "NAME"],
            'filter' => ["IBLOCK_ID" => $iOtherBlockId],
        ]);
        do {
            $aBlockData = $oQuery2->fetch();
            if ($aBlockData != null) {
                $aIblockElements[$this->arParams["OTHER_IBLOCK_CODE"]][$aBlockData["ID"]] =
                    $aBlockData["ID"] . ', ' . $aBlockData["NAME"];
            }
        } while ($aBlockData != null);

        //вытаскиваем ФИО юзеров
        $userBy = "id";
        $userOrder = "asc";
        $oUsers = CUser::GetList($userBy, $userOrder);
        do {
            $aUser = $oUsers->Fetch();

            if ($aUser != null) {
                $sUserFio = $aUser["SECOND_NAME"] . ' ' . $aUser["NAME"] . ' ' . $aUser["LAST_NAME"];
                $aIblockElements["USERS"][$aUser["ID"]] = self::getShortFio($sUserFio);
            }
        } while ($aUser != null);


        return $aIblockElements;
    }


    public function executeComponent()
    {
        $this->ajaxProcess();

        $this->arResult["NAV_ENABLED"] = ($this->getMaxPage() > 1);
        $this->arResult['IBLOCKS'] = $this->getIblockElements();

        $this->includeComponentTemplate();
    }
}
