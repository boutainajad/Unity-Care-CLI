<?php

require_once __DIR__ . "/Person.php";

class Patient extends Person
{
    private string $patientCode;
    private string $genre;
    private string $address;

    public function __construct(
        int $id,
        string $firstName,
        string $lastName,
        string $email,
        string $phone,
        string $dateOfBirth,
        string $patientCode = '',
        string $genre = '',
        string $address = ''
    ) {
        //  (Person)
        parent::__construct($id, $firstName, $lastName, $email, $phone, $dateOfBirth);
        
        // Initialiser les attributs specifiques à Patient
        $this->patientCode = $patientCode;
        $this->genre = $genre;
        $this->address = $address;
    }


    public function getPatientCode(): string
    {
        return $this->patientCode;
    }

    public function setPatientCode(string $patientCode): void
    {
        $this->patientCode = $patientCode;
    }

    public function getGenre(): string
    {
        return $this->genre;
    }

    public function setGenre(string $genre): void
    {
        $this->genre = $genre;
    }

    public function getAddress(): string
    {
        return $this->address;
    }

    public function setAddress(string $address): void
    {
        $this->address = $address;
    }

    public function __toString(): string
    {
        return $this->getFullName() . " | Code: " . $this->patientCode;
    }
}