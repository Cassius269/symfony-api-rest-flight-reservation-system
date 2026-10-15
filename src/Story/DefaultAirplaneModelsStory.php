<?php

namespace App\Story;

use App\Factory\AirplaneModelFactory;
use Zenstruck\Foundry\Story;

final class DefaultAirplaneModelsStory extends Story
{
    public function build(): void
    {
        // TODO build your story here (https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#stories)
        AirplaneModelFactory::createMany(30);
    }
}
