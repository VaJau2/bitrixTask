<?
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) {
    die();
}

/**
 * @var array $arParams
 * @var array $arResult
 */

$iCode1 = $arParams["IBLOCK_CODE"];
$iCode2 = $arParams["OTHER_IBLOCK_CODE"];

?>

<section class="section m-4 js-table-container">
    <table class="table table-bordered">
        <thead>
        <tr>
            <th scope="col">Название</th>
            <th scope="col">Дата</th>
            <th scope="col">ФИО пользователей</th>
            <th scope="col">ID и названия элементов</th>
        </tr>
        </thead>
        <tbody class="js-table-user-body">
        <? foreach($arResult["IBLOCKS"][$iCode1] as $iBlockData) : ?>
            <tr>
                <th scope="row" rowspan="<?=$iBlockData["ARRAY_COUNT"]?>"><?=$iBlockData["NAME"]?></th>
                <td rowspan="<?=$iBlockData["ARRAY_COUNT"]?>"><?=$iBlockData["DATE"]?></td>
                <td><?=$arResult["IBLOCKS"]["USERS"][current($iBlockData["USERS"])]?></td>
                <td><?=$arResult["IBLOCKS"]["$iCode2"][current($iBlockData["ELEMENTS"])]?></td>
            </tr>
            <? for($i = 1; $i < $iBlockData["ARRAY_COUNT"]; $i++) : ?>
                <tr>
                    <td>
                        <? if (array_key_exists($i, $iBlockData["USERS"])) : ?>
                            <?=$arResult["IBLOCKS"]["USERS"][$iBlockData["USERS"][$i]]?>
                        <? endif; ?>
                    </td>

                    <td>
                        <? if (array_key_exists($i, $iBlockData["ELEMENTS"])) : ?>
                            <?=$arResult["IBLOCKS"][$iCode2][$iBlockData["ELEMENTS"][$i]]?>
                        <? endif; ?>
                    </td>
                </tr>
            <? endfor; ?>
        <? endforeach; ?>

        </tbody>
    </table>

    <?if ($arResult['NAV']):?>
        <?=$arResult['NAV']?>
    <?endif?>
</section>
