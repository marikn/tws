<?php

namespace Tws;

use GuzzleHttp\Client;
use Bramus\Ansi\Ansi;
use Bramus\Ansi\Writers\StreamWriter;

abstract class Test
{
    protected $client;
    protected $ansi;

    public function __construct()
    {
        $this->client = new Client();
        $this->ansi   = new Ansi(new StreamWriter('php://stdout'));
    }
}