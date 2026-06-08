<?php

namespace App\Story;

use App\Tests\Factory\StatusFactory;
use Zenstruck\Foundry\Story;

final class DefaultStatusStory extends Story
{
    public function build(): void
    {
        StatusFactory::createMany(10);
        dump('Story StatusFactory exécutée');
    }
}
