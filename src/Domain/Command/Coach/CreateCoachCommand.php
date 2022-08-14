<?php

declare(strict_types=1);

namespace Obblm\Core\Domain\Command\Coach;

use Obblm\Core\Domain\Command\AbstractCommand;
use Obblm\Core\Domain\Command\CommandInterface;
use Obblm\Core\Domain\Validator\Constraints as ObblmAssert;
use Symfony\Component\Validator\Constraints as Assert;

final class CreateCoachCommand extends AbstractCommand implements CommandInterface
{
    public const CONSTRUCTOR_ARGUMENTS = ['email', 'username', 'plainPassword', 'locale'];

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

    private string $locale = 'en';

    /**
     * CreateCoachCommand constructor.
     */
    public function __construct(
        string $email,
        string $username,
        string $plainPassword,
        string $locale = 'en'
    ) {
        $this->email = $email;
        $this->username = $username;
        $this->plainPassword = $plainPassword;
        $this->locale = $locale;
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

    public function getLocale(): string
    {
        return $this->locale;
    }
}
