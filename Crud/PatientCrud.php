<?php

require_once __DIR__ . "/../Models/PatientModel.php";
require_once __DIR__ . "/../config/Validator.php";

class PatientCrud
{
    private PatientModel $model;
    private Validator $validator;

    public function __construct(PDO $conn)
    {
        $this->model = new PatientModel($conn);
        $this->validator = new Validator();
    }

    // Afficher tous les patients
    public function listAll(): void
    {
        $patients = $this->model->getAll();
        
        if (empty($patients)) {
            echo "\n[i] Aucun patient trouve.\n";
            return;
        }

        echo "\n=== Liste des Patients ===\n";
        echo str_repeat("-", 120) . "\n";
        printf("%-5s %-15s %-15s %-10s %-12s %-25s %-30s\n", 
            "ID", "Prenom", "Nom", "Genre", "Telephone", "Email", "Adresse");
        echo str_repeat("-", 120) . "\n";

        foreach ($patients as $patient) {
            printf("%-5s %-15s %-15s %-10s %-12s %-25s %-30s\n",
                $patient['patient_id'],
                substr($patient['first_name'], 0, 15),
                substr($patient['last_name'], 0, 15),
                $patient['genre'],
                $patient['phone_number'],
                substr($patient['email'], 0, 25),
                substr($patient['adress'], 0, 30)
            );
        }
        echo str_repeat("-", 120) . "\n";
        echo "Total: " . count($patients) . " patient(s)\n";
    }

    // Rechercher un patient
    public function search(): void
    {
        echo "\n=== Rechercher un Patient ===\n";
        echo "1. Par nom\n";
        echo "2. Par email\n";
        echo "3. Par telephone\n";
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
                echo "Entrez l'email: ";
                $email = trim(fgets(STDIN));
                if ($this->validator->email($email)) {
                    $patient = $this->model->findByEmail($email);
                    $results = $patient ? [$patient] : [];
                } else {
                    $this->validator->displayErrors();
                    return;
                }
                break;
            case '3':
                echo "Entrez le telephone: ";
                $phone = trim(fgets(STDIN));
                if ($this->validator->phone($phone)) {
                    $patient = $this->model->findByPhone($phone);
                    $results = $patient ? [$patient] : [];
                } else {
                    $this->validator->displayErrors();
                    return;
                }
                break;
            default:
                echo "[!] Choix invalide.\n";
                return;
        }

        if (empty($results)) {
            echo "\n[i] Aucun patient trouve.\n";
            return;
        }

        echo "\n=== Resultats de la recherche ===\n";
        foreach ($results as $patient) {
            $this->displayPatient($patient);
        }
    }

    // Ajouter un patient
    public function add(): void
    {
        echo "\n=== Ajouter un Patient ===\n";
        
        echo "Prenom: ";
        $firstName = trim(fgets(STDIN));
        
        echo "Nom: ";
        $lastName = trim(fgets(STDIN));
        
        echo "Genre (Male/Female): ";
        $genre = trim(fgets(STDIN));
        
        echo "Date de naissance (YYYY-MM-DD): ";
        $dob = trim(fgets(STDIN));
        
        echo "Telephone: ";
        $phone = trim(fgets(STDIN));
        
        echo "Email: ";
        $email = trim(fgets(STDIN));
        
        echo "Adresse: ";
        $address = trim(fgets(STDIN));

        // Generer un code patient unique
        $patientCode = 'PAT' . date('Ymd') . rand(1000, 9999);

        $data = [
            'first_name' => $firstName,
            'last_name' => $lastName,
            'genre' => $genre,
            'date_of_birth' => $dob,
            'phone_number' => $phone,
            'email' => $email,
            'adress' => $address,
            'patient_code' => $patientCode
        ];

        // Valider les donnees
        if (!$this->validator->validatePatient($data)) {
            $this->validator->displayErrors();
            return;
        }

        try {
            $id = $this->model->create($data);
            echo "\n Patient ajoute avec succès! ID: {$id}, Code: {$patientCode}\n";
        } catch (Exception $e) {
            echo "\n[!] Erreur: " . $e->getMessage() . "\n";
        }
    }

    // Modifier un patient
    public function update(): void
    {
        echo "\n=== Modifier un Patient ===\n";
        echo "ID du patient: ";
        $id = (int)trim(fgets(STDIN));

        $patient = $this->model->getById($id);
        if (!$patient) {
            echo "\n[!] Patient non trouve.\n";
            return;
        }

        echo "\n--- Informations actuelles ---\n";
        $this->displayPatient($patient);

        echo "\n--- Nouvelles informations (laissez vide pour garder l'ancienne valeur) ---\n";
        
        echo "Prenom [{$patient['first_name']}]: ";
        $firstName = trim(fgets(STDIN)) ?: $patient['first_name'];
        
        echo "Nom [{$patient['last_name']}]: ";
        $lastName = trim(fgets(STDIN)) ?: $patient['last_name'];
        
        echo "Genre [{$patient['genre']}]: ";
        $genre = trim(fgets(STDIN)) ?: $patient['genre'];
        
        echo "Date de naissance [{$patient['date_of_birth']}]: ";
        $dob = trim(fgets(STDIN)) ?: $patient['date_of_birth'];
        
        echo "Telephone [{$patient['phone_number']}]: ";
        $phone = trim(fgets(STDIN)) ?: $patient['phone_number'];
        
        echo "Email [{$patient['email']}]: ";
        $email = trim(fgets(STDIN)) ?: $patient['email'];
        
        echo "Adresse [{$patient['adress']}]: ";
        $address = trim(fgets(STDIN)) ?: $patient['adress'];

        $data = [
            'first_name' => $firstName,
            'last_name' => $lastName,
            'genre' => $genre,
            'date_of_birth' => $dob,
            'phone_number' => $phone,
            'email' => $email,
            'adress' => $address,
            'patient_code' => $patient['patient_code']
        ];

        // Valider les donnees
        if (!$this->validator->validatePatient($data)) {
            $this->validator->displayErrors();
            return;
        }

        try {
            $this->model->update($id, $data);
            echo "\n✅ Patient modifie avec succès!\n";
        } catch (Exception $e) {
            echo "\n[!] Erreur: " . $e->getMessage() . "\n";
        }
    }

    // Supprimer un patient
    public function delete(): void
    {
        echo "\n=== Supprimer un Patient ===\n";
        echo "ID du patient: ";
        $id = (int)trim(fgets(STDIN));

        $patient = $this->model->getById($id);
        if (!$patient) {
            echo "\n[!] Patient non trouve.\n";
            return;
        }

        echo "\n--- Patient à supprimer ---\n";
        $this->displayPatient($patient);

        echo "\nÊtes-vous sûr de vouloir supprimer ce patient? (oui/non): ";
        $confirm = trim(fgets(STDIN));

        if (strtolower($confirm) === 'oui') {
            try {
                $this->model->delete($id);
                echo "\n Patient supprime avec succès!\n";
            } catch (Exception $e) {
                echo "\n[!] Erreur: " . $e->getMessage() . "\n";
            }
        } else {
            echo "\n[i] Suppression annulee.\n";
        }
    }

    // Afficher un patient
    private function displayPatient(array $patient): void
    {
        echo "\n";
        echo "ID: {$patient['patient_id']}\n";
        echo "Nom: {$patient['first_name']} {$patient['last_name']}\n";
        echo "Genre: {$patient['genre']}\n";
        echo "Date de naissance: {$patient['date_of_birth']}\n";
        echo "Telephone: {$patient['phone_number']}\n";
        echo "Email: {$patient['email']}\n";
        echo "Adresse: {$patient['adress']}\n";
        if (isset($patient['patient_code'])) {
            echo "Code Patient: {$patient['patient_code']}\n";
        }
        echo str_repeat("-", 50) . "\n";
    }
}