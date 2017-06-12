<?php

namespace App\Extensions\Auth;


use App\Exceptions\BaseException;
use App\Exceptions\InvalidClassException;
use Illuminate\Contracts\Auth\Authenticatable as UserContract;
use Illuminate\Contracts\Auth\Authenticatable;
use Jenssegers\Model\Model;

class NoDatabaseUserProvider implements \Illuminate\Contracts\Auth\UserProvider
{
    /**
     * The hasher implementation.
     *
     * @var \Illuminate\Contracts\Hashing\Hasher
     */
    protected $hasher;

    /**
     * Class of non Eloquent model
     *
     * @var string
     */
    protected $modelClass;

    public function __construct($hasher, $modelClass)
    {
        $this->checkModelClass($modelClass);

        $this->modelClass = $modelClass;

        $this->hasher = $hasher;
    }

    /**
     * Retrieve a user by their unique identifier.
     *
     * @param  mixed $identifier
     * @return UserContract|null
     */
    public function retrieveById($identifier)
    {
        $session = session();

        $storedUsername = $session->get('username');

        if (!$storedUsername) {
            return null;
        }

        return $this->getGenericUser([
            'id' => $session->getId(),
            'username' => $storedUsername,
        ]);
    }

    /**
     * Retrieve a user by their unique identifier and "remember me" token.
     *
     * @param  mixed $identifier
     * @param  string $token
     * @return Authenticatable|null
     * @throws BaseException
     */
    public function retrieveByToken($identifier, $token)
    {
        throw new BaseException('not implemented');
    }

    /**
     * Update the "remember me" token for the given user in storage.
     *
     * @param  \Illuminate\Contracts\Auth\Authenticatable $user
     * @param  string $token
     * @return void
     * @throws BaseException
     */
    public function updateRememberToken(Authenticatable $user, $token)
    {
        throw new BaseException('not implemented');
    }

    /**
     * Retrieve a user by the given credentials.
     *
     * @param  array $credentials
     * @return Authenticatable|null
     * @throws BaseException
     */
    public function retrieveByCredentials(array $credentials)
    {
        throw new BaseException('not implemented');
    }

    /**
     * Validate a user against the given credentials.
     *
     * @param  \Illuminate\Contracts\Auth\Authenticatable $user
     * @param  array $credentials
     * @return bool
     * @throws BaseException
     */
    public function validateCredentials(Authenticatable $user, array $credentials)
    {
        throw new BaseException('not implemented');
    }

    protected function checkModelClass($modelClass)
    {
        $class = new \ReflectionClass($modelClass);

        $interface = 'Illuminate\Contracts\Auth\Authenticatable';

        if (!$class->implementsInterface($interface)) {
            throw new InvalidClassException("Class must implements $interface, $modelClass given.");
        }
    }

    /**
     * Get the generic user.
     *
     * @param  mixed $user
     * @return UserContract|null
     */
    protected function getGenericUser($user)
    {
        if (!is_null($user)) {
            return new $this->modelClass((array)$user);
        }

        return null;
    }
}