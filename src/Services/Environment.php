<?php


namespace Gotrecillo\BackpackInstaller\Services;


use League\Flysystem\MountManager;

class Environment
{
    private $fileModifier;
    private $mountManager;

    public function __construct(FileModifier $fileModifier, MountManager $mountManager)
    {
        $this->fileModifier = $fileModifier;
        $this->mountManager = $mountManager;
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

    public function updateKey($key, $value)
    {
        $file = $this->mountManager->read('project://.env');
        $originalLines = explode(PHP_EOL, $file);
        $lines = array_map(function ($line) use($key, $value) {
            if(preg_match("/^$key=.*/", $line)){
                return $key. '='. $value;
            }
            return $line;
        }, $originalLines);

        $newContent = implode(PHP_EOL, $lines);

        $this->mountManager->update('project://.env', $newContent);
        $this->mountManager->update('project://.env.example', $newContent);
    }

}