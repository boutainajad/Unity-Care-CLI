<?php

require_once __DIR__ . "/BaseModel.php";
require_once __DIR__ . "/Patient.php";

class PatientModel extends BaseModel
{
    protected string $table = 'patients';
    protected string $primaryKey = 'patient_id';

    
    private function toPatient(array $data): Patient
    {
        return new Patient(
            (int)$data['patient_id'],
            $data['first_name'],
            $data['last_name'],
            $data['email'],
            $data['phone_number'],
            $data['date_of_birth'],
            $data['patient_code'] ?? '',
            $data['genre'] ?? '',
            $data['adress'] ?? ''
        );
    }

    // recuperer tout les patients 
    public function getAllAsObjects(): array
    {
        $data = $this->getAll();
        return array_map(fn($row) => $this->toPatient($row), $data);
    }

    
    //  Recuperer un patient par ID
    public function getByIdAsObject(int $id): ?Patient
    {
        $data = $this->getById($id);
        return $data ? $this->toPatient($data) : null;
    }

    // Recherche par nom
    public function searchByName(string $name): array
    {
        $stmt = $this->conn->prepare(
            "SELECT * FROM {$this->table} 
             WHERE first_name LIKE :name OR last_name LIKE :name"
        );
        $searchValue = "%{$name}%";
        $stmt->bindParam(':name', $searchValue);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Recherche par email
    public function findByEmail(string $email): ?array
    {
        $stmt = $this->conn->prepare(
            "SELECT * FROM {$this->table} WHERE email = :email"
        );
        $stmt->bindParam(':email', $email);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result ?: null;
    }

    // Recherche par telephone
    public function findByPhone(string $phone): ?array
    {
        $stmt = $this->conn->prepare(
            "SELECT * FROM {$this->table} WHERE phone_number = :phone"
        );
        $stmt->bindParam(':phone', $phone);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result ?: null;
    }

    // Statistiques
    public function getStatistics(): array
    {
        $stats = [];
        $stats['total'] = $this->count();
        
        $stmt = $this->conn->query(
            "SELECT genre, COUNT(*) as count 
             FROM {$this->table} 
             GROUP BY genre"
        );
        $stats['by_gender'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        return $stats;
    }
}