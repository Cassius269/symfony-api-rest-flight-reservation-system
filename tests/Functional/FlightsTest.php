<?php

namespace App\Tests\Functional;

use App\Factory\AirplaneFactory;
use App\Factory\AirportFactory;
use App\Factory\CaptainFactory;
use App\Factory\CityFactory;
use App\Factory\CompanyFactory;
use App\Factory\CountryFactory;
use App\Factory\FlightFactory;
use App\Factory\StatusFactory;
use App\Entity\Flight;
use App\Tests\AbstractTest;
use Zenstruck\Foundry\Test\Factories;
use Zenstruck\Foundry\Test\ResetDatabase;

final class FlightsTest extends AbstractTest
{
    use ResetDatabase, Factories; // réinitialisation de la bdd entre les tests

    public function testGetCollection(): void
    {
        $this->prepareFlightDependencies();
        FlightFactory::createMany(100);

        $response = $this->createClientWithCredentials()
            ->request('GET', '/api/flights');

        $this->assertResponseIsSuccessful(); // code entre 200 et 299
        $this->assertResponseHeaderSame('Content-Type', 'application/ld+json');
    }

    public function testCreateFlight(): void
    {
        $this->prepareFlightDependencies();
        $france = CountryFactory::createOne(['name' => 'France']);
        $comores = CountryFactory::createOne(['name' => 'Comores']);
        $company = CompanyFactory::createOne(['name' => 'Air France']);

        AirportFactory::createOne([
            'name' => 'Aéroport Charles de Gaulle',
            'city' => CityFactory::new(['country' => $france]),
        ]);
        AirportFactory::createOne([
            'name' => 'Aéroport Prince Saïd Ibrahim',
            'city' => CityFactory::new(['country' => $comores]),
        ]);
        AirplaneFactory::createOne(['reference' => 'hjytjytj', 'company' => $company]);
        CaptainFactory::createOne(['email' => 'test@example.com']);
        StatusFactory::createOne(['name' => 'Confirmé']);

        FlightFactory::createOne();
        $client = $this->createClientWithCredentials()
            ->request('POST', '/api/flights', [
                'json' => [
                    'dateDeparture' => '2031-04-30T04:36:45.685Z',
                    'dateArrival' => '2031-04-30T10:36:45.685Z',
                    'airportDeparture' => [
                        'name' => 'Aéroport Charles de Gaulle',
                        'city' => ['countryName' => 'France'],
                    ],
                    'airportArrival' => [
                        'name' => 'Aéroport Prince Saïd Ibrahim',
                        'city' => ['countryName' => 'Comores'],
                    ],
                    'price' => 1200.50,
                    'company' => ['name' => 'Air France'],
                    'airplane' => ['reference' => 'hjytjytj'],
                    'captain' => ['email' => 'test@example.com'],
                ],
            ]);

        $this->assertResponseStatusCodeSame(201);
    }

    public function testUpdateFlight(): void
    {
        $this->prepareFlightDependencies();
        $flight = FlightFactory::createOne();

        $client = $this->createClientWithCredentials();

        $iri = $this->findIriBy(Flight::class, ['id' => $flight->getId()]); // GET /api/flights/{id}

        $client->request('PATCH', $iri, [
            'json' => ['price' => 100],
            'headers' => [
                'Content-Type' => 'application/merge-patch+json',
                'Accept' => 'application/ld+json',
            ],
        ]);

        $this->assertResponseIsSuccessful();

        $this->assertJsonContains([
            'id' => $flight->getId(),
            'price' => 100,
        ]);
    }

    public function testDeleteFlight(): void
    {
        $this->prepareFlightDependencies();
        // Création d'un vol à supprimer
        $flight = FlightFactory::createOne();

        // Instanciation d'un client authentifié pour accéder aux endpoints protégés
        $client = $this->createClientWithCredentials();

        // Récupération de l'IRI
        $iri = $this->findIriBy(Flight::class, ['id' => $flight->getId()]);

        $client->request('DELETE', $iri);

        $this->assertResponseStatusCodeSame(204);

        // Vérifier que l'objet vol supprimé n'existe plus en base de données 
        $this->assertNull(
            FlightFactory::repository()->find($flight->getId())
        );
    }


    public function testDeleteNotFoundFlight(): void
    {
        $this->prepareFlightDependencies();
        $client = $this->createClientWithCredentials();

        $id = 10;

        $this->assertNull(
            FlightFactory::repository()->find($id),
            'Le vol utilisé pour ce test doit être absent.'
        );

        $client->request('DELETE', '/api/flights/' . $id);

        $this->assertResponseStatusCodeSame(
            404,
            'La suppression d’un vol inexistant doit retourner 404.'
        );
    }


    private function prepareFlightDependencies(): void
    {
        $company = CompanyFactory::createOne();

        AirplaneFactory::createOne([
            'company' => $company,
        ]);

        CaptainFactory::createOne();
        StatusFactory::createOne();
    }
}
