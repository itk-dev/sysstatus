<?php

namespace App\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class AppExtension extends AbstractExtension
{
    public function getFunctions() {
        return array(
            new TwigFunction('getclass', array($this, 'getClass')),
            new TwigFunction('getanswer', array($this, 'getAnswer')),
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

    public function getAnswer($entity, $question) {
        $answers = $entity->getAnswers();

        foreach ($answers as $answer) {
            if ($answer->getQuestion()->getId() == $question->getId()) {
                return $answer;
            }
        }

        return null;
    }
}
