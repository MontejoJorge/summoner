<?php
namespace Models;

class MatchInfo {
    private $gameId, $participantIdentities, $queueId, $gameType, $gameDuration, $teams, $platformId, $gameCreation, $seasonId, $gameVersion, $mapId, $gameMode, $participants;

    function __construct(){}

    public function set($data) {
        foreach ($data AS $key => $value) $this->{$key} = $value;
    }

    /**
     * @return mixed
     */
    public function getGameId()
    {
        return $this->gameId;
    }

    /**
     * @param mixed $gameId
     */
    public function setGameId($gameId)
    {
        $this->gameId = $gameId;
    }

    /**
     * @return mixed
     */
    public function getParticipantIdentities()
    {
        return $this->participantIdentities;
    }

    /**
     * @param mixed $participantIdentities
     */
    public function setParticipantIdentities($participantIdentities)
    {
        $this->participantIdentities = $participantIdentities;
    }

    /**
     * @return mixed
     */
    public function getQueueId()
    {
        return $this->queueId;
    }

    /**
     * @param mixed $queueId
     */
    public function setQueueId($queueId)
    {
        $this->queueId = $queueId;
    }

    /**
     * @return mixed
     */
    public function getGameType()
    {
        return $this->gameType;
    }

    /**
     * @param mixed $gameType
     */
    public function setGameType($gameType)
    {
        $this->gameType = $gameType;
    }

    /**
     * @return mixed
     */
    public function getGameDuration()
    {
        return $this->gameDuration;
    }

    /**
     * @param mixed $gameDuration
     */
    public function setGameDuration($gameDuration)
    {
        $this->gameDuration = $gameDuration;
    }

    /**
     * @return mixed
     */
    public function getTeams()
    {
        return $this->teams;
    }

    /**
     * @param mixed $teams
     */
    public function setTeams($teams)
    {
        $this->teams = $teams;
    }

    /**
     * @return mixed
     */
    public function getPlatformId()
    {
        return $this->platformId;
    }

    /**
     * @param mixed $platformId
     */
    public function setPlatformId($platformId)
    {
        $this->platformId = $platformId;
    }

    /**
     * @return mixed
     */
    public function getGameCreation()
    {
        return $this->gameCreation;
    }

    /**
     * @param mixed $gameCreation
     */
    public function setGameCreation($gameCreation)
    {
        $this->gameCreation = $gameCreation;
    }

    /**
     * @return mixed
     */
    public function getSeasonId()
    {
        return $this->seasonId;
    }

    /**
     * @param mixed $seasonId
     */
    public function setSeasonId($seasonId)
    {
        $this->seasonId = $seasonId;
    }

    /**
     * @return mixed
     */
    public function getGameVersion()
    {
        return $this->gameVersion;
    }

    /**
     * @param mixed $gameVersion
     */
    public function setGameVersion($gameVersion)
    {
        $this->gameVersion = $gameVersion;
    }

    /**
     * @return mixed
     */
    public function getMapId()
    {
        return $this->mapId;
    }

    /**
     * @param mixed $mapId
     */
    public function setMapId($mapId)
    {
        $this->mapId = $mapId;
    }

    /**
     * @return mixed
     */
    public function getGameMode()
    {
        return $this->gameMode;
    }

    /**
     * @param mixed $gameMode
     */
    public function setGameMode($gameMode)
    {
        $this->gameMode = $gameMode;
    }

    /**
     * @return mixed
     */
    public function getParticipants()
    {
        return $this->participants;
    }

    /**
     * @param mixed $participants
     */
    public function setParticipants($participants)
    {
        $this->participants = $participants;
    }
}