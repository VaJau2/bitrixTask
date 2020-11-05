<? if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) {
    die();
}

/**
 * @global $APPLICATION
 */
?>

<!doctype html>
<html lang="en">
<head>
    <title><?$APPLICATION->ShowTitle()?></title>
    <?$APPLICATION->ShowHead();?>

    <?
    use Bitrix\Main\Page\Asset;

    Asset::getInstance()->addString("<meta charset='utf-8'>");
    Asset::getInstance()->addString(
            '<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">'
    );
    Asset::getInstance()->addJs(SITE_TEMPLATE_PATH . "/script.js");
    Asset::getInstance()->addCss(SITE_TEMPLATE_PATH . "/style.css");
    //JQuery
    Asset::getInstance()->addJs("https://code.jquery.com/jquery-3.5.1.min.js");
    //Bootstrap core CSS
    Asset::getInstance()->addCss("https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css");

    ?>
</head>
<body>
<div id="panel"><?$APPLICATION->ShowPanel()?></div>
<nav class="navbar navbar-expand-md navbar-dark bg-dark fixed-top">
    <span class="navbar-brand">Задания по битриксу </span>
    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarsExampleDefault"
            aria-controls="navbarsExampleDefault" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
    </button>

    <div class="collapse navbar-collapse" id="navbarsExampleDefault">
        <ul class="navbar-nav mr-auto">
            <li class="nav-item">
                <a class="nav-link" href="#">Link</a>
            </li>
            <li class="nav-item">
                <a class="nav-link disabled" href="#" tabindex="-1" aria-disabled="true">Disabled</a>
            </li>
            <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle" href="#" id="dropdown01" data-toggle="dropdown"
                   aria-haspopup="true" aria-expanded="false">Dropdown</a>
                <div class="dropdown-menu" aria-labelledby="dropdown01">
                    <a class="dropdown-item" href="#">Action</a>
                    <a class="dropdown-item" href="#">Another action</a>
                    <a class="dropdown-item" href="#">Something else here</a>
                </div>
            </li>
        </ul>
        <form class="form-inline my-2 my-lg-0">
            <input class="form-control mr-sm-2" type="text" placeholder="Search" aria-label="Search">
            <button class="btn btn-secondary my-2 my-sm-0" type="submit">Search</button>
        </form>
    </div>
</nav>
<main role="main" class="container">