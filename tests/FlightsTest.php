<?php

namespace App\Tests;

use App\Dto\FlightResponseDto;
use App\Entity\Flight;
use App\Tests\Factory\FlightFactory;
use Zenstruck\Foundry\Test\Factories;
use Zenstruck\Foundry\Test\ResetDatabase;

final class FlightsTest extends AbstractTest
{
    use ResetDatabase, Factories;

    public function testGetCollection(): void
    {
        FlightFactory::createMany(100);

        $response = $this->createClientWithCredentials()
            ->request('GET', '/api/flights');

        $this->assertResponseIsSuccessful();
        $this->assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');

        $this->assertJsonContains([
            '@context' => '/contexts/Flight',
            '@type' => 'Collection',
            'totalItems' => 100,
        ]);

        $this->assertCount(30, $response->toArray()['hydra:member']);

        $this->assertMatchesResourceCollectionJsonSchema(FlightResponseDto::class);
    }

    public function testCreateFlight(): void
    {
        $client = $this->createClientWithCredentials()
            ->request('POST', '/api/flights', [
                'json' => [
                    'dateDeparture' => '2031-04-30T22:36:45.685Z',
                    'dateArrival' => '2031-04-30T04:36:45.685Z',
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
            '@id' => $iri,
            'price' => 100,
        ]);
    }

    public function testDeleteFlight(): void
    {
        $flight = FlightFactory::createOne();

        $client = $this->createClientWithCredentials();

        $iri = $this->findIriBy(Flight::class, ['id' => $flight->getId()]);

        $client->request('DELETE', $iri);

        $this->assertResponseStatusCodeSame(204);

        $this->assertNull(
            FlightFactory::repository()->find($flight->getId())
        );
    }
}
