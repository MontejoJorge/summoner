<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);

require_once "./models/Summoner.php";
require_once "./models/League.php";
require_once "./models/ChampionMastery.php";

require_once "./API_keys.php";

/*Declaracion de variables globales*/

$summoner = new Summoner();
$soloQ = new League();
$flex = new League();
$championMastery = new championMastery();

$errorDesc;
$errorIcon;



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
            $GLOBALS["errorDesc"] = "El invocador no existe";
            $GLOBALS["errorIcon"] = "<i class='fas fa-question-circle'></i>";
            break;
        case 403:
            $GLOBALS["errorDesc"] = "Pongase en contacto con el administrador, la API key ha caducado";
            $GLOBALS["errorIcon"] = "<i class='fas fa-exclamation-circle'></i>";
            break;
        default:
            $GLOBALS["errorDesc"] = "Estamos teniendo problemas, disculpe las molestias " . $get_http_response_code;
            $GLOBALS["errorIcon"] = "<i class='fas fa-exclamation-circle'></i>";
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
    $championMasteryV4 = "https://euw1.api.riotgames.com/lol/champion-mastery/v4/champion-masteries/by-summoner/" . $GLOBALS["summoner"]->getId();
    $championMasteryV4OBJ = executeRequest($championMasteryV4);

    return $championMasteryV4OBJ[$num];
}


//si han introducido el enombre empiezo a llamar a los metodos
if (isset($_GET["name"])) {
    //le paso al metodo el nombre remplazando los espacios por su caracter especial
    getSummonerInfo(str_replace(' ', '%20', $_GET["name"]));

    //si el invocador ha sido encontrado podemos buscar el resto
    if ($GLOBALS["summoner"]->getId() != "") {
        getLeagueInfo();

        //en el view un for y cada vez que lo repita mostrara 1
        $championId = getChampionMasteryInfo(54)->{"championId"};
        $championMastery = getChampionMasteryInfo(54);
        //echo $key;

        $allChampions = json_decode(file_get_contents("./media/other/champion.json"))->data;

        foreach ($allChampions as $champion){
            if ($champion->key==$championId){
                echo $champion->id;
                $GLOBALS["championMastery"]->set($champion);
                break;
            }
        }

        //TODO guardar el nombre encontrado en el objeto ChampionMastery
        echo $GLOBALS["championMastery"]->getId();

    //https://es.stackoverflow.com/questions/414826/acceder-a-json-por-indice-en-php
       //echo count($allChampions);
        // for ($i=0; $i < count($allChampions) ; $i++) { 
            
        // }
    }
}

require_once "summoner.view.php";
