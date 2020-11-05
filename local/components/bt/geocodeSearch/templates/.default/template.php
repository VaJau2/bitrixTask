<? if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) {
    die();
}
?>

<section class="section m-3">
    <div class="card w-75">
        <div class="card-body">
            <form class="px-4 py-3">
                <h5 class="card-title">
                    <label for="adressText"><?=GetMessage("GEOCODE_FORM_NAME")?></label>
                </h5>

                <div class="input-group mb-3">
                    <input type="text" class="form-control" id="adressText"
                           placeholder="<?=GetMessage("GEOCODE_INPUT_ADDRESS")?>" required>
                    <div class="input-group-append">
                        <button class="btn btn-outline-secondary" type="button" id="geolocationSend">
                            <?=GetMessage("GEOCODE_BUTTON_TEXT")?>
                        </button>
                    </div>
                </div>
            </form>

        </div>
        <div class="collapse" id="coordsResult">
            <div class="dropdown-divider"></div>
            <div class="card-body">
                <p class="card-text"><?=GetMessage("GEOCODE_DROPDOWN_HEADER")?></p>
                <div class="form-group row">
                    <label for="resultLat" class="col-sm-2 col-form-label">
                        <?=GetMessage("GEOCODE_LATTITUDE")?>
                    </label>
                    <div class="col-sm-10">
                        <input type="text" readonly class="form-control-plaintext" id="resultLat" value="">
                    </div>
                </div>
                <div class="form-group row">
                    <label for="resultLong" class="col-sm-2 col-form-label">
                        <?=GetMessage("GEOCODE_LONGTITUE")?>
                    </label>
                    <div class="col-sm-10">
                        <input type="text" readonly class="form-control-plaintext" id="resultLong" value="">
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>