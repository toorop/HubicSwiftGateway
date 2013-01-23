<?php
/*
    Copyright 2013 Stéphane Depierrepont (aka Toorop) toorop@toorop.fr

    Based on the job made by Vincent Giersch : https://github.com/gierschv

    Licensed under the Apache License, Version 2.0 (the "License"); you may not
    use this file except in compliance with the License. You may obtain a copy of
    the License at

    http://www.apache.org/licenses/LICENSE-2.0

    Unless required by applicable law or agreed to in writing, software
    distributed under the License is distributed on an "AS IS" BASIS, WITHOUT
    WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied. See the
    License for the specific language governing permissions and limitations under
    the License.

 */


define('CACHEPATH',dirname(__FILE__).'/../../../cache');
define('CACHETIME',3600);


// Cache is ok ?
if(!file_exists(CACHEPATH))
    die500(CACHEPATH .' doesn\'t exists');
if(!is_writable(CACHEPATH))
    die500(CACHEPATH .' is not writable');


// Get Auth-User & Auth-Key  from headers
if (!$_SERVER || !$_SERVER['HTTP_X_AUTH_USER'] || !$_SERVER['HTTP_X_AUTH_KEY']) {
    header("HTTP/1.0 403 Forbidden");
    flush();
    die();
}

$authUser = $_SERVER['HTTP_X_AUTH_USER'];
$authKey = $_SERVER['HTTP_X_AUTH_KEY'];

// Get Credentials (Token and URL)
$credentials = getCredentials($authUser, $authKey);

// Send respoonse
header('X-Storage-Url: '.$credentials[0]);
header('X-Auth-Token: '. $credentials[1]);
header('HTTP/1.0 204 No Content');


/**
 *  Get credentials from OVH
 * @param $user
 * @param $key
 * @return array|mixed
 */
function getCredentials($user, $key)
{

    // In cache ?
    $cacheKey=md5($user);
    if (file_exists(CACHEPATH.'/'.$cacheKey) && time() - CACHETIME < filemtime(CACHEPATH.'/'.$cacheKey))
        return unserialize(file_get_contents(CACHEPATH.'/'.$cacheKey));

    $r = wsCall('sessionHandler', 'getAnonymousSession', array());
    $r = json_decode($r);
    /* get hubics */
    $r = wsCall('hubic', 'getHubics', array('sessionId' => $r->answer->session->id, 'email' => 'toorop@toorop.fr'));
    $hubics = json_decode($r);
    if(!is_array($hubics->answer) || count($hubics->answer)<1) {
        header("HTTP/1.0 404 Not found");
        echo "No hubic account found";
        flush();
        die();
    }
    /* Login */
    $r = wsCall('sessionHandler', 'login', array('login' => $hubics->answer[0]->nic, 'password' => $key, 'context' => 'hubic'));
    $r = json_decode($r);
    /* Get Hubic */
    $r = wsCall('hubic', 'getHubic', array('sessionId' => $r->answer->session->id, 'hubicId' => $hubics->answer[0]->id));
    $hubic = json_decode($r)->answer;
    $c=array(base64_decode($hubic->credentials->username), $hubic->credentials->secret);
    // put in cache
    file_put_contents(CACHEPATH.'/'.$cacheKey,serialize($c));
    return $c;
}


/**
 *  Hello ws.ovh.com do you hear me ?
 *
 * @param $ws
 * @param $method
 * @param $params
 * @return array
 */
function wsCall($ws, $method, $params)
{
    $wsRoots = array('sessionHandler' => 'https://ws.ovh.com/sessionHandler/r4/', 'hubic' => 'https://ws.ovh.com/hubic/r5/');
    $uri = $wsRoots[$ws] . 'rest.dispatcher/' . $method;
    if (is_array($params) && count($params) > 0)
        $uri .= '?params=' . urlencode(json_encode($params));
    /* Init Curl */
    $c = curl_init($uri);
    /* Verbosity (debug) */
    curl_setopt($c, CURLOPT_VERBOSE, 0);
    curl_setopt($c, CURLOPT_RETURNTRANSFER, true);
    /* Go go go !!! */
    $r = curl_exec($c);
    $httpCode = curl_getinfo($c, CURLINFO_HTTP_CODE);
    $error = curl_error($c);
    if ($httpCode !== 200) {
        header('HTTP/1.0 ' . $httpCode . '  ' . $error);
        flush();
        die();
    }
    return $r;
}


/**
 * Hum that's so bad....
 * @param $msg
 */
function die500($msg){
    header("HTTP/1.0 500 Internal Server Error");
    if($msg)
        echo $msg;
    flush();
    die();
}

