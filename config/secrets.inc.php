<?php
//Define HackerEarth API key secret here
define('HACKER_EARTH_API_SECRET', '');

//Define your custom OnlyAllowedIPs function, it should throw an Exception when wrong IP.
function OnlyAllowedIPs(){
        return true;
//        throw new Exception('Access denied.'); //Uncomment it to disallow IP
}