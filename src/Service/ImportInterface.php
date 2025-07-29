<?php

namespace App\Service;

use Symfony\Component\Console\Helper\ProgressBar;

interface ImportInterface
{
    /**
     * Import the given source.
     *
     * @param string $src
     *   Path to the source
     */
    public function import(string $src, ?ProgressBar $progressBar = null): void;
}
