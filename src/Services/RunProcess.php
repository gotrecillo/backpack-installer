<?php

namespace Gotrecillo\BackpackInstaller\Services;

use League\CLImate\CLImate;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Process;

class RunProcess
{
    private $cli;

    public function __construct(CLImate $cli)
    {
        $this->cli = $cli;
    }

    public function run($command)
    {
        $this->cli->br()->yellow($command);

        $process = new Process($command);
        $process->setTimeout(500);

        $process->run(function ($type, $buffer) {
            echo $buffer;
        });

        if ($process->isSuccessful() === false) {
            throw new ProcessFailedException($process);
        }
    }
}