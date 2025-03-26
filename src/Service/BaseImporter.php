<?php

namespace App\Service;

use App\Repository\GroupRepository;
use App\Repository\ReportRepository;
use App\Repository\SystemRepository;
use Doctrine\ORM\EntityManagerInterface;

abstract class BaseImporter implements ImportInterface
{
    protected string $url;

    public function __construct(
        protected ReportRepository $reportRepository,
        protected SystemRepository $systemRepository,
        protected GroupRepository $groupRepository,
        protected EntityManagerInterface $entityManager,
    ) {
        $this->url = getenv('SYSTEM_URL');
    }

    protected function sanitizeText(string $str): ?string
    {
        $str = strip_tags($str, '<p><div><strong><a><ul><li><span><br><br/>');

        $str = preg_replace("/<([a-z][a-z0-9]*)(?:[^>]*(\shref=['\"][^'\"]*['\"]))?[^>]*?(\/?)>/i", '<$1$2$3>', $str);
        $str = preg_replace("#(<\s*a\s+[^>]*href\s*=\s*[\"'])(?!http|mailto)([^\"'>]+)([\"'>]+)#", '$1'.$this->url.'$2$3', (string) $str);

        return $str;
    }

    /**
     * @throws \Exception
     */
    protected function convertDate(string $date): \DateTime
    {
        return new \DateTime($date);
    }

    protected function convertBoolean(string $str): bool
    {
        return 'true' == $str;
    }
}
