Unity Care Clinic – CLI (PHP OOP)
---
Description
---

Unity Care Clinic CLI est une application console développée en PHP 8 selon une architecture orientée objet.
Elle permet la gestion interne des patients, des médecins et des départements sans utiliser l’interface web.

Objectifs 
---

Appliquer les principes de la programmation orientée objet

Structurer un projet PHP clair et maintenable

Implémenter des opérations CRUD via une interface console

Générer des statistiques métier

Fonctionnalités
---

Gestion des Patients (CRUD)

Gestion des Médecins (CRUD)

Gestion des Départements (CRUD)

Validation des données utilisateur

Statistiques :

Âge moyen des patients

Ancienneté moyenne des médecins

Département le plus peuplé

Répartition des patients par département

Affichage des données en tableaux ASCII

Architecture du projet
---

Le projet est organisé en plusieurs couches :

Core : connexion base de données, BaseModel, Validator

Entities : classes métier (Patient, Doctor, Department, etc.)

Interfaces : interface Displayable

Console : menus et navigation CLI

Utils : affichage ASCII

Cette organisation respecte la séparation des responsabilités.

Technologies utilisées
---

PHP 8 (OOP)

MySQL (MySQLi orienté objet)

Design Pattern Singleton

UML & ERD 