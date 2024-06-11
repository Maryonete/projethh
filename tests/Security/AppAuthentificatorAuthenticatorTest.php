<?php

use App\Entity\User;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Security\AppAuthentificatorAuthenticator;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\User\InMemoryUserProvider;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\CsrfTokenBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Credentials\PasswordCredentials;

class AppAuthentificatorAuthenticatorTest extends TestCase
{
    // public function testAuthenticate()
    // {
    //     $urlGenerator = $this->createMock(UrlGeneratorInterface::class);
    //     $authenticator = new AppAuthentificatorAuthenticator($urlGenerator);

    //     $request = Request::create('/login', 'POST', [], [], [], [], '{"email": "test@example.com", "password": "password123", "_csrf_token": "token123"}');

    //     // Mock session
    //     $session = $this->createMock(SessionInterface::class);
    //     $session->expects($this->once())
    //         ->method('set')
    //         ->with('_security.last_username', 'test@example.com');

    //     $request->setSession($session);

    //     // Créer un fournisseur d'utilisateurs pour fournir un utilisateur fictif
    //     $user = new User('test@example.com', 'password123');
    //     $userProvider = new InMemoryUserProvider(['test@example.com' => $user]);

    //     $passport = $authenticator->authenticate($request);

    //     // Utilisez le fournisseur d'utilisateurs pour charger l'utilisateur à partir du badge
    //     $userBadge = $passport->getUserBadge();
    //     $userLoader = $userProvider->loadUserByIdentifier($userBadge->getUserIdentifier());
    //     $this->assertEquals($user, $userLoader); // Vérifie que l'utilisateur chargé est correct

    //     // Vérifie les autres aspects du passeport
    //     $credentials = $request->request->all();
    //     $this->assertEquals('password123', $credentials['password']); // Vérifiez le mot de passe
    //     $badges = $passport->getBadges();
    //     $this->assertCount(1, $badges);
    //     $this->assertInstanceOf(CsrfTokenBadge::class, $badges[0]);
    // }

    // public function testSupports()
    // {
    //     $urlGenerator = $this->createMock(UrlGeneratorInterface::class);
    //     $authenticator = new AppAuthentificatorAuthenticator($urlGenerator);

    //     $request = new Request([], [], [], [], [], ['REQUEST_URI' => '/']);

    //     // POST request and request URI matches login URL
    //     $request->setMethod('POST');
    //     $this->assertTrue($authenticator->supports($request));

    //     // GET request
    //     $request->setMethod('GET');
    //     $this->assertFalse($authenticator->supports($request));

    //     // POST request but request URI doesn't match login URL
    //     $request->setMethod('POST');
    //     $request->attributes->set('_route', 'other_route');
    //     $this->assertFalse($authenticator->supports($request));
    // }

    // You can write similar tests for onAuthenticationSuccess method and getLoginUrl method
}
