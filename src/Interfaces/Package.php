<?php

namespace Backpack\Install\Interfaces;

interface Package
{
    public function setup();
    public function install();
    public function setName($name);
    public function getName();
    public function setSlug($slug);
    public function getSlug();
}