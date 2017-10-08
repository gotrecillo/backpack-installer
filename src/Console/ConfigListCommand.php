<?php

namespace Gotrecillo\BackpackInstaller\Console;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ConfigListCommand extends BaseCommand
{
    public function configure()
    {
        $this->setName('config:list')
            ->setDescription('Shows the current configuration');
    }

    public function execute(InputInterface $input, OutputInterface $output)
    {
        $this->setupCommand();

        $options = $this->config->getOptions();

        $tableOptions = [];

        foreach ($options as $key => $value) {
            $tableOptions[] = ['key' => $key, 'value' => $this->normalizeValue($value)];
        }

        $this->cli->green()->table($tableOptions);
    }

    private function normalizeValue($value)
    {
        if ($value === true) {
            return 'true';
        }

        if ($value === false) {
            return 'false';
        }

        return $value;
    }


}