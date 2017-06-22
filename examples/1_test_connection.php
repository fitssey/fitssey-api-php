<?php

require_once __DIR__ . '/../src/LightenbodyService.php';

$uuid = 'your_studio_uuid';
$apiGuid = 'your_api_guid';
$apiKey = 'your_api_key';
$apiSource = 'your_api_source'; // optional

/** @var bool $isTest Determines whether the connection is a test or not (use false in production!) */
$isTest = true;

// construct the service
$lightenbodyService = new LightenbodyService($uuid, $apiGuid, $apiKey, $apiSource);

// get the response back, either success or failure one
$hasConnection = $lightenbodyService
    ->post('/test');

// prepare some variables used for this example
$url = $lightenbodyService->getApiUrl();
$responseCode = $lightenbodyService->getResponseCode();

?>

<html>
<head></head>
<body>
<h2>Test connection:</h2>
<span><strong>URL</strong>: <?php echo $url; ?></span><br>
<span><strong>Response code</strong>: <?php echo $responseCode; ?></span><br>
<span><strong>Connection status</strong>:&nbsp;</span>
    <?php if(200 == $responseCode): ?>
        <span style="color: green;">Connection is good!</span>
    <?php elseif(404 == $responseCode): ?>
        <span style="color: red;">Not found. Is studio uuid valid?</span>
    <?php elseif(403 == $responseCode): ?>
        <span style="color: red;">Not authenticated.</span><br>
        <span>Check the following points:</span>
        <ul>
                <li>Are API credentials (UUID, GUID, KEY, SOURCE) correct?</li>
                <li>Is domain allowed to connect from?</li>
        </ul>
    <?php elseif(500 == $responseCode): ?>
        <span style="color: red;">An internal error occurred.</span>
    <?php endif; ?>
</body>
</html>
