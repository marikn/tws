<?php

require_once '../vendor/autoload.php';

use GuzzleHttp\Pool;
use GuzzleHttp\Client;
use Bramus\Ansi\ControlSequences\EscapeSequences\Enums\SGR;

class Performance extends \Tws\Test
{
    public function test($requestsCount, $concurrency, $job, $time)
    {
        $startTime                 = time();
        $successfullyRequestsCount = 0;
        $testCaseExecutionTime     = 0;
        $totalExecutionTime        = 0;
        $requestsTotal             = 0;

        while (true) {
            if ((time() - $startTime) <= $time) {
                $client = new Client();

                $requests = function ($total) use ($client, $job, &$testCaseExecutionTime) {
                    $uri = 'http://localhost:8080/obtain_execution_lock/' . $job;
                    for ($i = 0; $i < $total; $i++) {
                        yield function() use ($client, $uri, &$testCaseExecutionTime) {
                            return $client->getAsync($uri, ['on_stats' => function (\GuzzleHttp\TransferStats $stats) use (&$testCaseExecutionTime) {
                                $testCaseExecutionTime += $stats->getTransferTime();
                            }]);
                        };
                    }
                };

                $responses['OK']        = [];
                $responses['FAIL']      = [];
                $responses['REJECTED']  = [];

                $requestsTotal += 100;

                $pool = new Pool($client, $requests($requestsCount), [
                    'concurrency' => $concurrency,
                    'fulfilled' => function ($response, $index) use ($job, &$responses, &$successfullyRequestsCount) {
                        $content = json_decode($response->getBody()->getContents());
                        switch ($content->message) {
                            case 'OK':
                                $successfullyRequestsCount++;

                                $responses['OK'][$index] = $content->message;

                                $this->client->request('GET', 'http://localhost:8080/release_execution_lock/' . $job);
                                break;
                            case 'FAIL':
                                $responses['FAIL'][$index] = $content->message;
                                break;
                        }
                    },
                    'rejected' => function ($reason, $index) use (&$responses) {
                        $responses['REJECTED'][$index] = $reason;
                    },
                ]);

                $promise = $pool->promise();
                $promise->wait();

                $totalExecutionTime += $testCaseExecutionTime;

                $this->ansi->color(array(SGR::COLOR_FG_GREEN))
                    ->text(time() - $startTime . " seconds spent. $requestsCount requests processed. TC requests execution time: " . round($testCaseExecutionTime, 2) . " seconds. Average time per request: " . round(($testCaseExecutionTime/100) * 1000, 2) . " milliseconds" . PHP_EOL);

                $testCaseExecutionTime = 0;
            } else {
                break;
            }
        }

        $this->ansi->color(array(SGR::COLOR_FG_GREEN))
            ->text(PHP_EOL . "Time end. $requestsTotal requests processed. Total requests execution time: " . round($totalExecutionTime, 2) . " seconds. Average time per request: " . round(($totalExecutionTime/$requestsTotal) * 1000, 2) . " milliseconds" . PHP_EOL);

    }
}

$performance = new Performance();
$performance->test(100, 100, 'A', 300);