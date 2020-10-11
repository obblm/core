<?php

namespace Obblm\Core\DataTransformer;

use Obblm\Core\Contracts\RuleHelperInterface;
use Obblm\Core\Contracts\SkillInterface;
use Symfony\Component\Form\DataTransformerInterface;

class StringToSkill implements DataTransformerInterface
{
    private $helper;

    public function __construct(RuleHelperInterface $helper)
    {
        $this->helper = $helper;
    }

    /**
     * @param mixed $value
     * @return SkillInterface|void
     */
    public function transform($value)
    {
        if (!$value && !is_string($value)) {
            return;
        }

        return $this->helper->getSkill($value);
    }

    public function reverseTransform($value)
    {
        if (!$value && !($value instanceof SkillInterface)) {
            return;
        }

        return $value->getKey();
    }
}
