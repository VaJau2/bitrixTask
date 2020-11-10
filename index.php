<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");

/**
 * @global $APPLICATION
 */

$APPLICATION->SetPageProperty("title", "Сайт тута");
$APPLICATION->SetPageProperty("NOT_SHOW_NAV_CHAIN", "Y");
$APPLICATION->SetTitle("Главная страница");
?>
    Тестовая страница, выводящая компоненты - форму поиска и список элементов инфоблока<br>

<?
$APPLICATION->IncludeComponent("bt:geocodeSearch", ".default");
?>
<span class="m-3"></span>
<?
$APPLICATION->IncludeComponent("bt:iblocksListBetterVersion",".default", [
    "ELEMENTS_COUNT" => "3",
    "CACHE_ENABLED" => "Y",
    "LIST_CACHE_TIME" => "7200",
    "IBLOCK_CODE" => "i1",
    "OTHER_IBLOCK_CODE" => "i2"
]);
?>

<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>
