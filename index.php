<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");

/**
 * @global $APPLICATION
 */

$APPLICATION->SetPageProperty("title", "Демонстрационная версия продукта «1С-Битрикс: Управление сайтом»");
$APPLICATION->SetPageProperty("NOT_SHOW_NAV_CHAIN", "Y");
$APPLICATION->SetTitle("Главная страница");
?>

    Тестовая страница, выводящая компоненты - форму поиска и список элементов инфоблока<br>

<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>