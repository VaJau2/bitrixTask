<?

use Bitrix\Iblock\IblockTable;
use Bitrix\Iblock\PropertyTable;
use Bitrix\Iblock\TypeTable;
use Bitrix\Main\Loader;

function executeMigration() {
    Loader::includeModule('iblock');

    TypeTable::add([
        "ID" => "blocks",
        "NAME" => "инфоблоки"
    ]);

    IblockTable::add([
        "ID" => "1",
        "LID" => "s1",
        "CODE" => "i1",
        "IBLOCK_TYPE_ID" => "blocks2",
        "NAME" => "инфоблок 1",
    ]);

    PropertyTable::add([
        "ID" => "1",
        "CODE" => "USERS",
        "IBLOCK_ID" => "1",
        "NAME" => "пользователи",
        "PROPERTY_TYPE" => "S",
        "USER_TYPE" => "UserID"
    ]);

    PropertyTable::add([
        "ID" => "2",
        "CODE" => "ELEMENTS",
        "IBLOCK_ID" => "1",
        "NAME" => "элементы",
        "PROPERTY_TYPE" => "E",
    ]);

    IblockTable::add([
        "ID" => "2",
        "LID" => "s1",
        "CODE" => "i2",
        "IBLOCK_TYPE_ID" => "blocks2",
        "NAME" => "инфоблок 2",
    ]);

    echo "migrate executed successfully";
}

//executeMigration();