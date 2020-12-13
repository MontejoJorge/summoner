<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);

require_once "./models/Summoner.php";
require_once "./models/League.php";
require_once "./API_keys.php";

function get_http_response_code($domain1){
    $headers = get_headers($domain1);
    return substr($headers[0], 9, 3);
}

function getWinrate($wins, $losses){
    return round($wins/($wins+$losses)*100);
}

function getPromoInfo($obj){
    if ($obj->getLeaguePoints()==100){

        //primero miramos que tipo de cola es (duo,flex)
        $progress = $obj->getMiniSeries()->{"progress"};

        $progressIcons = "";

        for ($i=0; $i < strlen($progress) ; $i++) { 
            switch ($progress[$i]) {
                case "L":
                    $progressIcons.='<i class="fas fa-times red promo-progress"></i>';
                    break;
                case "W":
                    $progressIcons.='<i class="fas fa-check green promo-progress"></i>';
                    break;
                case "N":
                    $progressIcons.='<i class="fas fa-minus gray promo-progress"></i>';
                    break;
            }
        }
        return $progressIcons;
    }

}


if (isset($_GET["name"])) {
    $name = str_replace(' ', '%20', $_GET["name"]);
    $summonerV4 = "https://euw1.api.riotgames.com/lol/summoner/v4/summoners/by-name/" . $name . $apiKey;

    $get_http_response_code = get_http_response_code($summonerV4);

    $summoner = new Summoner();

    switch ($get_http_response_code) {
        case 200:
            //creo un objeto con los datos del json
            $obj = json_decode(file_get_contents($summonerV4));
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


    //si el invocador ha sido encontrado podemos buscar sus ligas
    if ($get_http_response_code==200){
        
    $leagueV4 = "https://euw1.api.riotgames.com/lol/league/v4/entries/by-summoner/". $summoner->getId() . $apiKey;
    $leagueV4OBJ = json_decode(file_get_contents($leagueV4));

    //hay dos tipos de colas, soloq y flex, tego que guardar cada una de ellas en un objeto
    $soloQ = new League();
    $flex = new League();

    for ($i=0; $i < count($leagueV4OBJ) ; $i++) { 
        if ($leagueV4OBJ[$i]->{"queueType"}=="RANKED_FLEX_SR"){
            $flex->set($leagueV4OBJ[$i]);
        }
        if ($leagueV4OBJ[$i]->{"queueType"}=="RANKED_SOLO_5x5"){
            $soloQ->set($leagueV4OBJ[$i]);
        }
    }

    }
}

require_once "summoner.view.php";
