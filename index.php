<?php
/**
 * (c) Copyrights Jakub Krol 2015
 * GNU/GPL
 */

require 'vendor/autoload.php';
require 'Slim/Slim.php';
require 'src/hackerEarthV3CodeCompiler.class.php';
require 'config/secrets.inc.php';

$app = new \Slim\Slim();
$app->view(new \JsonApiView());
$app->add(new \JsonApiMiddleware());

$app->post(
    '/compile',
    function () use ($app) {
        OnlyAllowedIPs();

        error_log('Getting data...', 4);
        error_log($app->request->getBody(), 4);
        $inputData = json_decode($app->request->getBody(), true);
        if (empty($inputData['lang'])) throw new Exception('Empty "lang" json param!');
        if (empty($inputData['code'])) throw new Exception('Empty "code" json param!');

        error_log('Sending request...', 4);
        $codeCompiler = new \hackerEarthV3CodeCompiler(HACKER_EARTH_API_SECRET);
        $ans = $codeCompiler->run($inputData['lang'], $inputData['code'], @$inputData['user_input']?:'');
        error_log('Response:', 4);
        error_log(json_encode($ans), 4);

        error_log('Rendering response...', 4);
        $app->render(200, [
            'output_html'=>$codeCompiler->unescapeUnicodeCharactersAsJSON((is_null($ans)?'Server busy, please wait.':@$ans['run_status']['output_html']?:(@$ans['compile_status']?nl2br($ans['compile_status']):''))),
            'output'=>$codeCompiler->unescapeUnicodeCharactersAsJSON((is_null($ans)?'Server busy, please wait.':trim(@$ans['run_status']['output']?:@$ans['compile_status']?:''))),
            'all_info'=>$ans
        ]);
    }
);

$app->get(
    '/',
    function() use ($app){
        $app->redirect('http://apki.org');
    }
);

$app->run();
