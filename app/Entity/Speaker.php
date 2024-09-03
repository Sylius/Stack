<?php

namespace App\Entity;

use App\Form\SpeakerType;
use App\Grid\SpeakerGrid;
use App\Grid\SpeakerGridImproved;
use App\Repository\SpeakerRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Sylius\Resource\Metadata\AsResource;
use Sylius\Resource\Metadata\BulkDelete;
use Sylius\Resource\Metadata\Create;
use Sylius\Resource\Metadata\Delete;
use Sylius\Resource\Metadata\Index;
use Sylius\Resource\Metadata\Update;
use Sylius\Resource\Model\ResourceInterface;

#[ORM\Entity(repositoryClass: SpeakerRepository::class)]
#[AsResource(
    section: 'admin',
    formType: SpeakerType::class,
    templatesDir: '@SyliusAdminUi/crud',
    routePrefix: '/admin',
    operations: [
        new Create(),
        new Update(),
        new Index(grid: SpeakerGrid::class),
        new Delete(),
        new BulkDelete(),
    ],
)]
class Speaker implements ResourceInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $firstName = null;

    #[ORM\Column(length: 255)]
    private ?string $lastName = null;

    /**
     * @var Collection<int, Talk>
     */
    #[ORM\OneToMany(mappedBy: 'speaker', targetEntity: Talk::class, orphanRemoval: true)]
    private Collection $talks;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $companyName = null;

    #[ORM\OneToOne(cascade: ['persist', 'remove'])]
    private ?SpeakerAvatar $avatar = null;

    public function __construct()
    {
        $this->talks = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getFirstName(): ?string
    {
        return $this->firstName;
    }

    public function setFirstName(string $firstName): static
    {
        $this->firstName = $firstName;

        return $this;
    }

    public function getLastName(): ?string
    {
        return $this->lastName;
    }

    public function setLastName(string $lastName): static
    {
        $this->lastName = $lastName;

        return $this;
    }

    public function getFullName(): string
    {
        return $this->firstName . ' ' . $this->lastName;
    }

    /**
     * @return Collection<int, Talk>
     */
    public function getTalks(): Collection
    {
        return $this->talks;
    }

    public function addTalk(Talk $talk): static
    {
        if (!$this->talks->contains($talk)) {
            $this->talks->add($talk);
            $talk->setSpeaker($this);
        }

        return $this;
    }

    public function removeTalk(Talk $talk): static
    {
        if ($this->talks->removeElement($talk)) {
            // set the owning side to null (unless already changed)
            if ($talk->getSpeaker() === $this) {
                $talk->setSpeaker(null);
            }
        }

        return $this;
    }

    public function getCompanyName(): ?string
    {
        return $this->companyName;
    }

    public function setCompanyName(?string $companyName): static
    {
        $this->companyName = $companyName;

        return $this;
    }

    public function getAvatar(): ?SpeakerAvatar
    {
        return $this->avatar;
    }

    public function setAvatar(?SpeakerAvatar $avatar): static
    {
        $this->avatar = $avatar;

        return $this;
    }
}
