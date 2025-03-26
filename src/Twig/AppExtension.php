<?php

namespace App\Twig;

use App\Entity\Question;
use Doctrine\Common\Collections\ArrayCollection;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use Twig\TwigFunction;

class AppExtension extends AbstractExtension
{
    #[\Override]
    public function getFunctions(): array
    {
        return [
            new TwigFunction('getclass', $this->getClass(...)),
            new TwigFunction('getanswer', $this->getAnswer(...)),
            new TwigFunction('breakintolines', $this->breakIntoLines(...)),
        ];
    }

    #[\Override]
    public function getFilters(): array
    {
        return [
            new TwigFilter('sort_order', $this->sortOrder(...)),
        ];
    }

    public function getClass(mixed $instance): bool
    {
        return $instance::class;
    }

    public function getAnswer(mixed $entity, Question $question): mixed
    {
        $answers = $entity->getAnswers();

        foreach ($answers as $answer) {
            if ($answer->getQuestion()->getId() == $question->getId()) {
                return $answer;
            }
        }

        return null;
    }

    /**
     * String split for unicode.
     *
     * From: http://php.net/manual/en/function.str-split.php#107658.
     *
     * @param string $str
     * @param int $l
     *
     * @return array<string>|false
     */
    private function str_split_unicode(string $str, int $l = 0): array|false
    {
        if ($l > 0) {
            $ret = [];
            $len = mb_strlen($str, 'UTF-8');
            for ($i = 0; $i < $len; $i += $l) {
                $ret[] = mb_substr($str, $i, $l, 'UTF-8');
            }

            return $ret;
        }

        return preg_split('//u', $str, -1, PREG_SPLIT_NO_EMPTY);
    }

    /**
     * Break the text into $numberOfLines of $chuckSize length. Prepends with
     *   empty lines.
     *
     * @return string
     */
    public function breakIntoLines(string $text, int $chuckSize, int $numberOfLines): string
    {
        $split = $this->str_split_unicode($text, $chuckSize);
        $numberOfSplits = count($split);

        $render = [];

        $addedEmptyLines = 0;
        for (; $addedEmptyLines < $numberOfLines - min($numberOfSplits, $numberOfLines); ++$addedEmptyLines) {
            $render[] = '';
        }

        for ($i = 0; $i < $numberOfLines - $addedEmptyLines; ++$i) {
            $render[] = $split[$i];
        }

        $result = implode('<br/>', $render);

        if ($numberOfSplits > $numberOfLines) {
            $result .= '...';
        }

        return $result;
    }

    /**
     * Sort by sortOrder.
     *
     * @param mixed $item
     *
     * @return mixed
     */
    public function sortOrder(mixed $item): mixed
    {
        $iterator = $item->getIterator();

        $iterator->uasort(static fn ($a, $b) => $a->getSortOrder() <=> $b->getSortOrder());

        return new ArrayCollection(iterator_to_array($iterator));
    }
}
