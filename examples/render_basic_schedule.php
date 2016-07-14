<?php

require_once __DIR__ . '/../src/LightenbodyService.php';

/**
 * An example file showing how to render the schedule gathered from lightenbody.
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

/** @var string $locale Set default locale code, e.g. en_EN, pl_PL, es_ES */
$locale = 'en_EN';

/** @var \DateTime $startDate Enter start date of the schedule */
$startDate = new \DateTime();

/** @var \DateTime $endDate Enter end date of the schedule */
$endDate = new \DateTime('+ 6 days');

// construct the service
$lightenbodyService = new LightenbodyService($uuid, $apiGuid, $apiKey, $apiSource);

// get the response back, either success or failure one
$response = $lightenbodyService
    ->setIsDebug($isTest)
    ->getSchedule($startDate, $endDate);

// prepare some variables used for this example
$url = $lightenbodyService->getUrl();
$responseCode = $lightenbodyService->getResponseCode();

?>

<html>
<head></head>
<body>
<p>Performing a connection on lightenbody's API:</p><br>
<p>URL: <em><?php echo $url; ?></em></p>
<p>Response code: <em><?php echo $responseCode; ?></em></p>
<p>Debug mode: <em><?php if($lightenbodyService->getIsDebug()): ?>Yes<?php else: ?>No<?php endif;?></em></p>
<h1>Schedule</h1>
<!-- so first let's make sure that we have a valid response from the API -->
<?php if(200 == $responseCode): ?>
    <!-- now iterate over the schedule collection -->
    <?php foreach($response->schedule as $class): ?>
        <!-- a class variable always contains 'date' property -->
        <h2><?php echo $class->date; ?></h2>
        <!-- now we have to check if the given date contains any schedule -->
        <?php if(isset($class->schedule)): ?>
            <table>
                <thead>
                <tr>
                    <th>Time</th>
                    <th>Class</th>
                    <th>Teacher</th>
                    <th>Level</th>
                    <th>Location</th>
                    <th>Capacity</th>
                </tr>
                </thead>
                <tbody>
                <!-- let's iterate over class schedule to find any entries -->
                <?php foreach($class->schedule as $schedule): ?>
                    <tr id="<?php $schedule->referenceId; ?>">
                        <td><?php echo $schedule->startTime; ?> - <?php echo $schedule->endTime; ?></td>
                        <td><?php echo $schedule->classroom->name->$locale->value; ?></td>
                        <td><?php echo $schedule->member->user->fullName; ?></td>
                        <td><?php echo $schedule->classroom->programLevel->name->$locale->value; ?></td>
                        <td><?php echo $schedule->room->location->name->$locale->value; ?></td>
                        <td><?php echo $schedule->onlineCapacity; ?></td>
                    </tr>

                <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p>No classes scheduled for this day.</p>
        <?php endif; ?>
    <?php endforeach; ?>
<?php else: ?>
    <p>Cannot render the schedule. Probably wrong API credentials (GUID, KEY, SOURCE) given?</p>
<?php endif; ?>
</body>
</html>
