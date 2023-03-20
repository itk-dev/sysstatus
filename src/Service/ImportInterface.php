<?php


namespace App\Service;

interface ImportInterface
{
    /**
     * Import the given source.
     *
     * @param string $src
     *   Path to the source
     */
    function import(string $src);
}
