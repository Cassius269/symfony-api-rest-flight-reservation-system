<?php

namespace App\Story;

use App\Factory\ConstructorFactory;
use Zenstruck\Foundry\Story;

final class DefaultConstructorsStory extends Story
{
    public function build(): void
    {
        ConstructorFactory::createMany(10);
    }
}
