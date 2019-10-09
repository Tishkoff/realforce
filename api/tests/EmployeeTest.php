<?php

namespace App\Tests;

use ApiPlatform\Core\Bridge\Symfony\Bundle\Test\ApiTestCase;
use App\Entity\Employee;
use Hautelook\AliceBundle\PhpUnit\RefreshDatabaseTrait;

/**
 * Class EmployeeTest
 * @package App\Tests
 */
class EmployeeTest extends ApiTestCase
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
        $response = static::createClient()->request('GET', '/employees');

        $this->assertResponseIsSuccessful();

        $this->assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');

        $this->assertJsonContains([
            '@context'         => '/contexts/Employee',
            '@id'              => '/employees',
            '@type'            => 'hydra:Collection',
            'hydra:totalItems' => 4,
        ]);

        $this->assertCount(4, $response->toArray()['hydra:member']);

        $this->assertMatchesResourceCollectionJsonSchema(Employee::class);
    }

    /**
     * @throws \Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\DecodingExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface
     */
    public function testCreateEmployee(): void
    {
        $response = static::createClient()->request('POST', '/employees', [
            'json' => [
                'name' => 'Test Name',
            ],
        ]);

        $this->assertResponseStatusCodeSame(201);
        $this->assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');
        $this->assertJsonContains([
            '@context'       => '/contexts/Employee',
            '@type'          => 'Employee',
            'name'           => 'Test Name',
            'propertyValues' => [],
        ]);
        $this->assertRegExp('~^/employees/\d+$~', $response->toArray()['@id']);
        $this->assertMatchesResourceItemJsonSchema(Employee::class);
    }

    /**
     * @throws \Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\DecodingExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface
     */
    public function testCreateInvalidEmployee(): void
    {
        static::createClient()->request('POST', '/employees', [
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
     * @throws \Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\DecodingExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface
     */
    public function testUpdateEmployee(): void
    {
        $client = static::createClient();

        $employeeId = static::findIriBy(Employee::class, ['name' => 'Alice']);

        $client->request('PUT', $employeeId, [
            'json' => [
                'name' => 'updated Alice',
            ],
        ]);

        $this->assertResponseIsSuccessful();
        $this->assertJsonContains([
            '@id'  => $employeeId,
            'name' => 'updated Alice',
        ]);
    }

    /**
     * @throws \Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface
     */
    public function testDeleteEmployee(): void
    {
        $client = static::createClient();
        $employeeId = static::findIriBy(Employee::class, ['name' => 'Last Employee']);

        $client->request('DELETE', $employeeId);

        $this->assertResponseStatusCodeSame(204);
        $this->assertNull(
            static::$container->get('doctrine')->getRepository(Employee::class)->findOneBy(['name' => 'Last Employee'])
        );
    }
}
