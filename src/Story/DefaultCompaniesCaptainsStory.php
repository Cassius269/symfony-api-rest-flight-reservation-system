<?php

namespace App\Story;

use App\Factory\CompanyCaptainFactory;
use Zenstruck\Foundry\Story;

final class DefaultCompaniesCaptainsStory extends Story
{
    public function build(): void
    {
        // TODO build your story here (https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#stories)
        CompanyCaptainFactory::createMany(100);
    }
}
