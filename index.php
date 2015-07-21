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

        $codeCompiler = new \hackerEarthV3CodeCompiler(HACKER_EARTH_API_SECRET);
        $inputData = json_decode($app->request->getBody(), true);
        if (empty($inputData['lang'])) throw new Exception('Empty "lang" json param!');
        if (empty($inputData['code'])) throw new Exception('Empty "code" json param!');
        $ans = $codeCompiler->run($inputData['lang'], $inputData['code'], @$inputData['user_input']?:'');
        $app->render(200, [
            'output_html'=>@$ans['run_status']['output_html']?:'',
            'output'=>@$ans['run_status']['output']?:'',
            'all_info'=>$ans
        ]);
    }
);

$app->run();
