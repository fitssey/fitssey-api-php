<?php

require_once __DIR__ . '/../src/LightenbodyService.php';

/**
 * An example file showing how to perform a connection against the lightenbody's API.
 * To obtain API credentials log in to your BackOffice account and under Studio > Security
 * generate a new API key.
 */

/** @var string $uuid Unique Identifier of your studio */
$uuid = 'empty';

/** @var string $apiGuid GUID value of your API credentials */
$apiGuid = 'empty';

/** @var string $apiKey KEY value of your API credentials */
$apiKey = 'empty';

/** @var string $apiSource SOURCE value of your API credentials */
$apiSource = 'empty';

/** @var bool $isTest Determines whether the connection is a test or not (use false in production!) */
$isTest = true;

// construct the service
$lightenbodyService = new LightenbodyService($uuid, $apiGuid, $apiKey, $apiSource);

// get the response back, either success or failure one
$hasConnection = $lightenbodyService
    ->setIsDebug($isTest)
    ->testConnection();

// prepare some variables used for this example
$url = $lightenbodyService->getUrl();
$responseCode = $lightenbodyService->getResponseCode();

?>

<html>
<head></head>
<body>
<p>Performing a test on lightenbody's API:</p><br>
<p>URL: <em><?php echo $url; ?></em></p>
<p>Response code: <em><?php echo $responseCode; ?></em></p>
<p>Debug mode: <em><?php if($lightenbodyService->getIsDebug()): ?>Yes<?php else: ?>No<?php endif;?></em></p>
<p>Connection result:&nbsp;
    <em>
        <?php if(200 == $responseCode): ?>
            OK
            <?php elseif(404 == $responseCode): ?>
            Not found. Is studio uuid valid?
            <?php elseif(403 == $responseCode): ?>
            Not authenticated. Are API credentials (GUID, KEY, SOURCE) correct? Is domain allowed to connect from? Check your API settings under BackOffice Studio account.
            <?php elseif(500 == $responseCode): ?>
            An internal error occurred.
        <?php endif; ?>
    </em>
</p>

</body>
</html>
