<?php

namespace App\Security;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAuthenticationException;
use Symfony\Component\Security\Http\Authenticator\AbstractAuthenticator;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Credentials\PasswordCredentials;
use Symfony\Component\Security\Http\Authenticator\Passport\PassportInterface;

class JsonAuthenticator extends AbstractAuthenticator
{
    public function supports(Request $request): ?bool
    {
        return 'login' === $request->attributes->get('_route')
            && $request->isMethod('POST')
            && 'json' === $request->getContentType();
    }

    public function authenticate(Request $request): PassportInterface
    {
        $data = json_decode($request->getContent(), true);

        if (!$data['username'] || !$data['password']) {
            throw new CustomUserMessageAuthenticationException('No credentials provided');
        }

        return new Passport(new UserBadge($data['username']), new PasswordCredentials($data['password']));
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $firewallName): ?Response
    {
        $json = json_encode([
            'apiToken' => $token->getUser()->getApiToken()
        ]);

        return new JsonResponse($json, Response::HTTP_OK);
    }

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception): ?Response
    {
        $data = [
            'message' => strtr($exception->getMessageKey(), $exception->getMessageData()),
        ];

        return new JsonResponse($data, Response::HTTP_UNAUTHORIZED);
    }
}
