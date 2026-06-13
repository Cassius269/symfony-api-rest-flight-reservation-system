<?php

namespace App\Story;

use App\Tests\Factory\CompanyFactory;
use Zenstruck\Foundry\Story;

final class DefaultCompaniesStory extends Story
{
    public function build(): void
    {
        // Créer 10 compagnies par défaut
        CompanyFactory::createMany(10);
        dump('Story CompanyFactory exécutée');
    }
}
