<?php


namespace Backpack\Install\Services;


class Environment
{
    private $fileModifier;

    public function __construct(FileModifier $fileModifier)
    {
        $this->fileModifier = $fileModifier;
    }

    public function setKey($key, $value)
    {
        $line = $key . '=' . $value;
        $this->fileModifier->addAtEnd('project://.env', $line);
        $this->fileModifier->addAtEnd('project://.env.example', $line);
    }

    public function addChanges($changes)
    {
        foreach ($changes as $key => $value) {
            $this->setKey($key, $value);
        }
    }

}