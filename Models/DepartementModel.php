<?php

require_once __DIR__ . "/BaseModel.php";

class DepartmentModel extends BaseModel
{
    protected string $table = 'departments';
    protected string $primaryKey = 'department_id';

    public function searchByName(string $name): array
    {
        return $this->search('department_name', $name);
    }

    public function searchByLocation(string $location): array
    {
        return $this->search('location', $location);
    }

    public function getAllWithDoctorCount(): array
    {
        $sql = "SELECT d.*, COUNT(doc.doctor_id) as doctor_count 
                FROM {$this->table} d
                LEFT JOIN doctors doc ON d.department_id = doc.department_id
                GROUP BY d.department_id";
        $stmt = $this->conn->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getByIdWithDoctorCount(int $id): ?array
    {
        $sql = "SELECT d.*, COUNT(doc.doctor_id) as doctor_count 
                FROM {$this->table} d
                LEFT JOIN doctors doc ON d.department_id = doc.department_id
                WHERE d.{$this->primaryKey} = :id
                GROUP BY d.department_id";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result ?: null;
    }

    public function hasDoctors(int $id): bool
    {
        $stmt = $this->conn->prepare(
            "SELECT COUNT(*) FROM doctors WHERE department_id = :id"
        );
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchColumn() > 0;
    }

    public function getStatistics(): array
    {
        $stats = [];
        $stats['total'] = $this->count();
        
        $stmt = $this->conn->query(
            "SELECT d.department_name, COUNT(doc.doctor_id) as doctor_count 
             FROM {$this->table} d
             LEFT JOIN doctors doc ON d.department_id = doc.department_id
             GROUP BY d.department_id, d.department_name
             ORDER BY doctor_count DESC
             LIMIT 5"
        );
        $stats['top_departments'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        return $stats;
    }
}

