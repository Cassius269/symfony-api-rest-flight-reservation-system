<?php

namespace App\Tests\Factory;

use App\Entity\Admin;
use App\Repository\AdminRepository;
use Zenstruck\Foundry\Persistence\PersistentObjectFactory;
use Zenstruck\Foundry\Persistence\Proxy;
use Zenstruck\Foundry\Persistence\ProxyRepositoryDecorator;

/**
 * @extends PersistentObjectFactory<Admin>
 */
final class AdminFactory extends PersistentObjectFactory
{
    public function __construct() {}

    #[\Override]
    public static function class(): string
    {
        return Admin::class;
    }

    #[\Override]
    protected function defaults(): array|callable
    {
        return [
            'email' => self::faker()->email(),
            'firstname' => self::faker()->firstName(),
            'lastname' => self::faker()->lastName(),
            'roles' => ['ROLE_ADMIN'],
            'password' => self::faker()->password(4, 10), // hashed version of "$3cr3t"
            'createdAt' => \DateTimeImmutable::createFromMutable(self::faker()->dateTime()),
        ];
    }

    #[\Override]
    protected function initialize(): static
    {
        return $this;
    }
}
