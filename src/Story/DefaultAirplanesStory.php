<?php

namespace App\Story;

use App\Factory\AirplaneFactory;
use Zenstruck\Foundry\Story;

final class DefaultAirplanesStory extends Story
{
    public function build(): void
    {
        AirplaneFactory::createMany(10);
    }
}
