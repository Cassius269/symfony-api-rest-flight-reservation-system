<?php

namespace App\Factory;

use App\Entity\Passenger;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Zenstruck\Foundry\Persistence\PersistentObjectFactory;

/**
 * @extends PersistentObjectFactory<Passenger>
 */
final class PassengerFactory extends PersistentObjectFactory
{
    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#factories-as-services
     *
     * @todo inject services if required
     */
    public function __construct(private UserPasswordHasherInterface $passwordHasher) {}

    #[\Override]
    public static function class(): string
    {
        return Passenger::class;
    }

    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#model-factories
     *
     * @todo add your default values here
     */
    #[\Override]
    protected function defaults(): array|callable
    {
        return [
            'createdAt' => \DateTimeImmutable::createFromMutable(self::faker()->dateTime()),
            'email' => self::faker()->safeEmail(),
            'firstname' => self::faker()->text(30),
            'lastname' => self::faker()->text(30),
            'password' => self::faker()->text(10),
            'roles' => ['ROLE_PASSENGER'],
        ];
    }

    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#initialization
     */
    #[\Override]
    protected function initialize(): static
    {
        return $this
            ->afterInstantiate(function (Passenger $passenger): void {
                $passenger->setPassword(
                    $this->passwordHasher->hashPassword($passenger, $passenger->getPassword())
                );
            });
    }
}
