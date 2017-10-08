<?php

namespace Gotrecillo\BackpackInstaller\Services;

use Gotrecillo\BackpackInstaller\Interfaces\Package;
use League\CLImate\CLImate;

class PackageInstaller
{
    private $cli;

    public function __construct(CLImate $cli)
    {
        $this->cli = $cli;
    }

    public function install(Package $package)
    {
        $package->install();
    }
}