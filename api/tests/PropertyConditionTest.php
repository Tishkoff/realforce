<?php

namespace App\Tests;

use ApiPlatform\Core\Bridge\Symfony\Bundle\Test\ApiTestCase;
use App\Entity\Property;
use App\Entity\PropertyCondition;
use Hautelook\AliceBundle\PhpUnit\RefreshDatabaseTrait;

/**
 * Class PropertyConditionTest
 * @package App\Tests
 */
class PropertyConditionTest extends ApiTestCase
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
        $response = static::createClient()->request('GET', '/property_conditions');

        $this->assertResponseIsSuccessful();

        $this->assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');

        $this->assertJsonContains([
            '@context'         => '/contexts/PropertyCondition',
            '@id'              => '/property_conditions',
            '@type'            => 'hydra:Collection',
            'hydra:totalItems' => 5,
        ]);

        $this->assertCount(5, $response->toArray()['hydra:member']);

        $this->assertMatchesResourceCollectionJsonSchema(PropertyCondition::class);
    }

    /**
     * @throws \Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\DecodingExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface
     */
    public function testCreatePropertyCondition(): void
    {
        static::createClient();

        $propertyId = static::findIriBy(Property::class, ['name' => 'age']);

        $response = static::createClient()->request('POST', '/property_conditions', [
            'json' => [
                'debitCredit' => true,
                'flatPercent' => true,
                'condition'   => '==',
                'value'       => '5',
                'amount'      => '11',
                'property'    => $propertyId,
            ],
        ]);

        $this->assertResponseStatusCodeSame(201);
        $this->assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');
        $this->assertJsonContains([
            '@context'    => '/contexts/PropertyCondition',
            '@type'       => 'PropertyCondition',
            'debitCredit' => true,
            'flatPercent' => true,
            'condition'   => '==',
            'value'       => '5',
            'amount'      => '11',
            'property'    => $propertyId,
        ]);
        $this->assertRegExp('~^/property_conditions/\d+$~', $response->toArray()['@id']);
        $this->assertMatchesResourceItemJsonSchema(PropertyCondition::class);
    }

    /**
     * @throws \Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\DecodingExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface
     */
    public function testCreateInvalidPropertyCondition(): void
    {
        static::createClient();

        $propertyId = static::findIriBy(Property::class, ['name' => 'age']);

        $response = static::createClient()->request('POST', '/property_conditions', [
            'json' => [
                'debitCredit' => true,
                'flatPercent' => true,
                'condition'   => '===',
                'value'       => '5',
                'amount'      => '11',
                'property'    => $propertyId,
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
     * @throws \Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\DecodingExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface
     */
    public function testUpdatePropertyCondition(): void
    {
        $client = static::createClient();

        $propertyConditionId = static::findIriBy(PropertyCondition::class, ['condition' => '>=']);

        $client->request('PUT', $propertyConditionId, [
            'json' => [
                'condition' => '!=',
            ],
        ]);

        $this->assertResponseIsSuccessful();
        $this->assertJsonContains([
            '@id'       => $propertyConditionId,
            'condition' => '!=',
        ]);
    }

    /**
     * @throws \Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface
     */
    public function testDeletePropertyCondition(): void
    {
        $client = static::createClient();
        $propertyId = static::findIriBy(PropertyCondition::class, ['amount' => '1234']);

        $client->request('DELETE', $propertyId);

        $this->assertResponseStatusCodeSame(204);
        $this->assertNull(
            static::$container->get('doctrine')->getRepository(PropertyCondition::class)->findOneBy(['amount' => '1234'])
        );
    }
}
