<?php

namespace App\Security;

use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Http\Authenticator\AbstractLoginFormAuthenticator;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\CsrfTokenBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\RememberMeBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Credentials\PasswordCredentials;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;
use Symfony\Component\Security\Http\SecurityRequestAttributes;
use Symfony\Component\Security\Http\Util\TargetPathTrait;

class UserAuthenticator extends AbstractLoginFormAuthenticator
{
    use TargetPathTrait;

    public const LOGIN_ROUTE = 'app_login';

    public function __construct(private UrlGeneratorInterface $urlGenerator)
    {
    }

    public function authenticate(Request $request): Passport
    {
        // Support both JSON payloads and traditional form submissions.
        $post = $request->request;
        $json = [];
        $contentType = (string) $request->headers->get('Content-Type', '');
        if (str_contains($contentType, 'application/json')) {
            $decoded = json_decode($request->getContent() ?? '', true);
            if (is_array($decoded)) {
                $json = $decoded;
            }
        }

        $email = (string) ($post->get('email') ?? $post->get('_username') ?? ($json['email'] ?? ''));
        $password = (string) ($post->get('password') ?? $post->get('_password') ?? ($json['password'] ?? ''));
        $csrf = (string) ($post->get('_csrf_token') ?? ($json['_csrf_token'] ?? ''));

        $request->getSession()->set(SecurityRequestAttributes::LAST_USERNAME, $email);

        return new Passport(
            new UserBadge($email),
            new PasswordCredentials($password),
            [
                new CsrfTokenBadge('authenticate', $csrf),
                new RememberMeBadge(),
            ]
        );
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $firewallName): ?Response
    {
        if ($targetPath = $this->getTargetPath($request->getSession(), $firewallName)) {
            return new RedirectResponse($targetPath);
        }

        // Redirect based on roles
        $roles = method_exists($token, 'getRoleNames') ? $token->getRoleNames() : $token->getRoles();
        if (in_array('ROLE_ADMIN', $roles, true)) {
            return new RedirectResponse($this->urlGenerator->generate('app_admin'));
        }
        if (in_array('ROLE_TEACHER', $roles, true)) {
            return new RedirectResponse($this->urlGenerator->generate('app_teacher'));
        }
        if (in_array('ROLE_STUDENT', $roles, true)) {
            return new RedirectResponse($this->urlGenerator->generate('app_student'));
        }

        // default
        return new RedirectResponse($this->urlGenerator->generate('app_landing_page'));
    }

    protected function getLoginUrl(Request $request): string
    {
        return $this->urlGenerator->generate(self::LOGIN_ROUTE);
    }
}
