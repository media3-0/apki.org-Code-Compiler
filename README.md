# apki.org-Code-Compiler
Compiling tool for exercises solving in apki.org platform. It is working as small REST JSON API.

# How to run

1.Add a file `config/secrets.inc.php` with content:

```php
<?php
//Define HackerEarth API key secret here
define('HACKER_EARTH_API_SECRET', 'Your API Secret');

//Define your custom OnlyAllowedIPs function, it should throw an Exception when wrong IP.
function OnlyAllowedIPs(){
        return true;
//        throw new Exception('Access denied.'); //Uncomment to disallow some IP
}
```

2.Run and enjoy :-)

# Thanks
This application is working thanks to Hacker Earth project https://code.hackerearth.com . We use Slim Framework with slim-jsonAPI ( https://github.com/entomb/slim-json-api ) - great fot this small "REST" JSON-API. Thank you for your support :)



-----

(c) Copyrights 2015 Jakub Król, Apki.org. License: GNU/GPL.
