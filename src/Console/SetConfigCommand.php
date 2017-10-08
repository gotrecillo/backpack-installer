<?php

namespace Gotrecillo\BackpackInstaller\Console;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class SetConfigCommand extends BaseCommand
{
    public function configure()
    {
        $this->setName('config:set')
            ->setDescription('Set a config value');
    }

    public function execute(InputInterface $input, OutputInterface $output)
    {
        $this->setupCommand();

        $this->cli->info("Let's modify the configuration...");

        do {
            $this->setKey();
            $this->cli->clear();
            $continue = $this->cli->green()->confirm('Do you want to change another value')->confirmed();
        } while ($continue);

    }

    private function setKey()
    {
        $options = $this->config->getOptions();

        $keys = array_keys($options);

        do {
            $key = $this->cli->lightYellow()->radio('What key do you want to modify?', $keys)->prompt();
        } while (!$key);

        do {
            $value = $this->cli->lightYellow()->input('What is the new value?')->prompt();
        } while (!$value);

        $this->config->updateOption($key, $value);
    }
}
