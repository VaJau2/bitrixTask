<?
use Bitrix\Iblock\IblockTable;
use Bitrix\Main\Loader;

Loader::includeModule('iblock');
$aiBlocks = IblockTable::getList()->fetchAll();
foreach ($aiBlocks as $oiBlock) {
    $arIBlockCodes[] = $oiBlock["CODE"];
}

$arComponentParameters = [
    "PARAMETERS" => [
        "ELEMENTS_COUNT" => [
            "NAME"=>GetMessage("LIST_ELEMENTS_COUNT"),
            "TYPE" => "TEXT",
            "DEFAULT"=>'3',
        ],

        "CACHE_ENABLED" => [
            "NAME"=>GetMessage("CACHE_ENABLED"),
            "TYPE" => "CHECKBOX",
            "DEFAULT"=>'Y',
        ],

        "LIST_CACHE_TIME" => [
            "NAME"=>GetMessage("LIST_CACHE_TIME"),
            "TYPE" => "TEXT",
            "DEFAULT"=>'7200',
        ],

        "IBLOCK_CODE" => [
            "NAME"=>GetMessage("LIST_IBLOCK_CODE"),
            "TYPE" => "LIST",
            "VALUES" => $arIBlockCodes,
        ],

        "OTHER_IBLOCK_CODE" => [
            "NAME"=>GetMessage("LIST_IBLOCK_CODE"),
            "TYPE" => "LIST",
            "VALUES" => $arIBlockCodes,
        ],
    ]
];