<?php

namespace App\Story;


use App\Story\DefaultAirplanesStory;
use Zenstruck\Foundry\Attribute\AsFixture;
use Zenstruck\Foundry\Story;

#[AsFixture(name: 'main')]
final class AppStory extends Story
{
    public function build(): void
    {
        DefaultAdminsStory::load();
        DefaultConstructorsStory::load();
        DefaultCompaniesStory::load();
        DefaultCaptainsStory::load();
        DefaultAirplaneModelsStory::load();
        DefaultAirplanesStory::load();
        DefaultCompaniesCaptainsStory::load();
        DefaultStatusStory::load();
        DefaultAirportsStory::load();
        DefaultFlightsStory::load();
    }
}
