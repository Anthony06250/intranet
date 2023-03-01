<?php

namespace App\Trait;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;
use libphonenumber\PhoneNumber;
use Misd\PhoneNumberBundle\Validator\Constraints\PhoneNumber as AssertPhoneNumber;

trait ContactTrait
{
    /**
     * @var PhoneNumber|null
     */
    #[ORM\Column(type: 'phone_number', nullable: true)]
    #[AssertPhoneNumber]
    #[Groups(['search'])]
    private ?PhoneNumber $phone = null;

    /**
     * @var string|null
     */
    #[ORM\Column(length: 180, nullable: true)]
    #[Assert\Length(max: 180)]
    #[Groups(['search'])]
    private ?string $email = null;

    /**
     * @return PhoneNumber|null
     */
    public function getPhone(): ?PhoneNumber
    {
        return $this->phone;
    }

    /**
     * @param PhoneNumber|null $phone
     * @return $this
     */
    public function setPhone(?PhoneNumber $phone): self
    {
        $this->phone = $phone;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getEmail(): ?string
    {
        return $this->email;
    }

    /**
     * @param string|null $email
     * @return $this
     */
    public function setEmail(?string $email): self
    {
        $this->email = $email;

        return $this;
    }
}