<?php

declare(strict_types=1);

namespace Obblm\Core\Tests\Domain\Command;

use Obblm\Core\Domain\Command\AbstractCommand;
use Obblm\Core\Domain\Command\Coach\CreateCoachCommand;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;

class CreateCoachCommandTest extends TestCase
{
    public function testFromArray()
    {
        $data = [
            'email' => random_bytes(8).'@'.random_bytes(8).'.com',
            'username' => random_bytes(8),
            'plainPassword' => random_bytes(8),
        ];
        $command = AbstractCommand::fromArray(CreateCoachCommand::class, $data);

        self::assertEquals($data['email'], $command->getEmail());
        self::assertEquals($data['username'], $command->getUsername());
        self::assertEquals($data['plainPassword'], $command->getPlainPassword());
    }
}
