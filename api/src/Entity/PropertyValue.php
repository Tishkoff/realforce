<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * PropertyValue Entity.
 *
 * @ApiResource
 * @ORM\Entity
 */
class PropertyValue
{
    /**
     * @var int The entity Id
     *
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column
     *
     * @Groups("EmployeeGeneral")
     */
    public $value = '';

    /**
     * Many PropertyValues have one Property. This is the owning side.
     * @ORM\ManyToOne(targetEntity="Property", inversedBy="propertyValues")
     * @ORM\JoinColumn(name="property_id", referencedColumnName="id")
     * @Assert\NotNull()
     *
     * @Groups("EmployeeGeneral")
     */
    public $property;

    /**
     * Many PropertyValues have one Employee. This is the owning side.
     * @ORM\ManyToOne(targetEntity="Employee", inversedBy="propertyValues")
     * @ORM\JoinColumn(name="employee_id", referencedColumnName="id")
     * @Assert\NotNull()
     */
    public $employee;

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }
}
