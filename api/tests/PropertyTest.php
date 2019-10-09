<?php

namespace App\Tests;

use ApiPlatform\Core\Bridge\Symfony\Bundle\Test\ApiTestCase;
use App\Entity\Property;
use Hautelook\AliceBundle\PhpUnit\RefreshDatabaseTrait;

/**
 * Class PropertyTest
 * @package App\Tests
 */
class PropertyTest extends ApiTestCase
{
    use RefreshDatabaseTrait;

    /**
     * @throws \Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\DecodingExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface
     */
    public function testGetCollection(): void
    {
        $response = static::createClient()->request('GET', '/properties');

        $this->assertResponseIsSuccessful();

        $this->assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');

        $this->assertJsonContains([
            '@context'         => '/contexts/Property',
            '@id'              => '/properties',
            '@type'            => 'hydra:Collection',
            'hydra:totalItems' => 6,
        ]);

        $this->assertCount(6, $response->toArray()['hydra:member']);

        $this->assertMatchesResourceCollectionJsonSchema(Property::class);
    }

    /**
     * @throws \Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\DecodingExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface
     */
    public function testCreateProperty(): void
    {
        $response = static::createClient()->request('POST', '/properties', [
            'json' => [
                'name' => 'Test Name',
            ],
        ]);

        $this->assertResponseStatusCodeSame(201);
        $this->assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');
        $this->assertJsonContains([
            '@context'       => '/contexts/Property',
            '@type'          => 'Property',
            'name'           => 'Test Name',
            'propertyValues' => [],
        ]);
        $this->assertRegExp('~^/properties/\d+$~', $response->toArray()['@id']);
        $this->assertMatchesResourceItemJsonSchema(Property::class);
    }

    /**
     * @throws \Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\DecodingExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface
     */
    public function testCreateInvalidProperty(): void
    {
        static::createClient()->request('POST', '/properties', [
            'json' => [

            ],
        ]);

        $this->assertResponseStatusCodeSame(400);
        $this->assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');

        $this->assertJsonContains([
            '@context'    => '/contexts/ConstraintViolationList',
            '@type'       => 'ConstraintViolationList',
            'hydra:title' => 'An error occurred',
        ]);
    }

    /**
     * @throws \Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface
     */
    public function testUpdateProperty(): void
    {
        $client = static::createClient();

        $propertyId = static::findIriBy(Property::class, ['name' => 'age']);

        $client->request('PUT', $propertyId, [
            'json' => [
                'name' => 'updated age',
            ],
        ]);

        $this->assertResponseIsSuccessful();
        $this->assertJsonContains([
            '@id'  => $propertyId,
            'name' => 'updated age',
        ]);
    }

    /**
     * @throws \Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface
     */
    public function testDeleteProperty(): void
    {
        $client = static::createClient();
        $propertyId = static::findIriBy(Property::class, ['name' => 'works_remote']);

        $client->request('DELETE', $propertyId);

        $this->assertResponseStatusCodeSame(204);
        $this->assertNull(
            static::$container->get('doctrine')->getRepository(Property::class)->findOneBy(['name' => 'works_remote'])
        );
    }
}
