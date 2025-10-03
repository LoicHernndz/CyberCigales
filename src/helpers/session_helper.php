<?php

// Je vérifie si une session n'est pas déjà démarrée pour éviter les erreurs
if(!isset($_SESSION)){
    session_start(); // Je démarre la session pour pouvoir stocker des messages temporaires
}

// Ma fonction pour afficher des messages flash (messages temporaires qui s'affichent une seule fois)
// $name = le nom du message (ex: "register", "login")
// $message = le texte à afficher
// $class = la classe CSS pour le style (rouge pour erreur par défaut)
function flash($name = '', $message = '', $class = 'form-message form-message-red'){
    if(!empty($name)){
        if(!empty($message) && empty($_SESSION[$name])){
            $_SESSION[$name] = $message; // Je stocke le message
            $_SESSION[$name.'_class'] = $class; // Je stocke la classe CSS
        } else if(empty($message) && !empty($_SESSION[$name])){
            // Si je veux AFFICHER le message stocké (pas de nouveau message fourni)
            $class = !empty($_SESSION[$name.'_class']) ? $_SESSION[$name.'_class'] : $class;
            $message = '<div class="'.$class.'">'.$_SESSION[$name].'</div>'; // J'affiche le message
            unset($_SESSION[$name]); // Je supprime le message après l'avoir affiché
            unset($_SESSION[$name.'_class']); // Je supprime la classe aussi
            return $message;
        }
    }
    return '';
}

// Ma fonction pour rediriger l'utilisateur vers une autre page
function redirect($location){
    header("location: ".$location); // Je dis au navigateur d'aller à cette adresse
    exit();
}