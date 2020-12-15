<?php

class championMastery {
    private $championId, $championLevel, $championPoints, $lastPlayTime, $championPointsSinceLastLevel, $championPointsUntilNextLevel, $chestGranted, $tokensEarned, $summonerId, $championName;

    public function set($data) {
        foreach ($data AS $key => $value) $this->{$key} = $value;
    }

    public function __construct(){}

    /**
     * Get the value of championId
     */ 
    public function getChampionId()
    {
        return $this->championId;
    }

    /**
     * Get the value of championLevel
     */ 
    public function getChampionLevel()
    {
        return $this->championLevel;
    }

    /**
     * Get the value of championPoints
     */ 
    public function getChampionPoints()
    {
        return $this->championPoints;
    }

    /**
     * Get the value of lastPlayTime
     */ 
    public function getLastPlayTime()
    {
        return $this->lastPlayTime;
    }

    /**
     * Get the value of championPointsSinceLastLevel
     */ 
    public function getChampionPointsSinceLastLevel()
    {
        return $this->championPointsSinceLastLevel;
    }

    /**
     * Get the value of championPointsUntilNextLevel
     */ 
    public function getChampionPointsUntilNextLevel()
    {
        return $this->championPointsUntilNextLevel;
    }

    /**
     * Get the value of chestGranted
     */ 
    public function getChestGranted()
    {
        return $this->chestGranted;
    }

    /**
     * Get the value of tokensEarned
     */ 
    public function getTokensEarned()
    {
        return $this->tokensEarned;
    }

    /**
     * Get the value of summonerId
     */ 
    public function getSummonerId()
    {
        return $this->summonerId;
    }

    /**
     * Get the value of championName
     */ 
    public function getChampionName()
    {
        return $this->championName;
    }
}