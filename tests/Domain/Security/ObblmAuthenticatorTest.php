<?php

declare(strict_types=1);

namespace Obblm\Core\Tests\Domain\Security;

use Obblm\Core\Domain\Exception\AuthenticationFailureException;
use Obblm\Core\Domain\Exception\InactiveCoachException;
use Obblm\Core\Domain\Model\Coach;
use Obblm\Core\Domain\Security\ObblmAuthenticator;
use Obblm\Core\Domain\Service\Coach\CoachService;
use Obblm\Core\Tests\Fixtures\Doctrine\CoachBuilder;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasher;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\InvalidCsrfTokenException;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;

class ObblmAuthenticatorTest extends KernelTestCase
{
    protected ?Coach $expectedCoach;
    protected ?ObblmAuthenticator $auth;
    protected ?UrlGeneratorInterface $urlGenerator;
    protected ?UserPasswordHasherInterface $hasher;

    public function setUp(): void
    {
        static::bootKernel();
        $this->expectedCoach = CoachBuilder::for(static::getContainer())->build();

        $this->urlGenerator = $this->createMock(UrlGeneratorInterface::class);
        $csrfTokenManager = static::getContainer()->get(CsrfTokenManagerInterface::class);
        $this->hasher = self::createMock(UserPasswordHasher::class);

        $this->auth = new ObblmAuthenticator($this->urlGenerator, $csrfTokenManager, $this->hasher);
    }

    /**
     * @test
     */
    public function testCredentialsFromRequest()
    {
        $login = random_bytes(8);
        $password = random_bytes(8);
        $token = random_bytes(8);

        $query = [
            'login' => $login,
            'password' => $password,
            '_csrf_token' => $token,
        ];

        $request = Request::create('/login', 'POST', $query);
        $request->setSession(static ::$container->get(SessionInterface::class));
        $request->attributes->set('_route', 'obblm.login');

        $credentials = $this->auth->getCredentials($request);

        self::assertSame([
            'login' => $login,
            'password' => $password,
            'csrf_token' => $token,
        ], $credentials);
    }

    /**
     * @test
     */
    public function testSupportsRequestOK()
    {
        $request = Request::create('/login', 'POST', []);
        $request->setSession(static::getContainer()->get(SessionInterface::class));
        $request->attributes->set('_route', 'obblm.login');
        self::assertEquals(true, $this->auth->supports($request));
    }

    /**
     * @test
     */
    public function testSupportsRequestKO()
    {
        $request = Request::create('/register', 'POST', []);
        $request->setSession(static::getContainer()->get(SessionInterface::class));
        $request->attributes->set('_route', 'obblm.register');
        self::assertEquals(false, $this->auth->supports($request));
    }

    /**
     * @test
     */
    public function testGetUserOK()
    {
        $tokenManager = static::getContainer()->get(CsrfTokenManagerInterface::class);
        $credentials = [
            'login' => $this->expectedCoach->getUsername(),
            'password' => $this->expectedCoach->getPlainPassword(),
            'csrf_token' => $tokenManager->getToken('authenticate')->getValue(),
        ];

        self::assertEquals($this->expectedCoach, $this->auth->getUser($credentials, $this->mockService()));
    }

    /**
     * @test
     */
    public function testGetUserThrowTokenException()
    {
        $tokenManager = static::getContainer()->get(CsrfTokenManagerInterface::class);
        $credentials = [
            'login' => $this->expectedCoach->getUsername(),
            'password' => $this->expectedCoach->getPlainPassword(),
            'csrf_token' => $tokenManager->getToken('test')->getValue(),
        ];
        self::expectException(InvalidCsrfTokenException::class);
        $this->auth->getUser($credentials, $this->mockService());
    }

    /**
     * @test
     */
    public function testGetUserThrowInvalidUserException()
    {
        $tokenManager = static::getContainer()->get(CsrfTokenManagerInterface::class);
        $credentials = [
            'login' => $this->expectedCoach->getUsername(),
            'password' => 'test',
            'csrf_token' => $tokenManager->getToken('authenticate')->getValue(),
        ];

        self::expectException(AuthenticationFailureException::class);
        self::expectErrorMessage(AuthenticationFailureException::MESSAGE);
        $this->auth->getUser($credentials, $this->mockService(true));
    }

    /**
     * @test
     */
    public function testGetUserThrowInactiveUserException()
    {
        $tokenManager = static::getContainer()->get(CsrfTokenManagerInterface::class);
        $credentials = [
            'login' => $this->expectedCoach->getUsername(),
            'password' => 'test',
            'csrf_token' => $tokenManager->getToken('authenticate')->getValue(),
        ];

        $this->expectedCoach->setActive(false);

        self::expectException(InactiveCoachException::class);
        self::expectErrorMessage(InactiveCoachException::MESSAGE);
        $this->auth->getUser($credentials, $this->mockService());
    }

    /**
     * @test
     */
    public function testSupportsRequestMethodKO()
    {
        $request = Request::create('/login', 'GET', []);
        $request->setSession(static ::$container->get(SessionInterface::class));
        $request->attributes->set('_route', 'obblm.login');
        self::assertEquals(false, $this->auth->supports($request));
    }

    /**
     * @test
     * @when I check password with a wrong password
     * @then Method returns false
     */
    public function testCheckInvalidCredentials()
    {
        $this->hasher->expects($this->once())->method('isPasswordValid')->willReturn(false);
        $check = $this->auth->checkCredentials([
            'password' => random_bytes(8),
        ], $this->expectedCoach);
        self::assertEquals(false, $check);
    }

    /**
     * @test
     * @when I check password with a good password
     * @then Method returns true
     */
    public function testCheckValidCredentials()
    {
        $this->hasher->expects($this->once())->method('isPasswordValid')->willReturn(true);
        $check = $this->auth->checkCredentials([
            'password' => $this->expectedCoach->getPlainPassword(),
        ], $this->expectedCoach);
        self::assertEquals(true, $check);
    }

    /**
     * @test
     */
    public function testGetPassword()
    {
        $check = $this->auth->getPassword([
            'password' => $this->expectedCoach->getPlainPassword(),
        ]);
        self::assertEquals($this->expectedCoach->getPlainPassword(), $check);
    }

    /**
     * @test
     */
    public function testAuthenticationSuccess()
    {
        $home = '/';
        $this->urlGenerator->expects($this->any())
            ->method('generate')
            ->willReturn($home);

        // Test empty redirect
        $request = new Request();
        $request->setSession(static ::$container->get(SessionInterface::class));
        $check = $this->auth->onAuthenticationSuccess(
            $request,
            $this->createMock(TokenInterface::class),
            'test_auth'
        );
        self::assertInstanceOf(RedirectResponse::class, $check);
        self::assertEquals($home, $check->getTargetUrl());

        // Test _target_path redirect
        $path = '/redirect_target_path';
        $request = new Request([], [
            '_target_path' => $path,
        ]);
        $request->setSession(static ::$container->get(SessionInterface::class));

        $check = $this->auth->onAuthenticationSuccess(
            $request,
            $this->createMock(TokenInterface::class),
            'test_auth'
        );
        self::assertInstanceOf(RedirectResponse::class, $check);
        self::assertEquals($path, $check->getTargetUrl());

        // Test povided_key in session redirect
        $key = 'povided_key';
        $path = '/redirect_povided_key_path';
        $request = new Request();
        /** @var SessionInterface $session */
        $session = static ::$container->get(SessionInterface::class);
        $session->set('_security.'.$key.'.target_path', $path);
        $request->setSession($session);

        $check = $this->auth->onAuthenticationSuccess(
            $request,
            $this->createMock(TokenInterface::class),
            $key
        );
        self::assertInstanceOf(RedirectResponse::class, $check);
        self::assertEquals($path, $check->getTargetUrl());
    }

    private function mockService($nullUser = false)
    {
        $service = $this->createMock(CoachService::class);

        $service->expects($this->any())
            ->method('get')
            ->willReturn($nullUser ? null : $this->expectedCoach);

        $service->expects($this->any())
            ->method('loadUserByUsername')
            ->willReturn($nullUser ? null : $this->expectedCoach);

        return $service;
    }

    public function tearDown(): void
    {
        parent::tearDown();
        static::ensureKernelShutdown();
        static::$kernel = null;
        $this->auth = null;
        $this->expectedCoach = null;
    }
}
