<?php

namespace App\Story;

use App\Factory\StatusFactory;
use Zenstruck\Foundry\Story;

final class DefaultStatusStory extends Story
{
    public function build(): void
    {
        StatusFactory::createMany(5);
        dump('Story StatusFactory exécutée');
    }
}
