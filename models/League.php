<?php
namespace Models;

class League {
    private $leagueId,$summonerid,$summonerName,$queueType,$tier,$rank,$leaguePoints,$wins,$losses,$hotStreak,$veteran,$freshBlood,$inactive,$miniSeries;
    

    public function set($data) {
        foreach ($data AS $key => $value) $this->{$key} = $value;
    }

    public function __construct(){}

    public function getLeagueId()
    {
        return $this->leagueId;
    }

    public function getSummonerid()
    {
        return $this->summonerid;
    }

    public function getSummonerName()
    {
        return $this->summonerName;
    }

    public function getQueueType()
    {
        return $this->queueType;
    }

    public function getTier()
    {
        return $this->tier;
    }

    public function getRank()
    {
        return $this->rank;
    }

    public function getLeaguePoints()
    {
        return $this->leaguePoints;
    }

    public function getWins()
    {
        return $this->wins;
    }

    public function getLosses()
    {
        return $this->losses;
    }

    public function getHotStreak()
    {
        return $this->hotStreak;
    }

    public function getVeteran()
    {
        return $this->veteran;
    }

    public function getFreshBlood()
    {
        return $this->freshBlood;
    }

    public function getInactive()
    {
        return $this->inactive;
    }

    public function getMiniSeries()
    {
        return $this->miniSeries;
    }

}