<?php

namespace Exceptions\bash;
use Exception;

/**
 * Exception levée lors d'un chemin de fichier invalide
 * 
 * Utilisée dans le système de fichiers virtuel du terminal bash.
 */
class InvalidPathException extends Exception
{
    /**
     * @param string $message Message d'erreur
     * @param int $code Code d'erreur
     * @param Exception|null $previous Exception précédente
     */
    public function __construct(string $message = '', int $code = 0, ?Exception $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}