<?php
// Models/DoctorModel.php

require_once __DIR__ . "/BaseModel.php";
require_once __DIR__ . "/Doctor.php";

class DoctorModel extends BaseModel
{
    protected string $table = 'doctors';
    protected string $primaryKey = 'doctor_id';

   
    private function toDoctor(array $data): Doctor
    {
        return new Doctor(
            (int)$data['doctor_id'],
            $data['firs_name'],
            $data['last_name'],
            $data['email'] ?? '',
            $data['phone_number'] ?? '',
            $data['date_of_birth'] ?? '',
            $data['specialization'] ?? '',
            (int)($data['years_of_service'] ?? 0),
            (int)$data['department_id']
        );
    }

    public function getAllAsObjects(): array
    {
        $data = $this->getAllWithDepartment();
        return array_map(fn($row) => $this->toDoctor($row), $data);
    }

 
    public function getByIdAsObject(int $id): ?Doctor
    {
        $data = $this->getByIdWithDepartment($id);
        return $data ? $this->toDoctor($data) : null;
    }

    // Recherche par nom
    public function searchByName(string $name): array
    {
        $stmt = $this->conn->prepare(
            "SELECT d.*, dep.department_name, dep.location 
             FROM {$this->table} d
             LEFT JOIN departments dep ON d.department_id = dep.department_id
             WHERE d.firs_name LIKE :name OR d.last_name LIKE :name"
        );
        $searchValue = "%{$name}%";
        $stmt->bindParam(':name', $searchValue);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Recuperer tous avec departement
    public function getAllWithDepartment(): array
    {
        $sql = "SELECT d.*, dep.department_name, dep.location 
                FROM {$this->table} d
                LEFT JOIN departments dep ON d.department_id = dep.department_id";
        $stmt = $this->conn->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Recuperer par ID avec departement
    public function getByIdWithDepartment(int $id): ?array
    {
        $sql = "SELECT d.*, dep.department_name, dep.location 
                FROM {$this->table} d
                LEFT JOIN departments dep ON d.department_id = dep.department_id
                WHERE d.{$this->primaryKey} = :id";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result ?: null;
    }

    // Recuperer par departement
    public function getByDepartment(int $departmentId): array
    {
        $stmt = $this->conn->prepare(
            "SELECT d.*, dep.department_name, dep.location 
             FROM {$this->table} d
             LEFT JOIN departments dep ON d.department_id = dep.department_id
             WHERE d.department_id = :dept_id"
        );
        $stmt->bindParam(':dept_id', $departmentId, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Statistiques
    public function getStatistics(): array
    {
        $stats = [];
        $stats['total'] = $this->count();
        
        $stmt = $this->conn->query(
            "SELECT dep.department_name, COUNT(d.doctor_id) as count 
             FROM departments dep
             LEFT JOIN {$this->table} d ON dep.department_id = d.department_id
             GROUP BY dep.department_id, dep.department_name
             ORDER BY count DESC"
        );
        $stats['by_department'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        return $stats;
    }
}