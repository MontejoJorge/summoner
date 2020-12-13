<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="./style/style.css">
    <script src="https://kit.fontawesome.com/9c6fba351e.js" crossorigin="anonymous"></script>
    <title>Summoner</title>
</head>
<body>
    <header>
        <h1>Summoner</h1>
            <form action="summoner.php" method="get">
                <input type="text" name="name" id="summonerName" placeholder="Nombre"><input type="submit" value="Buscar" id="search">
            </form>
        </div>
    </header>
    <div id="primary">
        <?php
            if (!isset($error)){
            ?>    
                <div id="summonerInfo" class="info">
                <img <?= 'src="./media/profileicons/'.  $summoner->getProfileIconId() .'.png"'?> alt="profile icon">
                <div id="summonerDesc">
                    <h3><?= $summoner->getName()?></h3>
                    <p id="level"><?= $summoner->getSummonerLevel()?></p>
                </div>
            </div>
            <div id="soloQ-info" class="info">
                <img src="./media/leagues/Emblem_Gold.png" alt="league icon">
                <div class="leagueInfo">
                    <p class="leagueType">Clasificatoria en solitario</p>
                    <h4>Gold 4</h4>
                    <p id="leaguePoints">0 LP</p>
                    <p id="wins-losses">200W 200L</p>
                    <p id="winRate">Tasa de victoria 48%</p>
                </div>
            </div>
            <div id="flex-info" class="info">
                <img src="./media/leagues/Emblem_Silver.png" alt="league icon">
                <div class="leageuInfo">
                    <p class="leagueType">Clasificatoria flexible</p>
                    <h4>Gold 4</h4>
                    <p id="leaguePoints">0 LP</p>
                    <p id="wins-losses">200W 200L</p>
                    <p id="winRate">Tasa de victoria 48%</p>
                </div>
            </div>
            <div id="matchHistory">
    
            </div>

            <?php
            } else {
            ?>
                <div id="errorIcon"><?=$errorIcon?></div>
                <div id="errorDesc"><?=$error?></div>
            <?php
            }
        ?>
    </div>

</body>
</html>