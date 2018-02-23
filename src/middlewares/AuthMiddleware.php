<?php


namespace hiapi\middlewares;

use hiapi\exceptions\InsufficientPermissionsException;
use League\Tactician\Middleware;
use yii\web\User;

/**
 * Class AuthMiddleware
 *
 * @author Dmytro Naumenko <d.naumenko.a@gmail.com>
 */
class AuthMiddleware implements Middleware
{
    /**
     * @var string
     */
    private $permission;
    /**
     * @var User
     */
    private $user;

    public function __construct(string $permission, User $user)
    {
        $this->permission = $permission;
        $this->user = $user;
    }

    /**
     * @param object $command
     * @param callable $next
     *
     * @return mixed
     * @throws InsufficientPermissionsException
     */
    public function execute($command, callable $next)
    {
        if (!$this->user->can($this->permission)) {
            throw new InsufficientPermissionsException($this->permission);
        }

        return $next($command);
    }
}
