<?php

declare(strict_types=1);

namespace Obblm\Core\Tests\Fixtures\Doctrine;

use Obblm\Core\Domain\Model\Rule;
use Obblm\Core\Tests\Fixtures\BuilderInterface;

class RuleBuilder extends AbstractDoctrineBuilder implements BuilderInterface
{
    protected ?string $key = null;
    protected ?string $name = null;
    protected ?string $template = null;

    public function build(): Rule
    {
        $rule = (new Rule())
            ->setRuleKey($this->key ? $this->key : random_bytes(8))
            ->setName($this->name ? $this->name : random_bytes(8))
            ->setTemplate($this->template ? $this->template : random_bytes(8))
            ;

        $em = $this->container->get('doctrine')->getManager();
        $em->persist($rule);
        $em->flush();

        return $rule;
    }
}
