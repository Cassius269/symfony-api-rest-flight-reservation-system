<?php

namespace App\Factory;

use App\Entity\Admin;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Zenstruck\Foundry\Persistence\PersistentObjectFactory;

/**
 * @extends PersistentObjectFactory<Admin>
 */
final class AdminFactory extends PersistentObjectFactory
{
    public function __construct(private UserPasswordHasherInterface $passwordHasher) {}

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
            'password' => '123456789',
            'createdAt' => \DateTimeImmutable::createFromMutable(self::faker()->dateTime()),
        ];
    }

    #[\Override]
    protected function initialize(): static
    {
        return $this
            ->afterInstantiate(function (Admin $admin): void {
                $admin->setPassword(
                    $this->passwordHasher->hashPassword($admin, $admin->getPassword())
                );
            });
    }
}
