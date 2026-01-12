<?php

namespace Exceptions\bash;
use Exception;

/**
 * Exception levée lors d'un appel de commande invalide
 * 
 * Utilisée quand une commande bash non autorisée est appelée.
 */
class CommandCallingException extends Exception
{
    private $previous;

    /**
     * @param string $message Message d'erreur
     * @param int $code Code d'erreur
     * @param Exception|null $previous Exception précédente
     */
    public function __construct($message, $code = 0, Exception $previous = null)
    {
        parent::__construct($message, $code);

        if (!is_null($previous)) {
            $this->previous = $previous;
        }
    }

}