<?php

namespace App\Service;

use App\Repository\GroupRepository;
use App\Repository\ReportRepository;
use App\Repository\SystemRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Helper\ProgressBar;
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

    public function import(string $src, ?ProgressBar $progressBar = null): void
    {
        // We need to be able to find all entities during import.
        $filters = $this->entityManager->getFilters();
        if ($filters->isEnabled('entity_active')) {
            $filters->disable('entity_active');
        }

        $this->doImport($src, $progressBar);
    }

    abstract protected function doImport(string $src, ?ProgressBar $progressBar): void;

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
            return '<a href="'.htmlspecialchars($obj->Url).'">'.$obj->Description.'</a>';
        }

        return '';
    }

    /**
     * @throws \Exception
     */
    protected function convertDate(string $date): ?\DateTimeInterface
    {
        return empty($date) ? null : new \DateTimeImmutable($date);
    }

    /**
     * @param array<int,object> $systemOwner
     *
     * @return string
     */
    protected function convertSystemOwner(array $systemOwner): string
    {
        if (empty($systemOwner)) {
            return '';
        }

        return $systemOwner[0]->LookupValue ?? '';
    }

    protected function convertBoolean(string $str): bool
    {
        return 'true' == $str;
    }
}
