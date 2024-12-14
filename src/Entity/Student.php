<?php

namespace App\Entity;

use App\Repository\StudentRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: StudentRepository::class)]
class Student
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\Column(length: 1)]
    private ?int $status = 1;

    #[ORM\ManyToMany(targetEntity: ClassRoom::class, mappedBy: 'students')]
    private Collection $classRooms;

    public function __construct()
    {
        $this->classRooms = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function getStatus(): ?int
    {
        return $this->status;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function setStatus(int $status): static
    {
        $this->status = $status;

        return $this;
    }

    /**
     * @return Collection<int, ClassRoom>
     */
    public function getClassRooms(): Collection
    {
        return $this->classRooms;
    }

    public function addClassRoom(ClassRoom $classRoom): static
    {
        if (!$this->classRooms->contains($classRoom)) {
            $this->classRooms->add($classRoom);
            $classRoom->addStudent($this);
        }

        return $this;
    }

    public function removeClassRoom(ClassRoom $classRoom): static
    {
        if ($this->classRooms->removeElement($classRoom)) {
            $classRoom->removeStudent($this);
        }

        return $this;
    }
}
