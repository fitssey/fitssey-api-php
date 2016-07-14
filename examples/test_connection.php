<?php

require_once __DIR__ . '/../src/LightenbodyService.php';

/**
 * An example file showing how to perform a connection against the lightenbody's API.
 * To obtain API credentials login into your BackOffice account and under Studio > Security
 * generate a new API key.
 */

/** @var string $uuid */
$uuid = 'empty';

/** @var string $apiGuid */
$apiGuid = 'empty';

/** @var string $apiKey */
$apiKey = 'empty';

/** @var string $apiSource */
$apiSource = 'empty';

/** @var bool $isTest */
$isTest = true;

$lightenbodyService = new LightenbodyService($uuid, $apiGuid, $apiKey, $apiSource);
$hasConnection = $lightenbodyService
    ->setIsDebug($isTest)
    ->testConnection();

$url = $lightenbodyService->getUrl();
$responseCode = $lightenbodyService->getResponseCode();

?>

<html>
<head></head>
<body>
<p>Performing a test on lightenbody's API:</p><br>
<p>Url: <em><?php echo $url; ?></em></p>
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
