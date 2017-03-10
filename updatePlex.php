<?php

$version = currentVersion();
$installedVersion = file_get_contents(__DIR__ . '/version.txt');
$log = fopen(__DIR__ . '/log.log', 'ab');
$date = date('m-d-Y');

if ($version !== $installedVersion) {
    $file = fopen(__DIR__ . '/version.txt', 'wb+');
    fwrite($file,$version);
    fclose($file);
    fwrite($log,PHP_EOL . "$date - Plex updated to $version");
    fclose($log);
    $downloadLink = downloadLink();
    $file = 'sh' .__DIR__. "/plexUpdate.sh $downloadLink 2>&1";
    shell_exec($file);
} else {
    fwrite($log,PHP_EOL . "$date - No update performed, current version $version");
    fclose($log);
}

function currentVersion()
{
    // Initialize a new request for this URL
    $curl = curl_init('https://plex.tv/api/downloads/1.json?channel=plexpass');
    // Set the options for this request
    curl_setopt_array($curl, array(
        CURLOPT_FOLLOWLOCATION => true, // Yes, we want to follow a redirect
        CURLOPT_RETURNTRANSFER => true, // Yes, we want that curl_exec returns the fetched data
        CURLOPT_TIMEOUT => 8,
        CURLOPT_SSL_VERIFYPEER => true,
    ));
    // Fetch the data from the URL
    $data = curl_exec($curl);
    // Close the connection
    curl_close($curl);
    $data = json_decode($data, TRUE);
    return $data['computer']['Linux']['version'];
    //return $data;
}

function downloadLink()
{
    // Initialize a new request for this URL
    $curl = curl_init('https://plex.tv/api/downloads/1.json?channel=plexpass');
    // Set the options for this request
    curl_setopt_array($curl, array(
        CURLOPT_FOLLOWLOCATION => true, // Yes, we want to follow a redirect
        CURLOPT_RETURNTRANSFER => true, // Yes, we want that curl_exec returns the fetched data
        CURLOPT_TIMEOUT => 8,
        CURLOPT_SSL_VERIFYPEER => true,
    ));
    // Fetch the data from the URL
    $data = curl_exec($curl);
    // Close the connection
    curl_close($curl);
    $data = json_decode($data, TRUE);
    foreach ($data['computer']['Linux']['releases'] as $release) {
        if ($release['label'] === 'Ubuntu 64-bit (10.04 Lucid or newer)') {
            return $release['url'];
        }
    }
    return null;
}