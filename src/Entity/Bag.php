<?php

declare(strict_types=1);

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Post;
use App\Repository\BagRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * This class represents a bag of a given trainer. It holds a set of Poke-balls (items).
 */
#[ORM\Entity(repositoryClass: BagRepository::class)]
#[ApiResource(
    operations: [
        new GetCollection(security: "is_granted('ROLE_USER')"),
        new Post(uriTemplate: "/bag/create")
    ]
)]
class Bag
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['admin'])]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'bags')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Trainer $trainer = null;

    #[ORM\OneToMany(mappedBy: 'bag', targetEntity: BagItem::class)]
    #[Groups(['admin'])]
    private Collection $bagItems;

    public function __construct()
    {
        $this->bagItems = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTrainer(): ?Trainer
    {
        return $this->trainer;
    }

    public function setTrainer(?Trainer $trainer): self
    {
        $this->trainer = $trainer;

        return $this;
    }

    /**
     * @return Collection<int, BagItem>
     */
    public function getBagItems(): Collection
    {
        return $this->bagItems;
    }

    public function addBagItem(BagItem $bagItem): self
    {
        if (!$this->bagItems->contains($bagItem)) {
            $this->bagItems->add($bagItem);
            $bagItem->setBag($this);
        }

        return $this;
    }

    public function removeBagItem(BagItem $bagItem): self
    {
        if ($this->bagItems->removeElement($bagItem)) {
            // set the owning side to null (unless already changed)
            if ($bagItem->getBag() === $this) {
                $bagItem->setBag(null);
            }
        }

        return $this;
    }
}
