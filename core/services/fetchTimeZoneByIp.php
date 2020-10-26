<?php

class FetchTimeZoneByIp {

    private $clientIp;
    private $ipApiUrl = 'https://ipapi.co/';
    private $timeZone;

    /**
     * Fill client ip param and request client time zone through ip api
     * @param string clientIp
     */
    function __construct($clientIp)
    {
        $this->clientIp = $clientIp;
        $this->timeZone = file_get_contents($this->ipApiUrl.$this->clientIp.'/timezone/');
    }

    /**
     * Return client time zone
     * @return string
     */
    public function getClientTimeZone() : string
    {
        return $this->timeZone;
    }

}
