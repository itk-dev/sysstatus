<?php

namespace App\Service;

use Doctrine\ORM\EntityManagerInterface;
use App\Repository\ReportRepository;
use App\Repository\SystemRepository;
use App\Repository\GroupRepository;

abstract class BaseImporter implements ImportInterface
{
    protected $entityManager;
    protected $reportRepository;
    protected $systemRepository;
    protected $groupRepository;
    protected $url;


    public function __construct(
        ReportRepository $reportRepository,
        SystemRepository $systemRepository,
        GroupRepository $groupRepository,
        EntityManagerInterface $entityManager
    ) {
        $this->reportRepository = $reportRepository;
        $this->systemRepository = $systemRepository;
        $this->groupRepository = $groupRepository;
        $this->entityManager = $entityManager;

        $this->url = getenv('SYSTEM_URL');
    }

    protected function sanitizeText(string $str) {
        $str = strip_tags($str, '<p><div><strong><a><ul><li><span><br><br/>');

        $str = preg_replace("/<([a-z][a-z0-9]*)(?:[^>]*(\shref=['\"][^'\"]*['\"]))?[^>]*?(\/?)>/i",'<$1$2$3>', $str);
        $str = preg_replace("#(<\s*a\s+[^>]*href\s*=\s*[\"'])(?!http|mailto)([^\"'>]+)([\"'>]+)#", '$1' . $this->url . '$2$3', $str);

        return $str;
    }

    protected function convertDate(string $date) {
        if (!is_string($date)) {
            return null;
        }

        $new = new \DateTime($date);

        return $new;
    }

    protected function convertBoolean(string $str) {
        return $str == 'true';
    }
}
