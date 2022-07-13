<?php

declare(strict_types=1);

namespace Obblm\Core\Domain\Command\Coach;

use Obblm\Core\Domain\Command\CommandInterface;
use Obblm\Core\Domain\Validator\Constraints as ObblmAssert;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Constraints as Assert;

final class CreateCoachCommand implements CommandInterface
{
    /**
     * @Assert\NotBlank()
     * @ObblmAssert\Coach\UniqueEmail()
     */
    private string $email;
    /**
     * @Assert\NotBlank()
     * @ObblmAssert\Coach\UniqueUsername()
     */
    private string $username;
    /**
     * @Assert\NotBlank()
     */
    private string $plainPassword;

    /**
     * CreateCoachCommand constructor.
     */
    public function __construct(
        string $email,
        string $username,
        string $plainPassword
    ) {
        $this->email = $email;
        $this->username = $username;
        $this->plainPassword = $plainPassword;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function getUsername(): string
    {
        return $this->username;
    }

    public function getPlainPassword(): string
    {
        return $this->plainPassword;
    }

    public static function fromArray($data): CreateCoachCommand
    {
        return new CreateCoachCommand(
            $data['email'],
            $data['username'],
            $data['plainPassword']
        );
    }

    public static function fromRequest(Request $request): CreateCoachCommand
    {
        return new CreateCoachCommand(
            $request->get('email'),
            $request->get('username'),
            $request->get('plainPassword')
        );
    }
}
