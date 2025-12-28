<?php

require_once __DIR__ . "/../Models/DepartementModel.php";
require_once __DIR__ . "/../config/Validator.php";

class DepartmentCrud
{
    private DepartmentModel $model;
    private Validator $validator;

    public function __construct(PDO $conn)
    {
        $this->model = new DepartmentModel($conn);
        $this->validator = new Validator();
    }

    // Afficher tous les departements
    public function listAll(): void
    {
        $departments = $this->model->getAllWithDoctorCount();
        
        if (empty($departments)) {
            echo "\n[i] Aucun departement trouve.\n";
            return;
        }

        echo "\n=== Liste des Departements ===\n";
        echo str_repeat("-", 80) . "\n";
        printf("%-5s %-30s %-25s %-10s\n", 
            "ID", "Nom", "Localisation", "Docteurs");
        echo str_repeat("-", 80) . "\n";

        foreach ($departments as $dept) {
            printf("%-5s %-30s %-25s %-10s\n",
                $dept['department_id'],
                substr($dept['department_name'], 0, 30),
                substr($dept['location'], 0, 25),
                $dept['doctor_count']
            );
        }
        echo str_repeat("-", 80) . "\n";
        echo "Total: " . count($departments) . " departement(s)\n";
    }

    // Chercher un departement
    public function search(): void
    {
        echo "\n=== Rechercher un Departement ===\n";
        echo "1. Par nom\n";
        echo "2. Par localisation\n";
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
                echo "Entrez la localisation: ";
                $location = trim(fgets(STDIN));
                if (!empty($location)) {
                    $results = $this->model->searchByLocation($location);
                } else {
                    echo "[!] Localisation ne peut pas être vide\n";
                    return;
                }
                break;
            default:
                echo "[!] Choix invalide.\n";
                return;
        }

        if (empty($results)) {
            echo "\n[i] Aucun departement trouve.\n";
            return;
        }

        echo "\n=== Resultats de la recherche ===\n";
        foreach ($results as $dept) {
            $this->displayDepartment($dept);
        }
    }

    // Ajouter un departement
    public function add(): void
    {
        echo "\n=== Ajouter un Departement ===\n";
        
        echo "Nom du departement: ";
        $name = trim(fgets(STDIN));
        
        echo "Localisation: ";
        $location = trim(fgets(STDIN));

        $data = [
            'department_name' => $name,
            'location' => $location
        ];

        // Valider les donnees
        if (!$this->validator->validateDepartment($data)) {
            $this->validator->displayErrors();
            return;
        }

        try {
            $id = $this->model->create($data);
            echo "\n Departement ajoute avec succès! ID: {$id}\n";
        } catch (Exception $e) {
            echo "\n[!] Erreur: " . $e->getMessage() . "\n";
        }
    }

    // Modifier un departement
    public function update(): void
    {
        echo "\n=== Modifier un Departement ===\n";
        echo "ID du departement: ";
        $id = (int)trim(fgets(STDIN));

        $dept = $this->model->getByIdWithDoctorCount($id);
        if (!$dept) {
            echo "\n[!] Departement non trouve.\n";
            return;
        }

        echo "\n--- Informations actuelles ---\n";
        $this->displayDepartment($dept);

        echo "\n--- Nouvelles informations (laissez vide pour garder l'ancienne valeur) ---\n";
        
        echo "Nom [{$dept['department_name']}]: ";
        $name = trim(fgets(STDIN)) ?: $dept['department_name'];
        
        echo "Localisation [{$dept['location']}]: ";
        $location = trim(fgets(STDIN)) ?: $dept['location'];

        $data = [
            'department_name' => $name,
            'location' => $location
        ];

        // Valider les donnees
        if (!$this->validator->validateDepartment($data)) {
            $this->validator->displayErrors();
            return;
        }

        try {
            $this->model->update($id, $data);
            echo "\n Departement modifie avec succès!\n";
        } catch (Exception $e) {
            echo "\n[!] Erreur: " . $e->getMessage() . "\n";
        }
    }

    // Supprimer un departement
    public function delete(): void
    {
        echo "\n=== Supprimer un Departement ===\n";
        echo "ID du departement: ";
        $id = (int)trim(fgets(STDIN));

        $dept = $this->model->getByIdWithDoctorCount($id);
        if (!$dept) {
            echo "\n[!] Departement non trouve.\n";
            return;
        }

        echo "\n--- Departement à supprimer ---\n";
        $this->displayDepartment($dept);

        // Verifier si le departement contient des docteurs
        if ($this->model->hasDoctors($id)) {
            echo "\n  Attention: Ce departement contient {$dept['doctor_count']} docteur(s).\n";
            echo "Si vous supprimez ce departement, les docteurs seront dissocies (department_id = NULL).\n";
        }

        echo "\nÊtes-vous sûr de vouloir supprimer ce departement? (oui/non): ";
        $confirm = trim(fgets(STDIN));

        if (strtolower($confirm) === 'oui') {
            try {
                $this->model->delete($id);
                echo "\n Departement supprime avec succès!\n";
            } catch (Exception $e) {
                echo "\n[!] Erreur: " . $e->getMessage() . "\n";
            }
        } else {
            echo "\n[i] Suppression annulee.\n";
        }
    }

    // Afficher un departement
    private function displayDepartment(array $dept): void
    {
        echo "\n";
        echo "ID: {$dept['department_id']}\n";
        echo "Nom: {$dept['department_name']}\n";
        echo "Localisation: {$dept['location']}\n";
        if (isset($dept['doctor_count'])) {
            echo "Nombre de docteurs: {$dept['doctor_count']}\n";
        }
        echo str_repeat("-", 50) . "\n";
    }
}