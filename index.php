<?php

require_once "config/conection.php";

echo "
=== Unity Care CLI ===
1. Gérer les patients
2. Gérer les Doctors
3. Gérer les départements
4. Statistiques
5. Quitter
";


while (true) {
    $choice = trim(string: fgets(STDIN));
    
    switch ($choice) {
        case 1:
            include "Menus/PatientMenu.php";
            break;
        case 2:
            include "Menus/DoctorMenu.php";
            break;
        case 3:
            include "Menus/DepartmentMenu.php";
            break;
        case 4:
            include "Menus/Statistiques.php";
            break;
        case 5:
            echo "Exiting program...\n";
            exit;
            default:
            echo "[!] Invalid choice. Please enter 1-5.\n";
            sleep(1);
    }
}