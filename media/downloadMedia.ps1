cls
powershell -Command "(New-Object Net.WebClient).DownloadFile('http://static.developer.riotgames.com/docs/lol/ranked-emblems.zip', 'leagues.zip')"
powershell -Command "Invoke-WebRequest http://static.developer.riotgames.com/docs/lol/ranked-emblems.zip -OutFile leagues.zip"
cls

mkdir leagues

Expand-Archive leagues.zip -DestinationPath leagues

cls
echo "This may take a few minutes, powershell will close when finished."

powershell -Command "(New-Object Net.WebClient).DownloadFile('https://ddragon.leagueoflegends.com/cdn/dragontail-10.25.1.tgz', 'dragontail.tgz')"

cls
echo "This may take a few minutes, powershell will close when finished."

powershell -Command "Invoke-WebRequest https://ddragon.leagueoflegends.com/cdn/dragontail-10.25.1.tgz -OutFile dragontail.tgz"
cls
echo "This may take a few minutes, powershell will close when finished."



mkdir profileicons
mkdir champion

mkdir dragontail

tar zxvf dragontail.tgz -C dragontail

cp .\dragontail\10.25.1\img\profileicon\* .\profileicons

cp .\dragontail\10.25.1\img\champion\* .\champion


Remove-Item -Recurse -Force .\dragontail
Remove-Item -Recurse -Force .\dragontail.tgz
Remove-Item -Recurse -Force .\leagues.zip

exit

