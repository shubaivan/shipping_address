<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Timestampable\Traits\TimestampableEntity;
use Symfony\Component\Validator\Constraints as Assert;
use JMS\Serializer\Annotation;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ShippingAddressRepository")
 */
class ShippingAddress
{
    const GROUP_PUT = 'put_group';
    const GROUP_POST = 'post_group';

    use TimestampableEntity;

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank(groups={ShippingAddress::GROUP_POST})
     * @Annotation\Groups({ShippingAddress::GROUP_PUT, ShippingAddress::GROUP_POST})
     */
    private $address;

    /**
     * @ORM\ManyToOne(targetEntity="User", inversedBy="shippingAddress")
     * @Assert\NotBlank(groups={ShippingAddress::GROUP_POST})
     * @Annotation\Groups({ShippingAddress::GROUP_PUT, ShippingAddress::GROUP_POST})
     */
    private $user;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getAddress(): ?string
    {
        return $this->address;
    }

    public function setAddress(string $address): self
    {
        $this->address = $address;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;

        return $this;
    }
}
