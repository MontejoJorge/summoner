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
use Models\MatchInfo;


/*Declaracion de variables globales*/

$summoner = new Summoner(); //Contiene informacion basica sobre el invocador
$soloQ = new League(); //Contiene informacion sobre la liga "soloQ"
$flex = new League(); //Contiene informacion sobre la liga "flex"
$championsMasteries = []; //Contiene un array con informacion sobre los 3 campeones con mas puntos
$error = new Error(); //Contendra informacion si ocurre un error en la solicitud a riot games
$matchList = []; //Contiene la lista de partidas de un invocador
$matchInfoList = []; //Contiene un array con informacion sobre cada partida


function executeRequest($url)
{
    $options = array(
        "http" => array(
            "header" => "User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/87.0.4280.88 Safari/537.36\r\n" .
                "Accept-Language: es-ES,es;q=0.9,en;q=0.8\r\n" .
                "Accept-Charset: application/x-www-form-urlencoded; charset=UTF-8\r\n" .
                "Origin: https://developer.riotgames.com\r\n" .
                "X-Riot-Token: " . $GLOBALS["apiKey"] . "\r\n"
        )
    );    
    $context  = stream_context_create($options);

    $headers = get_headers($url,0,$context);
    $responseCode = substr($headers[0], 9, 3);

    switch ($responseCode) {
        case 200:
            return json_decode(file_get_contents($url, false, $context));
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
            $GLOBALS["error"]->setErrorDesc("Estamos teniendo problemas, disculpe las molestias. Codigo de error: " . $responseCode);
            $GLOBALS["error"]->setErrorIcon("fas fa-exclamation-circle");
            break;
    }    
}

function getSummonerInfo($name)
{
    $summonerV4 = "https://euw1.api.riotgames.com/lol/summoner/v4/summoners/by-name/" . $name;
    
    //creo un objeto con los datos del json
    $obj = executeRequest($summonerV4);
    //seteo los datos al objeto Summoner;
    $GLOBALS["summoner"]->set($obj);
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

    //TODO usar la funcion getChampionNameById ?
    $championId = $championMasteryV4OBJ[$num]->{"championId"};
    $championMasteryInfo = $championMasteryV4OBJ[$num];

    $allChampions = json_decode(file_get_contents("./data/champion.json"))->data;

    foreach ($allChampions as $champion) {
        if ($champion->key == $championId) {
            $championMastery->set($championMasteryInfo);
            $championMastery->setChampionName($champion->id);
            break;
        }
    }
    return $championMastery;
}

function getChampionNameById($id)
{
    $allChampions = json_decode(file_get_contents("./data/champion.json"))->data;

    foreach ($allChampions as $champion) {
        if ($champion->key == $id) {
            return $champion->id;
        }
    }

}

function shortNumbers($n, $precision = 0)
{
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
    return $n_format . " Points";
}

function getMatchlist($acountId, $beginIndex = 0, $endIndex = 100)
{
    $matchV4 = "https://euw1.api.riotgames.com/lol/match/v4/matchlists/by-account/" . $acountId . "?endIndex=" . $endIndex . "&beginIndex=" . $beginIndex;

    $matchOBJ = executeRequest($matchV4);

    $GLOBALS["matchList"] = $matchOBJ->matches;
}

function getMatchInfo($matchId)
{
    $match = "https://euw1.api.riotgames.com/lol/match/v4/matches/" . $matchId;

    $matchOBJ = new MatchInfo();

    $matchOBJ->set(executeRequest($match));

    array_push($GLOBALS["matchInfoList"], $matchOBJ);
}

function getQueueDesc($id)
{
    $queues = json_decode(file_get_contents("./data/queues.json"));

    foreach ($queues as $q) {
        if ($q->queueId == $id) {
            $desc = $q->description;
            break;
        }
    }
    return $desc;
}

function timeAgo($date)
{
    $timestamp = $date / 1000;
    $strTime = array("second", "minute", "hour", "day", "month", "year");
    $length = array("60", "60", "24", "30", "12", "10");

    $currentTime = time();
    if ($currentTime >= $timestamp) {
        $diff     = time() - $timestamp;
        for ($i = 0; $diff >= $length[$i] && $i < count($length) - 1; $i++) {
            $diff = $diff / $length[$i];
        }

        $diff = round($diff);
        //return $diff . " " . $strTime[$i] . "(s) ago ";
        return $diff . " " . $strTime[$i] . ($diff > 1 ? 's' : '') . ' ago';
    }
}

function getParticipantId($match, $name=null)
{
    if (!isset($name)){
        $name = $GLOBALS["summoner"]->getName();
    }
    $id = "";
    foreach ($match->getParticipantIdentities() as $p) {
        if ($p->player->summonerName == $name) {
            $id = $p->participantId;
            return $id;
        }
    }
}

function getParticipantsInfo($match, $participantId, $info)
{
    $infoReturn = "";
    foreach ($match->getParticipants() as $p) {
        if ($p->participantId == $participantId) {
            $infoReturn = $p->$info;
            return $infoReturn;
        }
    }
}

function getWinOrLose($match)
{
    $id = getParticipantId($match);

    $team = getParticipantsInfo($match, $id, "teamId");

    foreach ($match->getTeams() as $m) {
        if ($m->teamId == $team) {
            $result = $m->win;
        }
    }

    switch ($result) {
        case "Win":
            return "Victory";
        case "Fail":
            return "Defeat";
        default:
            return "Tie";
    }
}

//si han introducido el enombre empiezo a llamar a los metodos
if (isset($_GET["name"])) {
    //le paso al metodo el nombre remplazando los espacios por su caracter especial
    getSummonerInfo(str_replace(' ', '%20', $_GET["name"]));
    //si el invocador ha sido encontrado podemos buscar el resto
    if ($GLOBALS["summoner"]->getId() != "") {
        getLeagueInfo();

        for ($i = 0; $i < 3; $i++) {
            array_push($GLOBALS["championsMasteries"], getChampionMasteryInfo($i));
        }

        //cambiar el 0,5
        getMatchlist($GLOBALS["summoner"]->getAccountId(), 0, 5);

        foreach ($GLOBALS["matchList"] as $m) {
            getMatchInfo($m->gameId);
        }
    }
}
echo Blade::render("summoner", [
    "error" => $GLOBALS["error"],
    "summoner" => $GLOBALS["summoner"],
    "soloQ" => $GLOBALS["soloQ"],
    "flex" => $GLOBALS["flex"],
    "championsMasteries" => $GLOBALS["championsMasteries"],
    "matchInfoList" => $GLOBALS["matchInfoList"]
]);
