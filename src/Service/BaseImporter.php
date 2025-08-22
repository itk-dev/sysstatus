<?php

namespace App\Service;

use App\Repository\GroupRepository;
use App\Repository\ReportRepository;
use App\Repository\SystemRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

abstract class BaseImporter implements ImportInterface
{
    protected string $url;

    public function __construct(
        protected ReportRepository $reportRepository,
        protected SystemRepository $systemRepository,
        protected GroupRepository $groupRepository,
        protected EntityManagerInterface $entityManager,
        protected ParameterBagInterface $params,
    ) {
        $this->url = $this->params->get('system_url') ?? '';
    }

    protected function sanitizeText(string $str): ?string
    {
        $str = strip_tags($str, '<p><div><strong><a><ul><li><span><br><br/>');

        $str = preg_replace("/<([a-z][a-z0-9]*)(?:[^>]*(\shref=['\"][^'\"]*['\"]))?[^>]*?(\/?)>/i", '<$1$2$3>', $str);
        $str = preg_replace("#(<\s*a\s+[^>]*href\s*=\s*[\"'])(?!http|mailto)([^\"'>]+)([\"'>]+)#", '$1'.$this->url.'$2$3', (string) $str);

        return $str;
    }

    /**
     * @param array<int,mixed>|null $list
     *
     * @return string|null
     */
    protected function convertList(?array $list): ?string
    {
        if ($list) {
            return implode(', ', $list);
        }

        return '';
    }

    /**
     * @param $obj
     *
     * @return string|null
     */
    protected function convertLink(?object $obj): ?string
    {
        if ($obj && $obj->Url && $obj->Description) {
            return '<a href="'.$obj->Url.'">'.$obj->Description.'</a>';
        }

        return '';
    }

    /**
     * @throws \Exception
     */
    protected function convertDate(string $date): \DateTime
    {
        return new \DateTime($date);
    }

    /**
     * @param array<int,object> $systemOwner
     *
     * @return string
     */
    protected function convertSystemOwner(array $systemOwner): string
    {
        return $systemOwner[0]->LookupValue ?? '';
    }

    protected function convertBoolean(string $str): bool
    {
        return 'true' == $str;
    }
}
