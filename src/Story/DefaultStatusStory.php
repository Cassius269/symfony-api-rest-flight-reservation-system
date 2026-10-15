<?php

namespace App\Story;

use App\Factory\StatusFactory;
use Zenstruck\Foundry\Story;

final class DefaultStatusStory extends Story
{
    public function build(): void
    {
        StatusFactory::createOne(
            ['name' => 'Confirmé']
        );

        StatusFactory::createOne([
            'name' => 'En cours'
        ]);

        StatusFactory::createOne([
            'name' => 'Confirmé'
        ]);

        StatusFactory::createOne([
            'name' => 'Annulé'
        ]);

        dump('Story StatusFactory exécutée');
    }
}
