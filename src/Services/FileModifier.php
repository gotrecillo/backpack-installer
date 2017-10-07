<?php

namespace Backpack\Install\Services;

use League\Flysystem\MountManager;

class FileModifier
{
    private $mountManager;

    public function __construct(MountManager $mountManager)
    {
        $this->mountManager = $mountManager;
    }

    public function addAtEnd($path, $newContent)
    {
        $content = $this->mountManager->read($path);
        $newContent = $content . PHP_EOL . $newContent . PHP_EOL;
        $this->mountManager->update($path, $newContent);
    }

    public function addAfterHook($path, $hook, $newContent, $padding = 0)
    {
        $padStart = str_repeat(' ', $padding);
        $paddedNewContent = strtr($newContent, [PHP_EOL => PHP_EOL . $padStart]);
        $updatedContent = $hook . PHP_EOL . $padStart . $paddedNewContent;

        $this->updateContent($path, $hook, $updatedContent);
    }

    private function updateContent($path, $hook, $newContent)
    {
        $content = $this->mountManager->read($path);

        $updatedContent = strtr($content, [
            $hook => $newContent
        ]);

        $this->mountManager->update($path, $updatedContent);
    }

    public function addBeforeHook($path, $hook, $newContent, $padding = 0)
    {
        $padStart = str_repeat(' ', $padding);
        $paddedNewContent = strtr($newContent, [PHP_EOL => PHP_EOL . $padStart]);
        $updatedContent = $paddedNewContent . PHP_EOL . $padStart . $hook;

        $this->updateContent($path, $hook, $updatedContent);
    }

    public function removeHook($path, $hook)
    {
        $content = $this->mountManager->read($path);

        $updatedContent = strtr($content, [$hook => '']);

        $this->mountManager->update($path, $updatedContent);
    }
}