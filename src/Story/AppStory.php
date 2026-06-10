<?php

namespace App\Story;

use App\Story\DefaultCompanyStory;
use App\Story\DefaultFlightStory;
use App\Story\DefaultStatusStory;
use Zenstruck\Foundry\Attribute\AsFixture;
use Zenstruck\Foundry\Story;

#[AsFixture(name: 'main')]
final class AppStory extends Story
{
    public function build(): void
    {
        DefaultCaptainsStory::load();
        DefaultConstructorStory::load();
        DefaultCompanyStory::load();
        DefaultStatusStory::load();
        DefaultFlightStory::load();
    }
}
