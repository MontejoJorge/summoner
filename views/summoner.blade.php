<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../style/style.css">
    <script src="https://kit.fontawesome.com/9c6fba351e.js" crossorigin="anonymous"></script>
    <title>Summoner</title>
</head>

<body>
    <header>
        <a href="summoner.php"><h1>Summoner</h1></a>
        <form action="summoner.php" method="get">
            <input type="text" name="name" id="summonerName" placeholder="Nombre"><input type="submit" value="Buscar" id="search">
        </form>
        </div>
    </header>
    <div id="primary">
        @if ($error->getErrorDesc()=="" && $_GET["name"]!="")
        <div id="summonerInfo" class="info">
            <img src="./media/profileicons/{{ $summoner->getProfileIconId() }}.png" alt="profile icon">
            <div id="summonerDesc">
                <h3>{{ $summoner->getName() }}</h3>
                <p id="level">{{ $summoner->getSummonerLevel() }}</p>
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
                    <img src="./media/leagues/Emblem_{{ ucfirst(strtolower($soloQ->getTier())) }}.png"'  alt="league icon">
                    <div class="leagueInfo">
                        <p class="leagueType">Clasificatoria en solitario</p>
                        <h4> {{ ucfirst(strtolower($soloQ->getTier())) . " " . $soloQ->getRank() }} </h4>
                        <p id="leaguePoints"> {{ $soloQ->getLeaguePoints() . " LP " . getPromoInfo($soloQ) }}</p>
                        <p id="wins-losses"> {{ $soloQ->getWins() . "W " . $soloQ->getLosses() . "L" }} </p>
                        <p id="winRate">Tasa de victoria  {{ getWinrate($soloQ->getWins(), $soloQ->getLosses()) }} %</p>
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
                    <img src="./media/leagues/Emblem_{{ ucfirst(strtolower($flex->getTier())) }}.png"'  alt="league icon">
                    <div class="leagueInfo">
                        <p class="leagueType">Clasificatoria flexible</p>
                        <h4> {{ ucfirst(strtolower($flex->getTier())) . " " . $flex->getRank() }}</h4>
                        <p id="leaguePoints"> {{ $flex->getLeaguePoints() . " LP " . getPromoInfo($flex) }}</p>
                        <p id="wins-losses"> {{ $flex->getWins() . "W " . $flex->getLosses() . "L" }} </p>
                        <p id="winRate">Tasa de victoria  {{ getWinrate($flex->getWins(), $flex->getLosses()) }} %</p>
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
