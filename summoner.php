<?php
error_reporting(0);

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
$leagues = []; //Contiene informacion sobre las ligas (soloQ y flex)
$championsMasteries = []; //Contiene un array con informacion sobre los 3 campeones con mas puntos
$error = new Error(); //Contendra informacion si ocurre un error en la solicitud a riot games
$matchList = []; //Contiene la lista de partidas de un invocador
$matchInfoList = []; //Contiene un array con informacion sobre cada partida

/**
 * @desc Esta funcion añade los headers a la request y obtiene el codigo de respuesta de dicha request
 * @param string url Es la url de destino
 * @return object Devuelve el objeto obtenido en la request
 */
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

    switch (http_response_code()) {
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
            $GLOBALS["error"]->setErrorDesc("Estamos teniendo problemas, disculpe las molestias. Codigo de error: " . http_response_code());
            $GLOBALS["error"]->setErrorIcon("fas fa-exclamation-circle");
            break;
    }
}

/**
 * @desc Esta funcion prepara la requuests para obtener la informacion de un invocador
 * @param string name Es el nombre del invocador a buscar
 * @return object Informacion de un invocador
 */
function getSummonerInfo($name)
{
    $summonerV4 = "https://euw1.api.riotgames.com/lol/summoner/v4/summoners/by-name/" . $name;

    $summonerV4OBJ = executeRequest($summonerV4);
    return $summonerV4OBJ;
}

/** 
 * @desc Esta funcion obtiene las ligas de un invocador
 * @param string id Es el id de un invocador
 * @return array En la posicion 0 contiene la informacio de soloQ, en la posicion 1 la de flex
 */
function getLeagueInfo($id)
{
    $leagueV4 = "https://euw1.api.riotgames.com/lol/league/v4/entries/by-summoner/" . $id;
    $leagueV4OBJ = executeRequest($leagueV4);

    $flex = new League;
    $soloQ = new League;

    //hay dos tipos de colas, soloq y flex, tego que guardar cada una de ellas en un objeto
    foreach ($leagueV4OBJ as $l) {
        if ($l->queueType == "RANKED_FLEX_SR") {
            $flex->set($l);
        }
        if ($l->queueType == "RANKED_SOLO_5x5") {
            $soloQ->set($l);
        }
    }
    $leagues = [$soloQ, $flex];
    return $leagues;
}

/**
 * @desc Esta funcion comprueba si el invocador esta en promocion
 * @param object league Es una de las posibles ligas (soloQ, flex)
 * @return string Los iconos segun el estado de su promocion
 */
function getPromoInfo($league)
{
    $noPromo = ["MASTER", "GRANDMASTER", "CHALLENGER"];
    if ($league->getLeaguePoints() == 100 && !in_array($league->getTier(), $noPromo)) {
        $progress = $league->getMiniSeries()->progress;
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

/**
 * @param int num Posicion del campeon a buscar, siendo 0 el campeon con mas puntos
 * @return object Toda la informacion de un campeon para un invocador
 */
function getChampionMasteryInfo($num, $id = null)
{
    if (!isset($id)) {
        $id = $GLOBALS["summoner"]->getId();
    }
    $championMastery = new championMastery();

    $championMasteryV4 = "https://euw1.api.riotgames.com/lol/champion-mastery/v4/champion-masteries/by-summoner/" . $id;
    $championMasteryV4OBJ = executeRequest($championMasteryV4);

    $championMasteryInfo = $championMasteryV4OBJ[$num];

    $championName = getChampionNameById($championMasteryInfo->championId);

    $championMastery->setChampionName($championName);
    $championMastery->set($championMasteryInfo);

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

/**
 * @desc Esta funcion guarda el historial de partidas de un invocador. INFO: el rango maximo permitido es de 100, de lo contrario, devuelve un error 400
 * @param string id Es el id de un invocador
 * @param int beginIndex Indica el indice por el que empezar a buscar, siendo 0 la ultima partida
 * @param int endIndex Indica en hasta que indice se buscara
 */
function getMatchlist($id, $beginIndex = 0, $endIndex = 100)
{
    $matchV4 = "https://euw1.api.riotgames.com/lol/match/v4/matchlists/by-account/" . $id . "?endIndex=" . $endIndex . "&beginIndex=" . $beginIndex;

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

/**
 * @param int id Es el identificador de la cola
 * @return string Descripcion de la cola indicada
 */
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

function trunc($val, $f = "0")
{
    if (($p = strpos($val, '.')) !== false) {
        $val = floatval(substr($val, 0, $p + 1 + $f));
    }
    return $val;
}

/**
 * @desc Esta funcion busca el id de un invocador en una partida determinada
 * @param object match Es el objeto de la partida
 * @param string name Es el nombre del invocador a buscar, por defecto el nombre sera el nombre de $GLOBALS["summoner"]
 * @return int Id del invocador indicado en esa partida
 */
function getParticipantId($match, $name = null)
{
    if (!isset($name)) {
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

/**
 * @desc Esta funcion busca una informacion sobre un participante en una partida determinada
 * @param object match Es el objeto de la partida
 * @param int participantId Es el id de un participante en esa partida
 * @param string info Es la informacion a buscar (teamId, championId, spell1Id, spell2Id, stats, timeline)
 */
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

function getSummonerNameByParticipantId($match, $id)
{
    foreach ($match->getParticipantIdentities() as $participant) {
        if ($participant->participantId == $id) {
            return $participant->player->summonerName;
        }
    }
}

function getKDA($match, $participantId, $format)
{
    $stats = getParticipantsInfo($match, $participantId, "stats");

    $k = $stats->kills;
    $d = $stats->deaths;
    $a = $stats->assists;

    if ($format == "math") {
        //(K + A) / D = KDA Ratio
        return trunc(($k + $a) / $d, 2);
    } elseif ($format == "text") {
        return $k . "/" . $d . "/" . $a;
    }
}

function getWinOrLose($match)
{
    $id = getParticipantId($match);
    $result = getParticipantsInfo($match, $id, "stats")->win;

    switch ($result) {
        case 1:
            return "Victory";
        case 0:
            return "Defeat";
        default:
            return "Tie";
    }
}

function getLeagueName($league)
{
    $name = $league->getQueueType();
    if ($name == "RANKED_FLEX_SR") {
        return "flex";
    } else {
        return "soloQ";
    }
}

//si han introducido el enombre empiezo a llamar a los metodos
if (isset($_GET["name"])) {
    //le paso al metodo el nombre remplazando los espacios por su caracter especial
    $summonerName = str_replace(' ', '%20', $_GET["name"]);
    $summonerV4OBJ = getSummonerInfo($summonerName);

    if (isset($summonerV4OBJ)) {
        $GLOBALS["summoner"]->set($summonerV4OBJ);
    }

    //si el invocador ha sido encontrado podemos buscar el resto
    if ($GLOBALS["summoner"]->getId() != "") {
        $leaguesArr = getLeagueInfo($GLOBALS["summoner"]->getId());

        foreach ($leaguesArr as $l) {
            array_push($GLOBALS["leagues"], $l);
        }

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
    "leagues" => $GLOBALS["leagues"],
    "championsMasteries" => $GLOBALS["championsMasteries"],
    "matchInfoList" => $GLOBALS["matchInfoList"]
]);
