<?php

require_once __DIR__ . '/../src/LightenbodyService.php';

$uuid = 'your_studio_uuid';
$apiSource = 'your_api_source';
$apiGuid = 'your_api_guid';
$apiKey = 'your_api_key';
$locale = 'pl_PL'; // ISO-8859-1 format, e.g. en_EN, en_US, pl_PL, fr_FR, nl_NL etc.

/** @var \DateTime $startDate Enter start date of the schedule */
$startDate = new \DateTime();

/** @var \DateTime $endDate Enter end date of the schedule */
$endDate = new \DateTime('+ 6 days');

// construct the service
$lightenbodyService = new LightenbodyService($uuid, $apiGuid, $apiKey, $apiSource);

// get the response back, either success or failure one
$response = $lightenbodyService
    ->post('/schedule', array(
        'startDate' => $startDate->format('Y-m-d'),
        'endDate'   => $endDate->format('Y-m-d')
    ))
;

// prepare some variables used for this example
$url = $lightenbodyService->getApiUrl();
$responseCode = $lightenbodyService->getResponseCode();

?>

<html>
<head></head>
<body>
<h2>Render basic schedule:</h2>
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

<!-- so first let's make sure that we have a valid response from the API -->
<?php if(200 == $responseCode): ?>
    <?php $schedule = $response->schedule; ?>

    <!-- now iterate over the schedule collection -->
    <?php foreach($schedule as $item): ?>

        <!-- a class variable always contains 'date' property -->
        <h3><?php echo $item->date; ?></h3>

        <!-- now we have to check if the given date contains any schedule -->
        <?php if(isset($item->scheduleEvents)): ?>
            <table width="100%" border="1">
                <thead>
                <tr>
                    <th>Time</th>
                    <th>Class</th>
                    <th>Teacher</th>
                    <th>Level</th>
                    <th>Location</th>
                    <th>Capacity</th>
                    <th>Book</th>
                </tr>
                </thead>
                <tbody>

                <!-- let's iterate over ScheduleEvents to find any entries -->
                <?php foreach($item->scheduleEvents as $scheduleEvent): ?>
                    <tr id="<?php $scheduleEvent->referenceId; ?>">
                        <td><?php echo $scheduleEvent->startTime; ?> - <?php echo $scheduleEvent->endTime; ?></td>
                        <td><?php echo $scheduleEvent->scheduleMeta->classService->name->$locale->value; ?></td>
                        <td><?php echo $scheduleEvent->member->user->fullName; ?></td>
                        <td><?php echo $scheduleEvent->scheduleMeta->classService->experienceLevel->name->$locale->value; ?></td>
                        <td><?php echo $scheduleEvent->room->location->name->$locale->value; ?></td>
                        <td><?php echo $scheduleEvent->onlineCapacity; ?></td>
                        <td>
						    <?php $parameters = http_build_query([
								'referenceIds'              => [$scheduleEvent->referenceId],
                                '_locale'                   => $locale,
                                'lightenbody-api-source'    => $apiSource
                            ]); ?>
                            <?php $url = sprintf('https://studio.lightenbody.com/%s/frontoffice,iframe/delegate?%s', $uuid, $parameters); ?>
                            <!-- you can process the link via iframe or popup -->
                            <a href="<?php echo $url; ?>">Book now</a>
                        </td>
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
