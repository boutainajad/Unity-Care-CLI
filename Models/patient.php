<?php

require_once "Person.php";

class Patient extends Person
{
    private string $patientCode;

    public function __construct(
        int $id,
        string $firstName,
        string $lastName,
        string $email,
        string $phone,
        string $dateOfBirth,
        string $patientCode
    ) {
        parent::__construct($id, $firstName, $lastName, $email, $phone, $dateOfBirth);
        $this->patientCode = $patientCode;
    }

    public function getPatientCode(): string
    {
        return $this->patientCode;
    }

    public function setPatientCode(string $patientCode): void
    {
        $this->patientCode = $patientCode;
    }

    public function __toString(): string
    {
        return $this->getFullName() . " | Code Patient: " . $this->patientCode;
    }
}
