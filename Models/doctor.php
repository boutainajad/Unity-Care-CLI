<?php
// Models/Doctor.php

require_once __DIR__ . "/Person.php";

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
        string $specialization = '',
        int $yearsOfService = 0,
        int $departmentId = 0
    ) {
        //  (Person)
        parent::__construct($id, $firstName, $lastName, $email, $phone, $dateOfBirth);
        
        // Doctor
        $this->specialization = $specialization;
        $this->yearsOfService = $yearsOfService;
        $this->departmentId = $departmentId;
    }

    // Doctor
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

    public function __toString(): string
    {
        return "Dr. " . $this->getFullName() . " - " . $this->specialization;
    }
}