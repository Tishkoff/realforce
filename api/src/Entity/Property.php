<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Property Entity.
 *
 * @ApiResource
 * @ORM\Entity
 */
class Property
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
     * @Assert\NotBlank
     *
     * @Groups("EmployeeGeneral")
     */
    public $name = '';

    /**
     * One Property has many PropertyValues. This is the inverse side.
     * @ORM\OneToMany(targetEntity="PropertyValue", mappedBy="employee")
     */
    public $propertyValues;

    /**
     * One Property has many PropertyConditions. This is the inverse side.
     * @ORM\OneToMany(targetEntity="PropertyCondition", mappedBy="property")
     *
     * @Groups("EmployeeGeneral")
     */
    public $propertyConditions;

    /**
     * Property constructor.
     */
    public function __construct() {
        $this->propertyValues = new ArrayCollection();
        $this->propertyConditions = new ArrayCollection();
    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }
}
