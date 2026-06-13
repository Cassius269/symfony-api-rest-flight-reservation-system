<?php

namespace App\Tests\Factory;

use App\Entity\Flight;
use App\Repository\FlightRepository;
use Doctrine\ORM\EntityRepository;
use Zenstruck\Foundry\Persistence\PersistentObjectFactory;
use Zenstruck\Foundry\Persistence\Proxy;
use Zenstruck\Foundry\Persistence\ProxyRepositoryDecorator;

/**
 * @extends PersistentObjectFactory<Flight>
 */
final class FlightFactory extends PersistentObjectFactory
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
        return Flight::class;
    }

    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#model-factories
     *
     * @todo add your default values here
     */
    #[\Override]
    protected function defaults(): array|callable
    {
        $dateDeparture = self::faker()->dateTimeBetween('+1 month', '+1 year');
        $dateArrival = (clone $dateDeparture)->modify('+6 hours');

        return [
            'airplane' => AirplaneFactory::new(),
            'airportArrival' => AirportFactory::new(),
            'airportDeparture' => AirportFactory::new(),
            'captain' => CaptainFactory::new(), // TODO add App\\Entity\\Captain type manually
            'company' => CompanyFactory::new(),
            'createdAt' => \DateTimeImmutable::createFromMutable(self::faker()->dateTime()),
            'dateArrival' => $dateArrival,
            'dateDeparture' => $dateDeparture,
            'isCanceled' => self::faker()->boolean(),
            'isDirect' => self::faker()->boolean(),
            'isLate' => self::faker()->boolean(),
            'price' => self::faker()->randomFloat(2, 100, 1000), // génère un prix décimal aléatoire entre 100 et 1000euros
            'status' => StatusFactory::new(),
        ];
    }

    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#initialization
     */
    #[\Override]
    protected function initialize(): static
    {
        return $this
            // ->afterInstantiate(function(Flight $flight): void {})
        ;
    }
}
