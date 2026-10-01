<?php

namespace App\Story;

use App\Factory\AirportFactory;
use Zenstruck\Foundry\Story;

final class DefaultAirportsStory extends Story
{
    public function build(): void
    {
        AirportFactory::createMany(10);
    }
}
