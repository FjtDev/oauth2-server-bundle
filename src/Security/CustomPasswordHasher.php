<?php

namespace OAuth2\ServerBundle\Security;

use Symfony\Component\PasswordHasher\PasswordHasherInterface;

class CustomPasswordHasher implements PasswordHasherInterface
{
    /**
     * @inheritDoc
     */
    public function hash(string $plainPassword) : string
    {
        return $plainPassword;
    }

    /**
     * @inheritDoc
     */
    public function verify(string $hashedPassword, string $plainPassword) : bool
    {
        return $hashedPassword === $plainPassword;
    }

    /**
     * @inheritDoc
     */
    public function needsRehash(string $hashedPassword) : bool
    {
        return true;
    }
}