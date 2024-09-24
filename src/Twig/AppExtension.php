<?php

namespace App\Twig;

use Doctrine\Common\Collections\ArrayCollection;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use Twig\TwigFunction;

class AppExtension extends AbstractExtension
{
    public function getFunctions()
    {
        return [
            new TwigFunction('getclass', [$this, 'getClass']),
            new TwigFunction('getanswer', [$this, 'getAnswer']),
            new TwigFunction('breakintolines', [$this, 'breakIntoLines']),
        ];
    }

    public function getFilters()
    {
        return [
            new TwigFilter('sort_order', [$this, 'sortOrder']),
        ];
    }

    /**
     * @return bool
     */
    public function getClass($instance)
    {
        return get_class($instance);
    }

    public function getAnswer($entity, $question)
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
     * From: http://php.net/manual/en/function.str-split.php#107658.
     *
     * @param int $l
     *
     * @return array|array[]|false|string[]
     */
    private function str_split_unicode($str, $l = 0)
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
    public function breakIntoLines($text, $chuckSize, $numberOfLines)
    {
        $split = $this->str_split_unicode($text, $chuckSize);
        $numberOfSplits = count($split);

        $render = [];

        $addedEmptyLines = 0;
        for ($addedEmptyLines; $addedEmptyLines < $numberOfLines - min($numberOfSplits, $numberOfLines); ++$addedEmptyLines) {
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
     * @return ArrayCollection
     */
    public function sortOrder($item)
    {
        $iterator = $item->getIterator();

        $iterator->uasort(function ($a, $b) {
            return ($a->getSortOrder() > $b->getSortOrder()) ? -1 : 1;
        });

        return new ArrayCollection(iterator_to_array($iterator));
    }
}
