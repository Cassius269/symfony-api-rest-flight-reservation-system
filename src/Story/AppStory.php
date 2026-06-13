<?php

namespace App\Story;

use App\Story\DefaultCompaniesStory;
use App\Story\DefaultFlightsStory;
use App\Story\DefaultStatusStory;
use Zenstruck\Foundry\Attribute\AsFixture;
use Zenstruck\Foundry\Story;

#[AsFixture(name: 'main')]
final class AppStory extends Story
{
    public function build(): void
    {
        DefaultCaptainsStory::load();
        DefaultConstructorsStory::load();
        DefaultCompaniesStory::load();
        DefaultStatusStory::load();
        DefaultFlightsStory::load();
    }
}
