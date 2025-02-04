<?php

namespace OAuth2\ServerBundle\User;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\PasswordHasher\Hasher\PasswordHasherFactory;
use Symfony\Component\Security\Core\Exception\UserNotFoundException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;

class OAuth2UserProvider implements UserProviderInterface
{
    private EntityManagerInterface $em;
    private PasswordHasherFactory $encoderFactory;

    public function __construct(EntityManagerInterface $entityManager/*, PasswordHasherFactory $encoderFactory*/)
    {
        $this->em = $entityManager;
//        $this->encoderFactory = $encoderFactory;
    }

    /**
     * Loads the user for the given username.
     *
     * This method must throw UsernameNotFoundException if the user is not
     * found.
     *
     * @param string $username The username
     *
     * @return UserInterface
     *
     * @see UsernameNotFoundException
     *
     * @throws UsernameNotFoundException if the user is not found
     *
     */
    public function loadUserByUsername($username)
    {
        $user = $this->em->getRepository('OAuth2ServerBundle:User')->find($username);

        if (!$user) {
            throw new UserNotFoundException(sprintf('Username "%s" not found.', $username));
        }

        return $user;
    }

    /**
     * Refreshes the user for the account interface.
     *
     * It is up to the implementation to decide if the user data should be
     * totally reloaded (e.g. from the database), or if the UserInterface
     * object can just be merged into some internal array of users / identity
     * map.
     * @param UserInterface $user
     *
     * @return UserInterface
     *
     * @throws UnsupportedUserException if the account is not supported
     */
    public function refreshUser(UserInterface $user)
    {
        if (!$user instanceof OAuth2UserInterface) {
            throw new UnsupportedUserException(sprintf('Instances of "%s" are not supported.', get_class($user)));
        }

        return $this->loadUserByUsername($user->getUsername());
    }

    /**
     * Whether this provider supports the given user class
     *
     * @param string $class
     *
     * @return Boolean
     */
    public function supportsClass($class)
    {
        if ($class == 'OAuth2UserInterface') {
            return true;
        }

        return false;
    }

    /**
     * Creates a new user
     *
     * @param string $username
     *
     * @param string $password
     *
     * @param array $roles
     *
     * @param array $scopes
     *
     * @return UserInterface
     */
    public function createUser($username, $password, array $roles = array(), array $scopes = array())
    {
        $user = new \OAuth2\ServerBundle\Entity\User();
        $user->setUsername($username);
        $user->setRoles($roles);
        $user->setScopes($scopes);

        // Generate password
        $salt = $this->generateSalt();
//        $password = $this->encoderFactory->getPasswordHasher($user)->hash($password);
        $password = $user->getPasswordHasher($user)->hash($password);

        $user->setSalt($salt);
        $user->setPassword($password);

        // Store User
        $this->em->persist($user);
        $this->em->flush();

        return $user;
    }

    /**
     * Creates a salt for password hashing
     *
     * @return string salt
     */
    protected function generateSalt()
    {
        return base_convert(sha1(uniqid(mt_rand(), true)), 16, 36);
    }
}
