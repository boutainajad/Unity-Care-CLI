<?php

require_once __DIR__ . "/../Models/DoctorModel.php";
require_once __DIR__ . "/../Models/DepartementModel.php";
require_once __DIR__ . "/../config/Validator.php";

class DoctorCrud
{
    private DoctorModel $model;
    private DepartmentModel $departmentModel;
    private Validator $validator;

    public function __construct(PDO $conn)
    {
        $this->model = new DoctorModel($conn);
        $this->departmentModel = new DepartmentModel($conn);
        $this->validator = new Validator();
    }

    // Afficher tous les docteurs
    public function listAll(): void
    {
        $doctors = $this->model->getAllWithDepartment();
        
        if (empty($doctors)) {
            echo "\n[i] Aucun docteur trouve.\n";
            return;
        }

        echo "\n=== Liste des Docteurs ===\n";
        echo str_repeat("-", 100) . "\n";
        printf("%-5s %-15s %-15s %-30s\n", 
            "ID", "Prenom", "Nom", "Departement");
        echo str_repeat("-", 100) . "\n";

        foreach ($doctors as $doctor) {
            printf("%-5s %-15s %-15s %-30s\n",
                $doctor['doctor_id'],
                substr($doctor['firs_name'], 0, 15),
                substr($doctor['last_name'], 0, 15),
                substr($doctor['department_name'] ?? 'N/A', 0, 30)
            );
        }
        echo str_repeat("-", 100) . "\n";
        echo "Total: " . count($doctors) . " docteur(s)\n";
    }

    // Chercher un docteur
    public function search(): void
    {
        echo "\n=== Rechercher un Docteur ===\n";
        echo "1. Par nom\n";
        echo "2. Par departement\n";
        echo "Choix: ";
        
        $choice = trim(fgets(STDIN));
        
        switch ($choice) {
            case '1':
                echo "Entrez le nom: ";
                $name = trim(fgets(STDIN));
                if (!empty($name)) {
                    $results = $this->model->searchByName($name);
                } else {
                    echo "[!] Nom ne peut pas être vide\n";
                    return;
                }
                break;
            case '2':
                $this->listDepartments();
                echo "ID du departement: ";
                $deptId = (int)trim(fgets(STDIN));
                
                if (!$this->departmentModel->exists($deptId)) {
                    echo "\n[!] Departement invalide.\n";
                    return;
                }
                
                $results = $this->model->getByDepartment($deptId);
                break;
            default:
                echo "[!] Choix invalide.\n";
                return;
        }

        if (empty($results)) {
            echo "\n[i] Aucun docteur trouve.\n";
            return;
        }

        echo "\n=== Resultats de la recherche ===\n";
        foreach ($results as $doctor) {
            $this->displayDoctor($doctor);
        }
    }

    // Ajouter un docteur
    public function add(): void
    {
        echo "\n=== Ajouter un Docteur ===\n";
        
        echo "Prenom: ";
        $firstName = trim(fgets(STDIN));
        
        echo "Nom: ";
        $lastName = trim(fgets(STDIN));
        
        echo "Email (optionnel): ";
        $email = trim(fgets(STDIN));
        
        echo "Telephone (optionnel): ";
        $phone = trim(fgets(STDIN));
        
        echo "Date de naissance (YYYY-MM-DD, optionnel): ";
        $dob = trim(fgets(STDIN));
        
        echo "Specialisation: ";
        $specialization = trim(fgets(STDIN));
        
        echo "Annees d'experience: ";
        $yearsOfService = trim(fgets(STDIN));
        
        $this->listDepartments();
        echo "ID du departement: ";
        $deptId = (int)trim(fgets(STDIN));

        $data = [
            'firs_name' => $firstName,
            'last_name' => $lastName,
            'email' => $email,
            'phone_number' => $phone,
            'date_of_birth' => $dob,
            'specialization' => $specialization,
            'years_of_service' => (int)$yearsOfService,
            'department_id' => $deptId
        ];

        // Valider les donnees
        if (!$this->validator->validateDoctor($data)) {
            $this->validator->displayErrors();
            return;
        }

        // Verifier que le departement existe
        if (!$this->departmentModel->exists($deptId)) {
            echo "\n[!] Departement invalide.\n";
            return;
        }

        try {
            $id = $this->model->create($data);
            echo "\n Docteur ajoute avec succès! ID: {$id}\n";
        } catch (Exception $e) {
            echo "\n[!] Erreur: " . $e->getMessage() . "\n";
        }
    }

    // Modifier un docteur
    public function update(): void
    {
        echo "\n=== Modifier un Docteur ===\n";
        echo "ID du docteur: ";
        $id = (int)trim(fgets(STDIN));

        $doctor = $this->model->getByIdWithDepartment($id);
        if (!$doctor) {
            echo "\n[!] Docteur non trouve.\n";
            return;
        }

        echo "\n--- Informations actuelles ---\n";
        $this->displayDoctor($doctor);

        echo "\n--- Nouvelles informations (laissez vide pour garder l'ancienne valeur) ---\n";
        
        echo "Prenom [{$doctor['firs_name']}]: ";
        $firstName = trim(fgets(STDIN)) ?: $doctor['firs_name'];
        
        echo "Nom [{$doctor['last_name']}]: ";
        $lastName = trim(fgets(STDIN)) ?: $doctor['last_name'];
        
        echo "Email [" . ($doctor['email'] ?? 'N/A') . "]: ";
        $emailInput = trim(fgets(STDIN));
        $email = $emailInput ?: ($doctor['email'] ?? '');
        
        echo "Telephone [" . ($doctor['phone_number'] ?? 'N/A') . "]: ";
        $phoneInput = trim(fgets(STDIN));
        $phone = $phoneInput ?: ($doctor['phone_number'] ?? '');
        
        echo "Date de naissance [" . ($doctor['date_of_birth'] ?? 'N/A') . "]: ";
        $dobInput = trim(fgets(STDIN));
        $dob = $dobInput ?: ($doctor['date_of_birth'] ?? '');
        
        echo "Specialisation [" . ($doctor['specialization'] ?? 'N/A') . "]: ";
        $specializationInput = trim(fgets(STDIN));
        $specialization = $specializationInput ?: ($doctor['specialization'] ?? '');
        
        echo "Annees d'experience [" . ($doctor['years_of_service'] ?? '0') . "]: ";
        $yearsInput = trim(fgets(STDIN));
        $yearsOfService = $yearsInput ? (int)$yearsInput : ($doctor['years_of_service'] ?? 0);
        
        $this->listDepartments();
        echo "ID du departement [{$doctor['department_id']}]: ";
        $deptInput = trim(fgets(STDIN));
        $deptId = $deptInput ? (int)$deptInput : $doctor['department_id'];

        $data = [
            'firs_name' => $firstName,
            'last_name' => $lastName,
            'email' => $email,
            'phone_number' => $phone,
            'date_of_birth' => $dob,
            'specialization' => $specialization,
            'years_of_service' => $yearsOfService,
            'department_id' => $deptId
        ];

        // Valider les donnees
        if (!$this->validator->validateDoctor($data)) {
            $this->validator->displayErrors();
            return;
        }

        // Verifier que le departement existe
        if (!$this->departmentModel->exists($deptId)) {
            echo "\n[!] Departement invalide.\n";
            return;
        }

        try {
            $this->model->update($id, $data);
            echo "\n Docteur modifie avec succès!\n";
        } catch (Exception $e) {
            echo "\n[!] Erreur: " . $e->getMessage() . "\n";
        }
    }

    // Supprimer un docteur
    public function delete(): void
    {
        echo "\n=== Supprimer un Docteur ===\n";
        echo "ID du docteur: ";
        $id = (int)trim(fgets(STDIN));

        $doctor = $this->model->getByIdWithDepartment($id);
        if (!$doctor) {
            echo "\n[!] Docteur non trouve.\n";
            return;
        }

        echo "\n--- Docteur à supprimer ---\n";
        $this->displayDoctor($doctor);

        echo "\nÊtes-vous sûr de vouloir supprimer ce docteur? (oui/non): ";
        $confirm = trim(fgets(STDIN));

        if (strtolower($confirm) === 'oui') {
            try {
                $this->model->delete($id);
                echo "\n Docteur supprime avec succès!\n";
            } catch (Exception $e) {
                echo "\n[!] Erreur: " . $e->getMessage() . "\n";
            }
        } else {
            echo "\n[i] Suppression annulee.\n";
        }
    }

    // Afficher un docteur
    private function displayDoctor(array $doctor): void
    {
        echo "\n";
        echo "ID: {$doctor['doctor_id']}\n";
        echo "Nom: {$doctor['firs_name']} {$doctor['last_name']}\n";
        echo "Email: " . ($doctor['email'] ?? 'N/A') . "\n";
        echo "Telephone: " . ($doctor['phone_number'] ?? 'N/A') . "\n";
        echo "Date de naissance: " . ($doctor['date_of_birth'] ?? 'N/A') . "\n";
        echo "Specialisation: " . ($doctor['specialization'] ?? 'N/A') . "\n";
        echo "Annees d'experience: " . ($doctor['years_of_service'] ?? '0') . "\n";
        echo "Departement: " . ($doctor['department_name'] ?? 'N/A') . "\n";
        if (isset($doctor['location'])) {
            echo "Localisation: {$doctor['location']}\n";
        }
        echo str_repeat("-", 50) . "\n";
    }

    // Afficher les departements
    private function listDepartments(): void
    {
        $departments = $this->departmentModel->getAll();
        echo "\n--- Departements disponibles ---\n";
        foreach ($departments as $dept) {
            echo "{$dept['department_id']}. {$dept['department_name']} ({$dept['location']})\n";
        }
    }
}