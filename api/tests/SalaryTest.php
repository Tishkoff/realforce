<?php

namespace App\Tests;

use ApiPlatform\Core\Bridge\Symfony\Bundle\Test\ApiTestCase;
use App\Entity\Employee;
use Hautelook\AliceBundle\PhpUnit\RefreshDatabaseTrait;

/**
 * Class SalaryTest
 * @package App\Tests
 */
class SalaryTest extends ApiTestCase
{
    use RefreshDatabaseTrait;

    /**
     * @throws \Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\DecodingExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface
     */
    public function testAliceSalary(): void
    {
        static::createClient();

        $employeeId = static::findIriBy(Employee::class, ['name' => 'Alice']);

        static::createClient()->request('GET', $employeeId . '/salary', []);

        $this->assertResponseStatusCodeSame(200);
        $this->assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');
        $this->assertJsonContains([
            '@context'    => '/contexts/Employee',
            '@type'       => 'Employee',
            'finalSalary' => 4800,
        ]);
    }

    /**
     * @throws \Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\DecodingExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface
     */
    public function testBobSalary(): void
    {
        static::createClient();

        $employeeId = static::findIriBy(Employee::class, ['name' => 'Bob']);

        static::createClient()->request('GET', $employeeId . '/salary', []);

        $this->assertResponseStatusCodeSame(200);
        $this->assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');
        $this->assertJsonContains([
            '@context'    => '/contexts/Employee',
            '@type'       => 'Employee',
            'finalSalary' => 2980,
        ]);
    }

    /**
     * @throws \Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\DecodingExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface
     */
    public function testCharlieSalary(): void
    {
        static::createClient();

        $employeeId = static::findIriBy(Employee::class, ['name' => 'Charlie']);

        static::createClient()->request('GET', $employeeId . '/salary', []);

        $this->assertResponseStatusCodeSame(200);
        $this->assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');
        $this->assertJsonContains([
            '@context'    => '/contexts/Employee',
            '@type'       => 'Employee',
            'finalSalary' => 3550,
        ]);
    }
}
