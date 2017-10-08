<?php

namespace Gotrecillo\BackpackInstaller\Packages;

class LaracastsGenerators extends Package
{
    public function setup()
    {
        $this->setSlug('laracasts/generators:dev-master --dev');
        $this->setName('Laracasts Generators');
    }
}
