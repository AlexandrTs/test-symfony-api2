<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use App\Repository\CustomersRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ORM\Entity(repositoryClass=CustomersRepository::class)
 * @UniqueEntity("email")
 */
#[ApiResource(
    normalizationContext: ['groups' => ['item']],
    collectionOperations: [
        'get' => [
            'normalization_context' => ['groups' => ['list']],
        ],
    ],
    itemOperations: [
        'get' => [
            'normalization_context' => ['groups' => ['item']],
        ],
    ]
)]
class Customers
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @Groups({"list","item"})
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"list","item"})
     */
    private $fullName;

    /**
     * @ORM\Column(type="string", length=255, unique=true)
     * @Assert\Email
     * @Groups({"list","item"})
     */
    private $email;

    /**
     * @ORM\Column(type="string", length=100)
     * @Groups({"list","item"})
     */
    private $country;

    /**
     * @ORM\Column(type="string", length=100, nullable=true)
     * @Groups({"item"})
     */
    private $username;

    /**
     * @ORM\Column(type="string", length="20", nullable=true)
     * @Groups({"item"})
     */
    private $gender;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Groups({"item"})
     */
    private $city;

    /**
     * @ORM\Column(type="string", length=20, nullable=true)
     * @Groups({"item"})
     */
    private $phone;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getFullName(): ?string
    {
        return $this->fullName;
    }

    public function setFullName(string $fullName): self
    {
        $this->fullName = $fullName;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    public function getCountry(): ?string
    {
        return $this->country;
    }

    public function setCountry(string $country): self
    {
        $this->country = $country;

        return $this;
    }

    public function getUsername(): ?string
    {
        return $this->username;
    }

    public function setUsername(?string $username): self
    {
        $this->username = $username;

        return $this;
    }

    public function getGender(): ?string
    {
        return $this->gender;
    }

    public function setGender(string $gender): self
    {
        $this->gender = $gender;

        return $this;
    }

    public function getCity(): ?string
    {
        return $this->city;
    }

    public function setCity(?string $city): self
    {
        $this->city = $city;

        return $this;
    }

    public function getPhone(): ?string
    {
        return $this->phone;
    }

    public function setPhone(?string $phone): self
    {
        $this->phone = $phone;

        return $this;
    }
}