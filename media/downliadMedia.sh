#!/bin/bash

rm -rf ./dragontail
rm -rf ./leagues
rm -rf ./profileicons
rm -f ./dragontail.tgz
rm -f ./leagues.zip

curl -sS http://static.developer.riotgames.com/docs/lol/ranked-emblems.zip > leagues.zip

mkdir leagues

apt update
apt install unzip -y

unzip leagues.zip -d ./leagues

clear
echo "This may take a few minutes..."

curl -sS https://ddragon.leagueoflegends.com/cdn/dragontail-10.25.1.tgz > dragontail.tgz

mkdir dragontail

mkdir profileicons
mkdir champion

tar -xzf dragontail.tgz -C ./dragontail

cp ./dragontail/10.25.1/img/profileicon/* ./profileicons > /dev/null 2>&1
cp ./dragontail/10.25.1/img/champion/* ./champion > /dev/null 2>&1

rm -rf ./dragontail
rm ./dragontail.tgz
rm ./leagues.zip

echo "---------------------DONE---------------------"