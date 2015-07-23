<?php
/**
 * (c) Copyrights Jakub Krol 2015
 * GNU/GPL
 */
class hackerEarthV3CodeCompiler {
    private $client_secret;
    const COMPILE_ENDPOINT =    'http://api.hackerearth.com/v3/code/compile/'; //You may change HTTP to HTTPS
    const RUN_ENDPOINT =        'http://api.hackerearth.com/v3/code/run/';     //You may change HTTP to HTTPS

    /**
     * @var array Array of default settings. Check pout documentation for more.
     */
    public $defaultSettings = [
        'time_limit'=>5, //time_limit is an optional parameter which is the max time for which program is allowed to run. Maximum value of time limit can be 5 seconds. Any value greater than 5 in the API request is set to 5. Exceeding the time limit returns the status as TLE in the response.
        'memory_limit'=>262144, //memory_limit is an optional paramer which is the max memory allowed to be used by the program. Maximum value of memory limit can be 262144 (256 MB). Any value greater than 262144 in the API request is set to 262144. Exceeding the memory limit returns the status as MLE in the response.
    ];

    /**
     * @param string $client_secret Client secret may be obtained from < http://www.hackerearth.com/api/register/ >. Required
     */
    public function __construct($client_secret){
        $this->client_secret = $client_secret;
    }

    /**
     * @param string $language Language. One of: ['C', 'CPP', 'CPP11', 'CLOJURE', 'CSHARP', 'JAVA', 'JAVASCRIPT', 'HASKELL', 'PERL', 'PHP', 'PYTHON', 'RUBY']. Required.
     * @param string $sourceCode Source code. Required.
     * @param string $userInput Optional. User input in console/app.
     * @param boolean $encodeUnicodeInText Optional. If true (default) it will automatically encode unicode in source code changing UTF characters into JSON-like escaped values. It looks like it is necessary for some languages with this API.
     * @return array JSON with original response. Use ['run_status']['output_html'] to show, ['run_status']['output'] to compare. Check docs for usage and more options.
     */
    public function run($language, $sourceCode, $userInput='', $encodeUnicodeInText=true){
        $data = array_merge($this->defaultSettings, [
            'client_secret'=>$this->client_secret,
            'source'=>$encodeUnicodeInText?$this->escapeUnicodeCharactersAsJSON($sourceCode):$sourceCode,
            'lang'=>$language,
            'input'=>$userInput,
            'async'=>0
        ]);
        return ($this->curlIt(self::RUN_ENDPOINT, $data));
    }

    private function uniord($s) {
        return unpack('V', iconv('UTF-8', 'UCS-4LE', $s))[1];
    }

    private function escapeUnicodeCharactersAsJSON($string){
        return preg_replace_callback('/[\x{007f}-\x{ffff}]/u', function($c){
            return '\u'.(substr('0000'.dechex($this->uniord($c[0])), -4));
        }, $string);
    }

    private function unichr($i) {
        return iconv('UCS-4LE', 'UTF-8', pack('V', $i));
    }

    /**
     * Helper method to unescape unicode characters from output.
     * @param string $string Input string
     * @return string Output string.
     */
    public function unescapeUnicodeCharactersAsJSON($string){
        return preg_replace_callback('/\\\u([0-9a-fA-F]){4}/u', function($c){
            return $this->unichr(hexdec(substr($c[0], 2)));
        }, $string);
    }

    /**
     * Private.
     * CURL an URL helper.
     * @param string $url API endpoint URL.
     * @param array $postBody Array of parameters.
     * @return array JSON-decoded output of server.
     */
    private function curlIt($url, $postBody){
        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL,$url);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch,CURLOPT_ENCODING , ""); //required to decode gzip
         curl_setopt($ch, CURLOPT_POSTFIELDS,
                  http_build_query($postBody));

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $server_output = curl_exec ($ch);

        curl_close ($ch);

        return json_decode($server_output, true);
    }
} 