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

        <section class="section m-3">

            <div class="card w-75">
                <div class="card-body">
                    <form class="px-4 py-3">
                        <h5 class="card-title">
                            <label for="adressText">Геолокация</label>
                        </h5>

                        <div class="input-group mb-3">
                            <input type="text" class="form-control" id="adressText" placeholder="Введите адрес...">
                            <div class="input-group-append">
                                <button class="btn btn-outline-secondary" type="button" id="geolocationSend">
                                    Найти координаты
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="collapse" id="coordsResult">
                    <div class="dropdown-divider"></div>
                    <div class="card-body">
                        <p class="card-text">Найденные координаты:</p>
                        <div class="form-group row">
                            <label for="resultLat" class="col-sm-2 col-form-label">Долгота</label>
                            <div class="col-sm-10">
                                <input type="text" readonly class="form-control-plaintext" id="resultLat" value="42">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="resultLong" class="col-sm-2 col-form-label">Широта</label>
                            <div class="col-sm-10">
                                <input type="text" readonly class="form-control-plaintext" id="resultLong" value="42">
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </section>

<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>