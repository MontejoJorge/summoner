<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);

require "./vendor/autoload.php";
require_once "./API_keys.php";

use duncan3dc\Laravel\Blade;
use Models\Summoner;
use Models\League;
use Models\ChampionMastery;
use Models\Error;


/*Declaracion de variables globales*/

$summoner = new Summoner();
$soloQ = new League();
$flex = new League();
$championsMasteries = [];
$error = new Error();



function get_http_response_code($domain1)
{
    $headers = get_headers($domain1 . $GLOBALS["apiKey"]);
    return substr($headers[0], 9, 3);
}

function executeRequest($url)
{
    return json_decode(file_get_contents($url . $GLOBALS["apiKey"]));
}

function getSummonerInfo($name)
{
    $summonerV4 = "https://euw1.api.riotgames.com/lol/summoner/v4/summoners/by-name/" . $name;

    $get_http_response_code = get_http_response_code($summonerV4);

    switch ($get_http_response_code) {
        case 200:
            //creo un objeto con los datos del json
            $obj = executeRequest($summonerV4);
            //seteo los datos al objeto Summoner;
            $GLOBALS["summoner"]->set($obj);
            break;
        case 404:
            $GLOBALS["error"]->setErrorDesc("El invocador no existe");
            $GLOBALS["error"]->setErrorIcon("fas fa-question-circle");
            break;
        case 403:
            $GLOBALS["error"]->setErrorDesc("Pongase en contacto con el administrador, la API key ha caducado");
            $GLOBALS["error"]->setErrorIcon("fas fa-exclamation-circle");
            break;
        default:
            $GLOBALS["error"]->setErrorDesc("Estamos teniendo problemas, disculpe las molestias " . $get_http_response_code);
            $GLOBALS["error"]->setErrorIcon("fas fa-exclamation-circle");
            break;
    }
}

function getLeagueInfo()
{
    $leagueV4 = "https://euw1.api.riotgames.com/lol/league/v4/entries/by-summoner/" . $GLOBALS["summoner"]->getId();
    $leagueV4OBJ = executeRequest($leagueV4);

    //hay dos tipos de colas, soloq y flex, tego que guardar cada una de ellas en un objeto
    for ($i = 0; $i < count($leagueV4OBJ); $i++) {
        if ($leagueV4OBJ[$i]->{"queueType"} == "RANKED_FLEX_SR") {
            $GLOBALS["flex"]->set($leagueV4OBJ[$i]);
        }
        if ($leagueV4OBJ[$i]->{"queueType"} == "RANKED_SOLO_5x5") {
            $GLOBALS["soloQ"]->set($leagueV4OBJ[$i]);
        }
    }
}

function getPromoInfo($obj)
{
    if ($obj->getLeaguePoints() == 100) {

        //primero miramos que tipo de cola es (duo,flex)
        $progress = $obj->getMiniSeries()->{"progress"};

        $progressIcons = "";

        for ($i = 0; $i < strlen($progress); $i++) {
            switch ($progress[$i]) {
                case "L":
                    $progressIcons .= '<i class="fas fa-times red promo-progress"></i>';
                    break;
                case "W":
                    $progressIcons .= '<i class="fas fa-check green promo-progress"></i>';
                    break;
                case "N":
                    $progressIcons .= '<i class="fas fa-minus gray promo-progress"></i>';
                    break;
            }
        }
        return $progressIcons;
    }
}

function getWinrate($wins, $losses)
{
    return round($wins / ($wins + $losses) * 100);
}

function getChampionMasteryInfo($num)
{    
    $championMastery = new championMastery();

    $championMasteryV4 = "https://euw1.api.riotgames.com/lol/champion-mastery/v4/champion-masteries/by-summoner/" . $GLOBALS["summoner"]->getId();
    $championMasteryV4OBJ = executeRequest($championMasteryV4);

    //TODO cambiar 0 por $num
    $championId = $championMasteryV4OBJ[$num]->{"championId"};
    $championMasteryInfo = $championMasteryV4OBJ[$num];

    $allChampions = json_decode(file_get_contents("./media/other/champion.json"))->data;

    foreach ($allChampions as $champion){
        if ($champion->key==$championId){
            $championMastery->set($championMasteryInfo);
            $championMastery->setChampionName($champion->id);
            break;
        }
    }
    return $championMastery;
}

function shortNumbers($n, $precision = 0){
    if ($n < 1000) {
        // Anything less than a million
        $n_format = number_format($n);
    } else if ($n < 1000000) {
        // Anything less than a billion
        $n_format = number_format($n / 1000, $precision) . 'K';
    } else {
        // At least a billion
        $n_format = number_format($n / 1000000, $precision) . 'M';
    }
    return $n_format." Points";
}

//si han introducido el enombre empiezo a llamar a los metodos
if (isset($_GET["name"])) {
    //le paso al metodo el nombre remplazando los espacios por su caracter especial
    getSummonerInfo(str_replace(' ', '%20', $_GET["name"]));
    //si el invocador ha sido encontrado podemos buscar el resto
    if ($GLOBALS["summoner"]->getId() != "") {
        getLeagueInfo();

        //en el view un for y cada vez que lo repita mostrara 1
        for ($i=0; $i < 3; $i++) { 
            array_push($GLOBALS["championsMasteries"],getChampionMasteryInfo($i));
        }
           
    }
}
echo Blade::render("summoner",[
    "error"=>$GLOBALS["error"],
    "summoner"=>$GLOBALS["summoner"],
    "soloQ"=>$GLOBALS["soloQ"],
    "flex"=>$GLOBALS["flex"],
    "championsMasteries"=>$GLOBALS["championsMasteries"]
]);