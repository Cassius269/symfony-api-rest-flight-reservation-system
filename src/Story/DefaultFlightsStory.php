<?php

namespace App\Story;

use App\Factory\AirplaneFactory;
use App\Factory\AirportFactory;
use App\Factory\CaptainFactory;

use App\Factory\FlightFactory;
use Zenstruck\Foundry\Story;

final class DefaultFlightsStory extends Story
{
    public function build(): void
    {
        // Créer 100 vols aléatires par défaut
        FlightFactory::createMany(100, fn() => [
            'airportDeparture' => AirportFactory::random(),
            'airportArrival' => AirportFactory::random(),
            "airplane" => AirplaneFactory::random(),
            'captain' => CaptainFactory::random()
        ]);

        dump('Story FlightFactory exécutée');
    }
}
