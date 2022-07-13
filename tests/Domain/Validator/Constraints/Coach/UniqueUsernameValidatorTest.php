<?php

declare(strict_types=1);

namespace Obblm\Core\Tests\Domain\Validator\Constraints\Coach;

use Obblm\Core\Domain\Service\Coach\CoachService;
use Obblm\Core\Domain\Validator\Constraints\Coach\UniqueUsername;
use Obblm\Core\Domain\Validator\Constraints\Coach\UniqueUsernameValidator;
use Obblm\Core\Tests\Domain\Validator\AbstractValidatorTest;
use Symfony\Component\Validator\ConstraintValidator;

class UniqueUsernameValidatorTest extends AbstractValidatorTest
{
    protected CoachService $service;

    public function setUp(): void
    {
        $this->service = $this->createMock(CoachService::class);
    }

    protected function getValidatorInstance(): ConstraintValidator
    {
        return new UniqueUsernameValidator($this->service);
    }

    public function testValidationOk()
    {
        $constraint = new UniqueUsername();
        $validator = $this->initValidator();

        $validator->validate('test', $constraint);

        $this->assertSame(get_class($validator), $constraint->validatedBy());
    }

    public function testValidationKo()
    {
        $this->service->expects($this->any())
            ->method('isUsernameExists')
            ->willReturn(true);

        $constraint = new UniqueUsername();
        $validator = $this->initValidator($constraint::MESSAGE);

        $validator->validate('test', $constraint);
    }
}
