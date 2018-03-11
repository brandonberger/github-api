<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
<?php

/**
 * to run:
 * php -s localhost:8000
 * visit localhost:8000
 */

require_once __DIR__ . '/vendor/autoload.php';

use \Cache\Adapter\Redis\RedisCachePool;

$client = new \Redis();
$client->connect('127.0.0.1', 6379);
$pool = new RedisCachePool($client);

$client = new \Github\Client();
$client->addCache($pool);

$rateLimits = $client->api('rate_limit')->getRateLimits();
// var_dump($rateLimits);
// echo 'Limit: ' .$rateLimits . '<br>';
/**
 * The Github\Client::AUTH_HTTP_* authentication methods send their values to GitHub using HTTP Basic Authentication.
 *********************************************************************************************************************
 * The required value of $password depends on the chosen method(param#3).
 * For Github\Client::AUTH_URL_TOKEN, Github\Client::AUTH_HTTP_TOKEN and Github\Client::JWT methods
 * you should provide the API token in $username variable
 * ($password is omitted in this particular case).
 */
$token = file_get_contents('apikey.txt');
$client->authenticate($token, null, Github\Client::AUTH_HTTP_TOKEN);
$repositories = $client->api('user')->repositories('brandonberger');

foreach ($repositories as $repo) { ?>
    <div class="card" style="width: 13rem; height:150px; display:inline-block; margin:15px;">
        <div class="card-body">
            <h5 style="text-overflow: ellipsis;white-space: nowrap;overflow:hidden;" class="card-title"><?=$repo['name']?></h5>
            <p style="font-size:.7em;min-height:30px;text-overflow: ellipsis;white-space: nowrap;overflow:hidden;" class="card-text"><?=$repo['description']?></p>
            <a target="_blank" href="<?=$repo['html_url']?>" style="text-overflow: ellipsis;white-space: nowrap;overflow:hidden;font-size:.7em;max-width:100%;min-width:100%;" class="btn btn-primary"><?=$repo['full_name']?></a>
        </div>
    </div>
<?php
}

// highlight_string("<?php\n\$data =\n" . var_export($repositories, true) . ";\n?\>//");
