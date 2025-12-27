<?php

require_once "Person.php";

class Doctor extends Person
{
    private string $specialization;
    private int $yearsOfService;
    private int $departmentId;

    public function __construct(
        int $id,
        string $firstName,
        string $lastName,
        string $email,
        string $phone,
        string $dateOfBirth,
        string $specialization,
        int $yearsOfService,
        int $departmentId
    ) {
        parent::__construct($id, $firstName, $lastName, $email, $phone, $dateOfBirth);
        $this->specialization = $specialization;
        $this->yearsOfService = $yearsOfService;
        $this->departmentId = $departmentId;
    }

    public function getSpecialization(): string
    {
        return $this->specialization;
    }

    public function setSpecialization(string $specialization): void
    {
        $this->specialization = $specialization;
    }

    public function getYearsOfService(): int
    {
        return $this->yearsOfService;
    }

    public function setYearsOfService(int $years): void
    {
        $this->yearsOfService = $years;
    }

    public function getDepartmentId(): int
    {
        return $this->departmentId;
    }

    public function setDepartmentId(int $departmentId): void
    {
        $this->departmentId = $departmentId;
    }
}
