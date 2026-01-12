<?php
namespace Models\InterfaceMail;

/**
 * Modèle de l'interface email simulée
 *
 * Fournit les données statiques des emails pour l'interface de messagerie.
 */
class InterfaceMailModel
{
    /**
     * Récupère les données des emails
     *
     * @return array Tableau des emails avec expéditeur, sujet, extrait et contenu
     */
    public function getemail(): array
    {
        return [
            [
                "sender" => "Le Hackeur",
                "subject" => "Fait attention",
                "time" => "9:00 AM",
                "snippet" => "Fais très attention à toi je connais tous tes mots de passes...",
                "content" => "<p>Fais très attention à toi je connais tous tes mots de passes.</p><p>C'est juste un conseil.</p>"
            ],
            [
                "sender" => "Le Hackeur",
                "subject" => "Important",
                "time" => "9:35 AM",
                "snippet" => "Réponds à mes messages sinon ça va mal se passer...",
                "content" => "<p>Réponds à mes messages sinon ça va mal se passer.</p><p>Je sais pas quoi dire mais c'est juste pour montrer que ça fonctionne.</p>"
            ],
            [
                "sender" => "Younes",
                "subject" => "Soit prudente",
                "time" => "8:51 AM",
                "snippet" => "J'ai vu qu'il y avait du vergla sur la route. Fais attention dans les virages. Ça peut être dangereux.",
                "content" => "<p>J'ai vu qu'il y avait du vergla sur la route. Fais attention dans les virages. Ça peut être dangereux.</p>"
            ],
            [
                "sender" => "Adam",
                "subject" => "<i class='fas fa-reply'></i> Re: Attention il fait froid ce matin",
                "time" => "Yesterday",
                "snippet" => "Oui j'ai vu qu'il faisait froid ce matin mais j'ai pris une veste tkt.",
                "content" => "<p>Oui j'ai vu qu'il faisait froid ce matin mais j'ai pris une veste tkt. Merci, c'est gentil en tout cas.</p>"
            ],
            [
                "sender" => "Matis",
                "subject" => "<i class='fas fa-reply'></i> Re: Comment ça va",
                "time" => "Yesterday",
                "snippet" => "Ça va merci et toi ? Tu as passé...",
                "content" => "<p>Ça va merci et toi ? Tu as passé une bonne journée ?</p>"
            ],
        ];
    }
}
