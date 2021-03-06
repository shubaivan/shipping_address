<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Timestampable\Traits\TimestampableEntity;
use Symfony\Component\Validator\Constraints as Assert;
use JMS\Serializer\Annotation;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ShippingAddressRepository")
 * @ORM\Table(
 *     uniqueConstraints={@ORM\UniqueConstraint(
 *     name="default_uniq_index",
 *     columns={"user_id", "default_address"},
 *     options={"where": "(default_address != 'f')"}
 *     )
 * })
 */
class ShippingAddress
{
    const GROUP_PUT = 'put_group';
    const GROUP_GET = 'get_group';
    const GROUP_POST = 'post_group';

    use TimestampableEntity;

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     * @Annotation\Groups({ShippingAddress::GROUP_GET})
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank(groups={ShippingAddress::GROUP_POST})
     * @Annotation\Groups({
     *     ShippingAddress::GROUP_PUT, ShippingAddress::GROUP_POST, ShippingAddress::GROUP_GET
     *     })
     */
    private $address;

    /**
     * @ORM\Column(type="boolean")
     * @Assert\NotBlank(groups={ShippingAddress::GROUP_POST})
     * @Annotation\Groups({
     *     ShippingAddress::GROUP_PUT, ShippingAddress::GROUP_POST, ShippingAddress::GROUP_GET
     *     })
     */
    private $defaultAddress = false;

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

    /**
     * @return string
     * @Annotation\VirtualProperty()
     * @Annotation\Groups({ShippingAddress::GROUP_GET})
     */
    public function getSerializedCreatedAt()
    {
        return $this->createdAt ? $this->createdAt->format('Y-m-d H:i:s') : '';
    }

    /**
     * @param bool $defaultAddress
     */
    public function setDefaultAddress(bool $defaultAddress): void
    {
        $this->defaultAddress = $defaultAddress;
    }
}
