<?php

namespace App\Entity;

use App\Repository\BudgetRepository;
use DH\Auditor\Provider\Doctrine\Auditing\Annotation\Auditable;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: BudgetRepository::class)]
#[Auditable]
class Budget
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'budgets')]
    private ?Department $department = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\Column]
    private ?float $total_budget = null;

    #[ORM\Column(nullable: true)]
    private ?float $per_employee_budget = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDepartment(): ?Department
    {
        return $this->department;
    }

    public function setDepartment(?Department $department): static
    {
        $this->department = $department;

        return $this;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getTotalBudget(): ?float
    {
        return $this->total_budget;
    }

    public function setTotalBudget(float $total_budget): static
    {
        $this->total_budget = $total_budget;

        return $this;
    }

    public function getPerEmployeeBudget(): ?float
    {
        return $this->per_employee_budget;
    }

    public function setPerEmployeeBudget(?float $per_employee_budget): static
    {
        $this->per_employee_budget = $per_employee_budget;

        return $this;
    }
}
