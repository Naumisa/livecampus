<?php

/**
 * Fonction de contrôleur pour la page d'accueil. Cette fonction prépare les données nécessaires
 * à afficher sur la page d'accueil du site. Actuellement, elle initialise un tableau de données vide,
 * mais elle peut être étendue pour inclure des données dynamiques selon les besoins de l'application.
 *
 * @return array Retourne un tableau associatif contenant les données à afficher sur la page.
 */
function index(): array
{
    $data = [];

    return [
        'data' => $data,
        'view' => "home/index",
    ];
}
