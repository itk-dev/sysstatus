<?php

namespace App\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class AppExtension extends AbstractExtension
{
    public function getFunctions() {
        return array(
            new TwigFunction('getclass', array($this, 'getClass')),
        );
    }

    /**
     * @param $instance
     * @return bool
     */
    public function getClass($instance)
    {
        return get_class($instance);
    }

}
