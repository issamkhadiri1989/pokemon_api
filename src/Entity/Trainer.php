<?php

declare(strict_types=1);

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Put;
use App\Controller\UserController;
use App\Processor\UserPasswordHasher;
use App\Repository\TrainerRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\Validator\Constraints\UserPassword;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * This class represents the trainers instance.
 */
#[ORM\Entity(repositoryClass: TrainerRepository::class)]
#[ApiResource(
    operations: [
        // Register new trainer
        new Post(
            uriTemplate: "/register",
            normalizationContext: ['groups' => ['user:register']],
            processor: UserPasswordHasher::class
        ),
        // Displays the public info of the trainer
        new Get(
            uriTemplate: "/trainer/{id}",
            normalizationContext: ['groups' => ['public']]
        ),
        // Listing all trainers
        new GetCollection(normalizationContext: ['groups' => ['profile', 'public']])
    ],
)]
#[ApiResource(
    operations: [
        // Displays private profile of the trainers
        new Get(
            controller: UserController::class,
            normalizationContext: ['groups' => ['profile', 'admin']],
        ),
        // Change partially the profile of the trainer
        new Put(security: "is_granted('ROLE_USER')")
    ],
    security: "is_granted('ROLE_USER')"
)]
#[ApiResource(
    uriTemplate: "/passwords",
    operations: [new Put(processor: UserPasswordHasher::class)],
    normalizationContext: ['groups' => ['update:password']],
    security: "is_granted('ROLE_USER')",
)]
#[UniqueEntity(fields: ["username"])]
class Trainer implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 180, unique: true)]
    #[Groups(['public', 'admin'])]
    #[Assert\NotNull(groups: ['user:register']), Assert\NotBlank(groups: ['user:register'])]
    private ?string $username = null;

    #[ORM\Column]
    #[Groups(['admin'])]
    private array $roles = [];

    /**
     * @var string The hashed password
     */
    #[ORM\Column]
    #[Assert\NotNull(groups: ['user:register'])]
    private ?string $password = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(['public', 'admin'])]
    #[Assert\NotNull(groups: ['user:register'])]
    private ?string $firstName = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(['public', 'admin'])]
    private ?string $lastName = null;

    #[ORM\OneToMany(mappedBy: 'trainer', targetEntity: Bag::class)]
    #[Groups(['admin'])]
    private Collection $bags;

    #[ORM\Column(nullable: true)]
    #[Groups(['public', 'admin'])]
    private ?int $experience = null;

    #[UserPassword(groups: ['update:password'])]
    private ?string $currentPassword = null;

    private ?string $oldPassword = null;

    #[ORM\OneToMany(mappedBy: 'challenger', targetEntity: Duel::class, orphanRemoval: true)]
    private Collection $duels;

    public function __construct()
    {
        $this->bags = new ArrayCollection();
        $this->duels = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUsername(): ?string
    {
        return $this->username;
    }

    public function setUsername(string $username): self
    {
        $this->username = $username;

        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return (string)$this->username;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials()
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    public function getFirstName(): ?string
    {
        return $this->firstName;
    }

    public function setFirstName(?string $firstName): self
    {
        $this->firstName = $firstName;

        return $this;
    }

    public function getLastName(): ?string
    {
        return $this->lastName;
    }

    public function setLastName(?string $lastName): self
    {
        $this->lastName = $lastName;

        return $this;
    }

    /**
     * @return Collection<int, Bag>
     */
    public function getBags(): Collection
    {
        return $this->bags;
    }

    public function addBag(Bag $bag): self
    {
        if (!$this->bags->contains($bag)) {
            $this->bags->add($bag);
            $bag->setTrainer($this);
        }

        return $this;
    }

    public function removeBag(Bag $bag): self
    {
        if ($this->bags->removeElement($bag)) {
            // set the owning side to null (unless already changed)
            if ($bag->getTrainer() === $this) {
                $bag->setTrainer(null);
            }
        }

        return $this;
    }

    public function getExperience(): ?int
    {
        return $this->experience;
    }

    public function setExperience(?int $experience): self
    {
        $this->experience = $experience;

        return $this;
    }

    /**
     * @return Collection<int, Duel>
     */
    public function getDuels(): Collection
    {
        return $this->duels;
    }

    public function addDuel(Duel $duel): self
    {
        if (!$this->duels->contains($duel)) {
            $this->duels->add($duel);
            $duel->setChallenger($this);
        }

        return $this;
    }

    public function removeDuel(Duel $duel): self
    {
        if ($this->duels->removeElement($duel)) {
            // set the owning side to null (unless already changed)
            if ($duel->getChallenger() === $this) {
                $duel->setChallenger(null);
            }
        }

        return $this;
    }
}
