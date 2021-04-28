<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use App\Repository\CouponRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ORM\Entity(repositoryClass=CouponRepository::class)
 *
 * @ApiResource(
 *     collectionOperations={"get"={"normalization_context"={"groups"="coupon:list"}}},
 *     itemOperations={"get"={"normalization_context"={"groups"="coupon:item"}}},
 *     order={"code"="ASC"},
 *     paginationEnabled=false
 * )
 */
class Coupon
{
    /**
     * @ORM\Id
     * @ORM\Column(type="string", length=10)
     */
    #[Groups(['coupon:list', 'coupon:item'])]
    private $code;

    /**
     * @ORM\Column(type="string", length=255)
     */
    #[Groups(['coupon:list', 'coupon:item'])]
    private $description;

    /**
     * @ORM\Column(type="date")
     */
    #[Groups(['coupon:list', 'coupon:item'])]
    private $begins;

    /**
     * @ORM\Column(type="date")
     */
    #[Groups(['coupon:list', 'coupon:item'])]
    private $ends;

    /**
     * @ORM\Column(type="string", length=500, nullable=true)
     */
    #[Groups(['coupon:list', 'coupon:item'])]
    private $limitations;

    /**
     * @ORM\ManyToMany(targetEntity=User::class, mappedBy="coupons")
     */
    private $users;

    public function __construct()
    {
        $this->users = new ArrayCollection();
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getCode(): ?string
    {
        return $this->code;
    }

    public function setCode(string $code): self
    {
        $this->code = $code;

        return $this;
    }

    public function getBegins(): ?\DateTimeInterface
    {
        return $this->begins;
    }

    public function setBegins(\DateTimeInterface $begins): self
    {
        $this->begins = $begins;

        return $this;
    }

    public function getEnds(): ?\DateTimeInterface
    {
        return $this->ends;
    }

    public function setEnds(\DateTimeInterface $ends): self
    {
        $this->ends = $ends;

        return $this;
    }

    public function getLimitations(): ?string
    {
        return $this->limitations;
    }

    public function setLimitations(?string $limitations): self
    {
        $this->limitations = $limitations;

        return $this;
    }

    /**
     * @return Collection|User[]
     */
    public function getUsers(): Collection
    {
        return $this->users;
    }

    public function addUser(User $user): self
    {
        if (!$this->users->contains($user)) {
            $this->users[] = $user;
            $user->addCoupon($this);
        }

        return $this;
    }

    public function removeUser(User $user): self
    {
        if ($this->users->removeElement($user)) {
            $user->removeCoupon($this);
        }

        return $this;
    }
}
