<?php

declare(strict_types=1);

namespace Obblm\Core\Tests\Domain\Validator;

use PHPUnit\Framework\TestCase;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Context\ExecutionContext;
use Symfony\Component\Validator\Violation\ConstraintViolationBuilder;

abstract class AbstractValidatorTest extends TestCase
{
    /**
     * Retourne une instance du validateur à tester.
     */
    abstract protected function getValidatorInstance(): ConstraintValidator;

    /**
     * Initialise le validateur.
     *
     * @param string|null $expectedMessage le message de violation attendu (null si on attend que la valeur soit
     *                                     validée)
     */
    protected function initValidator($expectedMessage = null): ConstraintValidator
    {
        $builder = $this->getMockBuilder(ConstraintViolationBuilder::class)
            ->disableOriginalConstructor()
            ->setMethods(['addViolation'])
            ->getMock();

        $context = $this->getMockBuilder(ExecutionContext::class)
            ->disableOriginalConstructor()
            ->setMethods(['buildViolation'])
            ->getMock();

        if ($expectedMessage) {
            $builder->expects($this->once())->method('addViolation');

            $context->expects($this->once())
                ->method('buildViolation')
                ->with($this->equalTo($expectedMessage))
                ->will($this->returnValue($builder));
        } else {
            $context->expects($this->never())->method('buildViolation');
        }

        $validator = $this->getValidatorInstance();
        /* @var ExecutionContext $context */
        $validator->initialize($context);

        return $validator;
    }
}
