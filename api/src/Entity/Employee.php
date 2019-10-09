<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiProperty;
use ApiPlatform\Core\Annotation\ApiResource;
use App\Controller\CalculateEmployeeSalary;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Employee Entity.
 *
 * @ORM\Entity
 *
 * @ApiResource(
 *     normalizationContext={"groups"={"EmployeeGeneral"}},
 *     itemOperations={
 *         "get",
 *         "put",
 *         "delete",
 *         "patch",
 *         "calculate_salary"={
 *             "method"="GET",
 *             "path"="/employees/{id}/salary",
 *             "controller"=CalculateEmployeeSalary::class,
 *             "normalization_context"={"groups"={"EmployeeGeneral", "calculate_salary"}},
 *             "openapi_context"={
 *                 "summary"="Calculates Salary for given Employee",
 *             }
 *         }
 *     }
 * )
 */
class Employee
{
    /**
     * @var int The entity Id
     *
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     *
     * @Groups("EmployeeGeneral")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column
     * @Assert\NotBlank
     *
     * @Groups("EmployeeGeneral")
     */
    public $name = '';

    /**
     * @var decimal
     *
     * @ORM\Column(type="decimal")
     * @Assert\NotBlank
     * @Assert\PositiveOrZero()
     *
     * @Groups("EmployeeGeneral")
     */
    public $salary = 0;

    /**
     * @var decimal
     *
     * @Groups("calculate_salary")
     *
     * @ApiProperty(
     *     attributes={
     *         "openapi_context"={
     *             "type"="decimal",
     *             "example"="5000"
     *         }
     *     }
     * )
     */
    public $finalSalary = 0;

    /**
     * One Employee has many PropertyValues. This is the inverse side.
     * @ORM\OneToMany(targetEntity="PropertyValue", mappedBy="employee")
     *
     * @Groups("EmployeeGeneral")
     */
    public $propertyValues;

    /**
     * Employee constructor.
     */
    public function __construct() {
        $this->propertyValues = new ArrayCollection();
    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }
}
