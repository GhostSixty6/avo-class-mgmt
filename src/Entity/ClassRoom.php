<?php

namespace App\Entity;

use App\Repository\ClassRoomRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ClassRoomRepository::class)]
#[ORM\Table(name: '`classroom`')]
class ClassRoom
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\Column(length: 1)]
    private ?int $status = 1;

    #[ORM\ManyToMany(targetEntity: User::class, inversedBy: 'classRooms')]
    private Collection $teachers;

    #[ORM\ManyToMany(targetEntity: Student::class, inversedBy: 'classRooms')]
    private Collection $students;


    public function __construct()
    {
        $this->teachers = new ArrayCollection();
        $this->students = new ArrayCollection();
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
     * @return Collection<int, User>
     */
    public function getTeachers(): Collection
    {
        return $this->teachers;
    }

    public function getTeacherCount(): ?int
    {
        return count($this->teachers);
    }

    public function getTeachersIds(): array
    {
        $ids = [];

        foreach ($this->teachers as $teacher) {
            $ids[] = $teacher->getId();
        }

        return $ids;
    }

    public function addTeacher(User $teacher): static
    {
        if (!$this->teachers->contains($teacher)) {
            $this->teachers->add($teacher);
        }

        return $this;
    }

    public function removeTeacher(User $teacher): static
    {
        $this->teachers->removeElement($teacher);

        return $this;
    }

    /**
     * @return Collection<int, Student>
     */
    public function getStudents(): Collection
    {
        return $this->students;
    }

    public function getStudentsIds(): array
    {
        $ids = [];

        foreach ($this->students as $student) {
            $ids[] = $student->getId();
        }

        return $ids;
    }

    public function getStudentCount(): ?int
    {
        return count($this->students);
    }

    public function addStudent(Student $student): static
    {
        if (!$this->students->contains($student)) {
            $this->students->add($student);
        }

        return $this;
    }

    public function removeStudent(Student $student): static
    {
        $this->students->removeElement($student);

        return $this;
    }
}
