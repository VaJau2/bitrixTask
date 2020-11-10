<? if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) {
    die();
}

/**
 * @var array $arResult
 */
if ($arResult['PAGE_COUNT'] == $arResult['CURRENT_PAGE']) {
    return;
}
?>
<button id="moreButton" class="btn btn-outline-primary js-show-more" data-page="<?=$arResult["CURRENT_PAGE"]+1?>">
    Ещё
</button>