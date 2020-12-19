<?php
namespace Models;

class Error {
    private $errorIcon;

    private $errorDesc;

    public function __construct(){}

    /**
     * Get the value of errorIcon
     */ 
    public function getErrorIcon()
    {
        return $this->errorIcon;
    }

    /**
     * Get the value of errorDesc
     */ 
    public function getErrorDesc()
    {
        return $this->errorDesc;
    }

    public function setErrorIcon($errorIcon)
    {
        $this->errorIcon = $errorIcon;
    }

    /**
     * Get the value of errorDesc
     */ 
    public function setErrorDesc($errorDesc)
    {
        $this->errorDesc = $errorDesc;
    }
}