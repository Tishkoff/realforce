<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * PropertyCondition Entity.
 *
 * @ApiResource
 * @ORM\Entity
 */
class PropertyCondition
{
    /**
     * Conditions possible
     */
    public const CONDITIONS = ['==', '!=', '>=', '<=', '>', '<'];

    /**
     * @var int The entity Id
     *
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @var bool
     *
     * @ORM\Column(type="boolean")
     *
     * @Groups("EmployeeGeneral")
     */
    public $debitCredit = 0;

    /**
     * @var bool
     *
     * @ORM\Column(type="boolean")
     *
     * @Groups("EmployeeGeneral")s
     */
    public $flatPercent = 0;

    /**
     * @var string
     *
     * @ORM\Column
     * @Assert\NotBlank
     * @Assert\Choice(choices=PropertyCondition::CONDITIONS, message="Choose a valid contition from: ==, !=, >=, <=, >, <.")
     *
     * @Groups("EmployeeGeneral")
     */
    public $condition = '';

    /**
     * @var string
     *
     * @ORM\Column
     * @Assert\NotBlank
     *
     * @Groups("EmployeeGeneral")
     */
    public $value = '';

    /**
     * @var decimal
     *
     * @ORM\Column(type="decimal")
     * @Assert\NotBlank
     * @Assert\PositiveOrZero()
     *
     * @Groups("EmployeeGeneral")
     */
    public $amount = 0;

    /**
     * Many PropertyConditions have one Property. This is the owning side.
     * @ORM\ManyToOne(targetEntity="Property", inversedBy="propertyValues")
     * @ORM\JoinColumn(name="property_id", referencedColumnName="id")
     * @Assert\NotNull()
     */
    public $property;

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }
}
