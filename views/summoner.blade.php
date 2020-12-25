<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../style/style.css">
    <script src="https://kit.fontawesome.com/9c6fba351e.js" crossorigin="anonymous"></script>
    <script src="../js/jquery-3.5.1.js"></script>
    <script src="../js/summoner.js"></script>
    <title>Summoner</title>
</head>

<body>
    <header>
        <a href="summoner.php"><h1>Summoner</h1></a>
        <form action="summoner.php" method="get">
            <input type="text" name="name" id="summonerName" placeholder="Nombre" required><input type="submit" value="Buscar" id="search">
        </form>
    </header>
    <div id="primary">
        @if ($error->getErrorDesc()=="" && $_GET["name"]!="")
        <div id="summonerInfo" class="info">
            <img src="./media/profileicons/{{ $summoner->getProfileIconId() }}.png" alt="profile icon">
            <div id="summonerDesc">
                <h3>{{ $summoner->getName() }}</h3>
                <p id="level">{{ $summoner->getSummonerLevel() }}</p>
                <label for="reveralMasteries">Show Masteries</label>
                <input type="checkbox" id="reveralMasteries">
            </div>
        </div>
            <div id="championsMasteries" class="info">
                @php $i=1 @endphp
                @foreach ($championsMasteries as $champ)
                    <div id="championMastery{{ $i }}" class="championMastery">
                        <img src="../media/champion/{{ $champ->getChampionName() }}.png" class="championMasteryChamp" alt="champion icon">
                        <img src="../media/other/championmastery_level{{ $champ->getChampionLevel() }}banner.png" class="championMasteryBanner" alt="">
                        <img src="../media/other/championmastery_level{{ $champ->getChampionLevel() }}.png" class="championMasteryIcon" alt="">
                        <p class="championMasteryPoints">{{ shortNumbers($champ->getChampionPoints()) }}</p>
                    </div>
                @php $i++ @endphp
                @endforeach
            </div>
            <div id="soloQ-info" class="info">
                @if ($soloQ->getleagueId() != "")               
                    <img src="./media/leagues/Emblem_{{ ucfirst(strtolower($soloQ->getTier())) }}.png"  alt="league icon">
                    <div class="leagueInfo">
                        <p class="leagueType">Clasificatoria en solitario</p>
                        <h4> {{ ucfirst(strtolower($soloQ->getTier())) . " " . $soloQ->getRank() }} </h4>
                        <p clas="leaguePoints"> {{ $soloQ->getLeaguePoints() . " LP "}} @php print getPromoInfo($soloQ) @endphp</p>
                        <p clas="wins-losses"> {{ $soloQ->getWins() . "W " . $soloQ->getLosses() . "L" }} </p>
                        <p clas="winRate">Tasa de victoria  {{ getWinrate($soloQ->getWins(), $soloQ->getLosses()) }} %</p>
                    </div>
                @else               
                    <img src="./media/other/Emblem_Default.png" alt="league icon">
                    <div class="leagueInfo">
                        <p class="leagueType">Clasificatoria en solitario</p>
                        <h4 class="unranked">Unranked</h4>
                    </div>             
                @endif
            </div>
            <div id="flex-info" class="info">
                @if ($flex->getleagueId() != "")
                    <img src="./media/leagues/Emblem_{{ ucfirst(strtolower($flex->getTier())) }}.png"  alt="league icon">
                    <div class="leagueInfo">
                        <p class="leagueType">Clasificatoria flexible</p>
                        <h4> {{ ucfirst(strtolower($flex->getTier())) . " " . $flex->getRank() }}</h4>
                        <p clas="leaguePoints"> {{ $flex->getLeaguePoints() . " LP "}} @php print getPromoInfo($flex) @endphp</p>
                        <p clas="wins-losses"> {{ $flex->getWins() . "W " . $flex->getLosses() . "L" }} </p>
                        <p clas="winRate">Tasa de victoria  {{ getWinrate($flex->getWins(), $flex->getLosses()) }} %</p>
                    </div>                
                @else                
                    <img src="./media/other/Emblem_Default.png" alt="league icon">
                    <div class="leagueInfo">
                        <p class="leagueType">Clasificatoria en flexible</p>
                        <h4 class="unranked">Unranked</h4>
                    </div>                
                @endif                
            </div>
            <div id="matchHistory">
                @foreach ($matchInfoList as $m)
                    <div class="match">
                       <div class="matchInfo">
                        <p class="gameMode">{{ getQueueDesc($m->getQueueId()) }}</p>
                        <p class="timeAgo">{{ timeAgo($m->getGameCreation()) }}</p>
                        <div class="bar"></div>
                        <p class="matchResult">{{ getWinOrLose($m) }}</p>
                        <p class="gameDuration">{{ gmdate("i",$m->getGameDuration())."m ".gmdate("s",$m->getGameDuration())."s" }}</p>
                       </div>
                       <div class="matchSummoner">
                           <!-- TODO mostrar imagen del campeon getParticipantsInfo -->
                        <img src="../media/champion/{{ getChampionNameById($m) }}" alt="">
                       </div>
                       <div class="matchKDA">

                       </div>
                       <div class="matchSummonerStats">

                       </div>
                       <div class="matchItems">

                       </div>
                       <div class="matchTeam1">
                           
                       </div>
                       <div class="matchTeam2">
                           
                       </div>
                    </div>
                @endforeach
            </div>        
        @elseif (!isset($_GET["name"]))
            <p id="withoutSummonerName">Introduce un nombre de invocador para ver sus estadisticas</p>
        @else
            <div id="errorIcon"><i class="{{ $error->getErrorIcon() }}"></i></div>
            <div id="errorDesc">{{ $error->getErrorDesc() }}</div>
        @endif
    </div>

</body>
</html>
