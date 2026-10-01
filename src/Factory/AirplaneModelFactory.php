<?php

namespace App\Factory;

use App\Entity\AirplaneModel;
use App\Factory\ConstructorFactory;
use Zenstruck\Foundry\Persistence\PersistentObjectFactory;


/**
 * @extends PersistentObjectFactory<AirplaneModel>
 */
final class AirplaneModelFactory extends PersistentObjectFactory
{
    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#factories-as-services
     *
     * @todo inject services if required
     */
    public function __construct() {}

    #[\Override]
    public static function class(): string
    {
        return AirplaneModel::class;
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
            'capacity' => self::faker()->randomNumber(),
            'constructor' => ConstructorFactory::new(),
            'model' => self::faker()->text(7),
            'createdAt' => \DateTimeImmutable::createFromMutable(self::faker()->dateTime()),
        ];
    }

    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#initialization
     */
    #[\Override]
    protected function initialize(): static
    {
        return $this
            // ->afterInstantiate(function(AirplaneModel $airplaneModel): void {})
        ;
    }
}
