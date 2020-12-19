<?php
namespace Models;

class Summoner {
    private $id,$accountId,$puuid,$name,$profileIconId,$revisionDate,$summonerLevel;

    public function set($data) {
        foreach ($data AS $key => $value) $this->{$key} = $value;
    }

    public function __construct(){}

    public function getId()
    {
        return $this->id;
    }

    public function getAccountId()
    {
        return $this->accountId;
    }

    public function getPuuid()
    {
        return $this->puuid;
    }

    public function getName()
    {
        return $this->name;
    }

    public function getProfileIconId()
    {
        return $this->profileIconId;
    }

    public function getRevisionDate()
    {
        return $this->revisionDate;
    }

    public function getSummonerLevel()
    {
        return $this->summonerLevel;
    }
}
