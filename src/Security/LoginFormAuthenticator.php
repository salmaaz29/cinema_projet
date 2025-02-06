<?php
namespace App\Security;

use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Security\Http\Authenticator\AbstractLoginFormAuthenticator;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\CsrfTokenBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\RememberMeBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Credentials\PasswordCredentials;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;
use Symfony\Component\Security\Http\SecurityRequestAttributes;
use Symfony\Component\Security\Http\Util\TargetPathTrait;
use App\Repository\UsersRepository;

class LoginFormAuthenticator extends AbstractLoginFormAuthenticator
{
    use TargetPathTrait;

    public const LOGIN_ROUTE = 'app_login';

    private UrlGeneratorInterface $urlGenerator;
    private UserPasswordHasherInterface $passwordEncoder;
    private UsersRepository $userRepository;

    public function __construct(UrlGeneratorInterface $urlGenerator, UserPasswordHasherInterface $passwordEncoder, UsersRepository $userRepository)
    {
        $this->urlGenerator = $urlGenerator;
        $this->passwordEncoder = $passwordEncoder;
        $this->userRepository = $userRepository;
    }

    public function authenticate(Request $request): Passport
    {
        $email = $request->request->get('email');
        $password = $request->request->get('password');
        $csrfToken = $request->request->get('_csrf_token');

        // Vérification des champs requis
        if (null === $email || null === $password) {
            throw new AuthenticationException('Email et mot de passe sont requis.');
        }

        // Sauvegarder l'email dans la session pour la prochaine tentative
        $request->getSession()->set(SecurityRequestAttributes::LAST_USERNAME, $email);

        // Recherche de l'utilisateur dans la base de données
        $user = $this->userRepository->findOneByEmail($email);

        if (null === $user) {
            throw new AuthenticationException('Utilisateur non trouvé.');
        }

        // Ajout du débogage pour examiner les mots de passe
        // dd([
        //     'password_in_base' => $user->getPassword(), // Mot de passe haché en base
        //     'provided_password' => $password,           // Mot de passe fourni par l'utilisateur
        //     'password_is_valid' => $this->passwordEncoder->isPasswordValid($user, $password), // Vérification du mot de passe
        // ]);

        // Vérification du mot de passe
        if (!$this->passwordEncoder->isPasswordValid($user, $password)) {
            throw new AuthenticationException('Mot de passe invalide.');
        }

        // Création du passport pour l'authentification
        return new Passport(
            new UserBadge($email),  // Utilisation de l'email pour l'authentification
            new PasswordCredentials($password),  // Utilisation du mot de passe pour l'authentification
            [
                new CsrfTokenBadge('authenticate', $csrfToken), // Vérification du CSRF
                new RememberMeBadge(),
            ]
        );
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $firewallName): ?Response
    {
        // Si l'utilisateur a un rôle d'admin, redirige vers le tableau de bord admin
        if (in_array('ROLE_ADMIN', $token->getUser()->getRoles())) {
            return new RedirectResponse($this->urlGenerator->generate('admin_dashboard'));
        }

        // Si l'utilisateur n'est pas admin, redirige vers le tableau de bord utilisateur
        return new RedirectResponse($this->urlGenerator->generate('app_pre_controller'));
    }

    protected function getLoginUrl(Request $request): string
    {
        return $this->urlGenerator->generate(self::LOGIN_ROUTE);
    }
}
