<?php

namespace App\Story;

use App\Tests\Factory\CaptainFactory;
use Zenstruck\Foundry\Story;

final class DefaultCaptainsStory extends Story
{
    public function build(): void
    {
        // Créer 10 capitaines par défaut
        CaptainFactory::createMany(100);
        dump('Story CaptainsFactory exécutée');
    }
}
