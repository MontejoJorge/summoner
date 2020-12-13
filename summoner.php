<?php
require_once "./models/Summoner.php";
require_once "./models/League.php";
require_once "./API_keys.php";

function get_http_response_code($domain1){
    $headers = get_headers($domain1);
    return substr($headers[0], 9, 3);
}

if (isset($_GET["name"])) {
    //
    $name = str_replace(' ', '%20', $_GET["name"]);
    $summonerV4 = "https://euw1.api.riotgames.com/lol/summoner/v4/summoners/by-name/" . $name . $apiKey;

    $get_http_response_code = get_http_response_code($summonerV4);

    switch ($get_http_response_code) {
        case 200:
            //creo un objeto con los datos del json
            $obj = json_decode(file_get_contents($summonerV4));
            $summoner = new Summoner();
            //seteo los datos al objeto Summoner;
            $summoner->set($obj);
            break;
        case 404:
            $error = "El invocador no existe";
            $errorIcon = "<i class='fas fa-question-circle'></i>";
            break;
        case 403:
            $error = "Pongase en contacto con el administrador, la API key ha caducado";
            $errorIcon = "<i class='fas fa-exclamation-circle'></i>";
            break;
        default:
            $error = "Estamos teniendo problemas, disculpe las molestias";
            $errorIcon = "<i class='fas fa-exclamation-circle'></i>";
            break;
    }

    //
    $leagueV4 = "https://euw1.api.riotgames.com/lol/league/v4/entries/by-summoner/". $summoner->getId() . $apiKey;
    $soloQ = new League();
    $flex = new League();

    //TODO  miniseriesDTO (promos)
    $obj = json_decode(file_get_contents($leagueV4));


}

require_once "summoner.view.php";
