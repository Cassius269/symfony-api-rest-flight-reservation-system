<?php

namespace App\Story;

use App\Tests\Factory\AirplaneFactory;
use App\Tests\Factory\AirplaneModelFactory;
use App\Tests\Factory\AirportFactory;
use App\Tests\Factory\CaptainFactory;
use App\Tests\Factory\CityFactory;
use App\Tests\Factory\CountryFactory;
use App\Tests\Factory\FlightFactory;
use Zenstruck\Foundry\Story;

final class DefaultFlightsStory extends Story
{
    public function build(): void
    {
        CountryFactory::createMany(10);
        CityFactory::createMany(20);
        AirplaneFactory::createMany(10);
        AirplaneModelFactory::createMany(10);
        AirportFactory::createMany(15);
        CaptainFactory::createMany(20);

        // Créer 100 vols aléatires par défaut
        FlightFactory::createMany(100);

        dump('Story FlightFactory exécutée');
    }
}
