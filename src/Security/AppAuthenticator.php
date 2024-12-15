<?php

namespace App\Security;

use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAuthenticationException;
use Symfony\Component\Security\Http\Authenticator\AbstractLoginFormAuthenticator;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\CsrfTokenBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\RememberMeBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Credentials\PasswordCredentials;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;
use Symfony\Component\Security\Http\SecurityRequestAttributes;
use Symfony\Component\Security\Http\Util\TargetPathTrait;

class AppAuthenticator extends AbstractLoginFormAuthenticator
{
    use TargetPathTrait;

    public const LOGIN_ROUTE = 'app_login';
    private const MAX_ATTEMPTS = 3;

    public function __construct(private UrlGeneratorInterface $urlGenerator)
    {
    }

    public function authenticate(Request $request): Passport
    {
        // Vérifie le nombre de tentatives échouées
        $attempts = $request->getSession()->get('login_attempts', 0);

        if ($attempts >= self::MAX_ATTEMPTS) {
            $captchaResponse = $request->get('g-recaptcha-response');
            if (!$this->validateCaptcha($captchaResponse)) {
                throw new CustomUserMessageAuthenticationException('Captcha invalide. Réessayez.');
            }
        }

        $username = $request->get('username');
        $request->getSession()->set(SecurityRequestAttributes::LAST_USERNAME, $username);

        return new Passport(
            new UserBadge($username),
            new PasswordCredentials($request->get('password')),
            [
                new CsrfTokenBadge('authenticate', $request->get('_csrf_token')),
                new RememberMeBadge(),
            ]
        );
    }

    public function onAuthenticationFailure(Request $request, \Symfony\Component\Security\Core\Exception\AuthenticationException $exception): Response
    {
        // Incrémentation du nombre de tentatives échouées
        $attempts = $request->getSession()->get('login_attempts', 0) + 1;
        $request->getSession()->set('login_attempts', $attempts);

        return parent::onAuthenticationFailure($request, $exception);
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $firewallName): ?Response
    {
        // Réinitialisation des tentatives après un succès
        $request->getSession()->remove('login_attempts');

        if ($targetPath = $this->getTargetPath($request->getSession(), $firewallName)) {
            return new RedirectResponse($targetPath);
        }

        // Redirige vers la page d'accueil
        return new RedirectResponse($this->urlGenerator->generate('app_home')); // Remplacez 'app_home' par le nom de votre route de la page d'accueil
    }

    protected function getLoginUrl(Request $request): string
    {
        return $this->urlGenerator->generate(self::LOGIN_ROUTE);
    }

    private function validateCaptcha(string $captchaResponse): bool
    {
        $secret = '6Lfqm5gqAAAAAKX8lky4wzSm-sv_F9ccdL_YGdL3'; // Clé secrète de votre compte reCAPTCHA
        $url = 'https://www.google.com/recaptcha/api/siteverify';
        $data = ['secret' => $secret, 'response' => $captchaResponse];

        $response = file_get_contents($url . '?' . http_build_query($data));
        $result = json_decode($response, true);

        return $result['success'] ?? false;
    }
}
