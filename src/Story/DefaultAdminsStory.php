<?php

namespace App\Story;

use App\Factory\AdminFactory;
use Zenstruck\Foundry\Story;

final class DefaultAdminsStory extends Story
{
    public function build(): void
    {

        // Créer 20 admins
        AdminFactory::createMany(20);

        // Créer un 21ème admin avec email et mot de passe prédéfini
        AdminFactory::createOne([
            'email' => 'jeanfez@gmail.com',
            "password" => "123456789"
        ]);
    }
}
