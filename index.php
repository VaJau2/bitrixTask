<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");

/**
 * @global $APPLICATION
 */

$APPLICATION->SetPageProperty("title", "Сайт тута");
$APPLICATION->SetPageProperty("NOT_SHOW_NAV_CHAIN", "Y");
$APPLICATION->SetTitle("Главная страница");
?>
<!--
TODO: 1) вытащить ссылку на bootstrap в BootstrapCDN и удалить assets
      2) запрогать отправку запроса на геокодер и глянуть, что выходит
      3) запрогать ответ на запрос
      4) сделать второе задание
-->
    Тестовая страница, выводящая компоненты - форму поиска и список элементов инфоблока<br>

<?
$APPLICATION->IncludeComponent("bt:geocodeSearch", ".default");
?>

<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>