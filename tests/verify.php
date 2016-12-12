<?php

require_once '../vendor/autoload.php';

use GuzzleHttp\Pool;
use Bramus\Ansi\ControlSequences\EscapeSequences\Enums\SGR;

class Verify extends \Tws\Test
{
    public function test($requestsCount, $concurrency, $job)
    {
        $requests = function ($total) use ($job) {
            $uri = 'http://localhost:8080/obtain_execution_lock/' . $job;
            for ($i = 0; $i < $total; $i++) {
                yield function() use ($uri) {
                    return $this->client->getAsync($uri);
                };
            }
        };

        $responses['OK']       = [];
        $responses['FAIL']     = [];
        $responses['REJECTED'] = [];

        $pool = new Pool($this->client, $requests($requestsCount), [
            'concurrency' => $concurrency,
            'fulfilled' => function ($response, $index) use (&$responses, $job) {
                $content = json_decode($response->getBody()->getContents());
                switch ($content->message) {
                    case 'OK':
                        $responses['OK'][$index] = $content->message;

                        $this->client->request('GET', 'http://localhost:8080/release_execution_lock/' . $job);
                        break;
                    case 'FAIL':
                        $responses['FAIL'][$index] = $content->message;
                        break;
                }
            },
            'rejected' => function ($reason, $index) use (&$responses) {
                $responses['FAIL'][$index] = $reason;
            },
        ]);

        $promise = $pool->promise();
        $promise->wait();

        $this->ansi->color(array(SGR::COLOR_FG_GREEN))
            ->text('Test case (' . $requestsCount . ' requests, ' . $concurrency . ' concurrent). Result: ' . count($responses['OK']) . ' requests with OK response. ' . count($responses['FAIL']) . ' requests with FAIL response. ' . count($responses['REJECTED']) . ' requests rejected.' . PHP_EOL);
    }
}

$verify = new Verify();

/** Test case (200 requests, 50 concurrent) Job type: A */
$verify->test(200, 50, 'A');

/** Test case (500 requests, 100 concurrent) Job type: A */
$verify->test(500, 100, 'A');

/** Test case (150 requests, 150 concurrent) Job type: A */
$verify->test(150, 150, 'A');

/** Test case (100 requests, 20 concurrent) Job type: B */
$verify->test(100, 20, 'B');

/** Test case (100 requests, 20 concurrent) Job type: B */
$verify->test(30, 30, 'B');