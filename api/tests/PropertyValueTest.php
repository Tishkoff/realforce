<?php

namespace App\Tests;

use ApiPlatform\Core\Bridge\Symfony\Bundle\Test\ApiTestCase;
use App\Entity\Employee;
use App\Entity\Property;
use App\Entity\PropertyValue;
use Hautelook\AliceBundle\PhpUnit\RefreshDatabaseTrait;

/**
 * Class PropertyValueTest
 * @package App\Tests
 */
class PropertyValueTest extends ApiTestCase
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
        $response = static::createClient()->request('GET', '/property_values');

        $this->assertResponseIsSuccessful();

        $this->assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');

        $this->assertJsonContains([
            '@context'         => '/contexts/PropertyValue',
            '@id'              => '/property_values',
            '@type'            => 'hydra:Collection',
            'hydra:totalItems' => 12,
        ]);

        $this->assertCount(12, $response->toArray()['hydra:member']);

        $this->assertMatchesResourceCollectionJsonSchema(PropertyValue::class);
    }

    /**
     * @throws \Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\DecodingExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface
     */
    public function testCreatePropertyValue(): void
    {
        static::createClient();

        $propertyId = static::findIriBy(Property::class, ['name' => 'age']);
        $employeeId = static::findIriBy(Employee::class, ['name' => 'Alice']);

        $response = static::createClient()->request('POST', '/property_values', [
            'json' => [
                'value'    => '5',
                'employee' => $employeeId,
                'property' => $propertyId,
            ],
        ]);

        $this->assertResponseStatusCodeSame(201);
        $this->assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');
        $this->assertJsonContains([
            '@context' => '/contexts/PropertyValue',
            '@type'    => 'PropertyValue',
            'value'    => '5',
            'employee' => $employeeId,
            'property' => $propertyId,
        ]);
        $this->assertRegExp('~^/property_values/\d+$~', $response->toArray()['@id']);
        $this->assertMatchesResourceItemJsonSchema(PropertyValue::class);
    }

    /**
     * @throws \Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\DecodingExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface
     */
    public function testCreateInvalidPropertyValue(): void
    {
        static::createClient();

        $propertyId = static::findIriBy(Property::class, ['name' => 'age']);
        $employeeId = static::findIriBy(Employee::class, ['name' => 'Alice']);

        $response = static::createClient()->request('POST', '/property_conditions', [
            'json' => [
                'employee' => $employeeId,
                'property' => $propertyId,
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
    public function testUpdatePropertyValue(): void
    {
        $client = static::createClient();

        $propertyValueId = static::findIriBy(PropertyValue::class, ['value' => '52']);

        $client->request('PUT', $propertyValueId, [
            'json' => [
                'value' => '53',
            ],
        ]);

        $this->assertResponseIsSuccessful();
        $this->assertJsonContains([
            '@id'   => $propertyValueId,
            'value' => '53',
        ]);
    }

    /**
     * @throws \Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface
     */
    public function testDeletePropertyValue(): void
    {
        $client = static::createClient();
        $propertyValueId = static::findIriBy(PropertyValue::class, ['value' => '52']);

        $client->request('DELETE', $propertyValueId);

        $this->assertResponseStatusCodeSame(204);
        $this->assertNull(
            static::$container->get('doctrine')->getRepository(PropertyValue::class)->findOneBy(['value' => '52'])
        );
    }
}
