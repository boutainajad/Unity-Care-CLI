<?php

require_once __DIR__ . "/config/conection.php";
require_once __DIR__ . "/Crud/PatientCrud.php";
require_once __DIR__ . "/Crud/DoctorCrud.php";
require_once __DIR__ . "/Crud/DepartmentCrud.php";

$patientCrud = new PatientCrud($conn);
$doctorCrud = new DoctorCrud($conn);
$departmentCrud = new DepartmentCrud($conn);

function displayMainMenu(): void
{
    echo "1. Gerer les patients\n";
    echo "2. Gerer les Doctors\n";
    echo "3. Gerer les departements\n";
    echo "4. Statistiques\n";
    echo "5. Quitter\n";
    echo "\nChoix: ";
}

function handlePatientMenu(PatientCrud $crud): void
{
    while (true) {
        echo "\n=== Gestion des Patients ===\n";
        echo "1. Lister tous les patients\n";
        echo "2. Rechercher un patient\n";
        echo "3. Ajouter un patient\n";
        echo "4. Modifier un patient\n";
        echo "5. Supprimer un patient\n";
        echo "6. Retour\n";
        echo "\nChoix: ";
        
        $choice = trim(fgets(STDIN));
        
        switch ($choice) {
            case '1': $crud->listAll(); break;
            case '2': $crud->search(); break;
            case '3': $crud->add(); break;
            case '4': $crud->update(); break;
            case '5': $crud->delete(); break;
            case '6': return;
            default: echo "[!] Choix invalide.\n";
        }
        
        echo "\nAppuyez sur Entree...";
        fgets(STDIN);
    }
}

function handleDoctorMenu(DoctorCrud $crud): void
{
    while (true) {
        echo "\n=== Gestion des Doctors ===\n";
        echo "1. Lister tous les doctors\n";
        echo "2. Rechercher un doctor\n";
        echo "3. Ajouter un doctor\n";
        echo "4. Modifier un doctor\n";
        echo "5. Supprimer un doctor\n";
        echo "6. Retour\n";
        echo "\nChoix: ";
        
        $choice = trim(fgets(STDIN));
        
        switch ($choice) {
            case '1': $crud->listAll(); break;
            case '2': $crud->search(); break;
            case '3': $crud->add(); break;
            case '4': $crud->update(); break;
            case '5': $crud->delete(); break;
            case '6': return;
            default: echo "[!] Choix invalide.\n";
        }
        
        echo "\nAppuyez sur Entree...";
        fgets(STDIN);
    }
}

function handleDepartmentMenu(DepartmentCrud $crud): void
{
    while (true) {
        echo "\n=== Gestion des Departements ===\n";
        echo "1. Lister tous les departements\n";
        echo "2. Rechercher un departement\n";
        echo "3. Ajouter un departement\n";
        echo "4. Modifier un departement\n";
        echo "5. Supprimer un departement\n";
        echo "6. Retour\n";
        echo "\nChoix: ";
        
        $choice = trim(fgets(STDIN));
        
        switch ($choice) {
            case '1': $crud->listAll(); break;
            case '2': $crud->search(); break;
            case '3': $crud->add(); break;
            case '4': $crud->update(); break;
            case '5': $crud->delete(); break;
            case '6': return;
            default: echo "[!] Choix invalide.\n";
        }
        
        echo "\nAppuyez sur Entree...";
        fgets(STDIN);
    }
}

function displayStatistics(PDO $conn): void
{
    require_once __DIR__ . "/Models/PatientModel.php";
    require_once __DIR__ . "/Models/DoctorModel.php";
    require_once __DIR__ . "/Models/DepartmentModel.php";
    
    $patientModel = new PatientModel($conn);
    $doctorModel = new DoctorModel($conn);
    $departmentModel = new DepartmentModel($conn);
    
 
    
    try {
        $patientStats = $patientModel->getStatistics();
        echo "\n--- Patients ---\n";
        echo "Total: {$patientStats['total']}\n";
        
        $doctorStats = $doctorModel->getStatistics();
        echo "\n--- Docteurs ---\n";
        echo "Total: {$doctorStats['total']}\n";
        
        $deptStats = $departmentModel->getStatistics();
        echo "\n--- Departements ---\n";
        echo "Total: {$deptStats['total']}\n";
    } catch (Exception $e) {
        echo "\n[!] Erreur: " . $e->getMessage() . "\n";
    }
}

echo "\n Bienvenue dans Unity Care CLI!\n";

while (true) {
    displayMainMenu();
    $choice = trim(fgets(STDIN));
    
    switch ($choice) {
        case '1': handlePatientMenu($patientCrud); break;
        case '2': handleDoctorMenu($doctorCrud); break;
        case '3': handleDepartmentMenu($departmentCrud); break;
        case '4': 
            displayStatistics($conn);
            echo "\nAppuyez sur Entree...";
            fgets(STDIN);
            break;
        case '5':
            echo "\n Au revoir!\n";
            exit(0);
        default:
            echo "[!] Choix invalide.\n";
            sleep(1);
    }
}