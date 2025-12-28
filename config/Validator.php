<?php
// config/Validator.php

class Validator
{
    private array $errors = [];

    // recupure erreur 
    public function getErrors(): array
    {
        return $this->errors;
    }

    // verifier erreur 
    public function hasErrors(): bool
    {
        return !empty($this->errors);
    }

//    afficher erreur 
    public function displayErrors(): void
    {
        if ($this->hasErrors()) {
            echo "\npas de  Erreurs de validation:\n";
            foreach ($this->errors as $error) {
                echo "  • $error\n";
            }
        }
    }


    public function reset(): void
    {
        $this->errors = [];
    }

    public function required(string $value, string $fieldName): bool
    {
        if (empty(trim($value))) {
            $this->errors[] = "$fieldName est obligatoire";
            return false;
        }
        return true;
    }

 
    public function email(string $email): bool
    {
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->errors[] = "Email invalide: $email";
            return false;
        }
        return true;
    }

    
    public function phone(string $phone): bool
    {
        $pattern = '/^(\+212|0)[5-7][0-9]{8}$/';
        $cleanPhone = preg_replace('/[\s\-]/', '', $phone);
        
        if (!preg_match($pattern, $cleanPhone)) {
            $this->errors[] = "Numero de telephone invalide: $phone (Format: 0612345678)";
            return false;
        }
        return true;
    }

  
    public function date(string $date, string $format = 'Y-m-d'): bool
    {
        $d = DateTime::createFromFormat($format, $date);
        if (!$d || $d->format($format) !== $date) {
            $this->errors[] = "Date invalide: $date (Format attendu: $format)";
            return false;
        }
        return true;
    }

  
    public function dateOfBirth(string $date): bool
    {
        if (!$this->date($date)) {
            return false;
        }

        $birthDate = new DateTime($date);
        $today = new DateTime();

        if ($birthDate > $today) {
            $this->errors[] = "Date de naissance ne peut pas être dans le futur";
            return false;
        }

       
        return true;
    }

   
    public function minLength(string $value, int $min, string $fieldName): bool
    {
        if (strlen($value) < $min) {
            $this->errors[] = "$fieldName doit contenir au moins $min caractères";
            return false;
        }
        return true;
    }

  
    public function maxLength(string $value, int $max, string $fieldName): bool
    {
        if (strlen($value) > $max) {
            $this->errors[] = "$fieldName ne doit pas depasser $max caractères";
            return false;
        }
        return true;
    }

    
    public function positiveInteger(mixed $value, string $fieldName): bool
    {
        if (!is_numeric($value) || (int)$value < 0) {
            $this->errors[] = "$fieldName doit être un nombre positif";
            return false;
        }
        return true;
    }

    public function gender(string $gender): bool
    {
        $validGenders = ['Male', 'Female', 'male', 'female', 'M', 'F'];
        if (!in_array($gender, $validGenders)) {
            $this->errors[] = "Genre invalide: $gender (Male/Female acceptes)";
            return false;
        }
        return true;
    }

   
    public function validatePatient(array $data): bool
    {
        $this->reset();

        // Prenom
        if (isset($data['first_name'])) {
            $this->required($data['first_name'], 'Prenom');
            $this->minLength($data['first_name'], 2, 'Prenom');
            $this->maxLength($data['first_name'], 50, 'Prenom');
        }

        // Nom
        if (isset($data['last_name'])) {
            $this->required($data['last_name'], 'Nom');
            $this->minLength($data['last_name'], 2, 'Nom');
            $this->maxLength($data['last_name'], 50, 'Nom');
        }

        // Email
        if (isset($data['email'])) {
            $this->required($data['email'], 'Email');
            $this->email($data['email']);
        }

        // Telephone
        if (isset($data['phone_number']) && !empty($data['phone_number'])) {
            $this->phone($data['phone_number']);
        }

        // Date de naissance
        if (isset($data['date_of_birth'])) {
            $this->required($data['date_of_birth'], 'Date de naissance');
            $this->dateOfBirth($data['date_of_birth']);
        }

        // Genre
        if (isset($data['genre'])) {
            $this->required($data['genre'], 'Genre');
            $this->gender($data['genre']);
        }

        return !$this->hasErrors();
    }

   
    public function validateDoctor(array $data): bool
    {
        $this->reset();

        // Prenom
        if (isset($data['firs_name'])) {
            $this->required($data['firs_name'], 'Prenom');
            $this->minLength($data['firs_name'], 2, 'Prenom');
            $this->maxLength($data['firs_name'], 50, 'Prenom');
        }

        // Nom
        if (isset($data['last_name'])) {
            $this->required($data['last_name'], 'Nom');
            $this->minLength($data['last_name'], 2, 'Nom');
            $this->maxLength($data['last_name'], 50, 'Nom');
        }

        // Email (optionnel pour doctor)
        if (isset($data['email']) && !empty($data['email'])) {
            $this->email($data['email']);
        }

        // Telephone (optionnel)
        if (isset($data['phone_number']) && !empty($data['phone_number'])) {
            $this->phone($data['phone_number']);
        }

        // Date de naissance (optionnel)
        if (isset($data['date_of_birth']) && !empty($data['date_of_birth'])) {
            $this->dateOfBirth($data['date_of_birth']);
        }

        // Annees de service
        if (isset($data['years_of_service'])) {
            $this->positiveInteger($data['years_of_service'], 'Annees de service');
        }

        // Department ID
        if (isset($data['department_id'])) {
            $this->required($data['department_id'], 'Departement');
            $this->positiveInteger($data['department_id'], 'Departement');
        }

        return !$this->hasErrors();
    }

//    valider departements
    public function validateDepartment(array $data): bool
    {
        $this->reset();

        // Nom du departement
        if (isset($data['department_name'])) {
            $this->required($data['department_name'], 'Nom du departement');
            $this->minLength($data['department_name'], 2, 'Nom du departement');
            $this->maxLength($data['department_name'], 100, 'Nom du departement');
        }

        // Localisation
        if (isset($data['location'])) {
            $this->required($data['location'], 'Localisation');
            $this->minLength($data['location'], 2, 'Localisation');
            $this->maxLength($data['location'], 200, 'Localisation');
        }

        return !$this->hasErrors();
    }
}