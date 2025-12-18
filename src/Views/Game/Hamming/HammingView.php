<?php
namespace Views\Game\Hamming;

use Views\AbstractView;

/**
 * Vue pour le mini-jeu du carré de Hamming
 * 
 * Affiche un carré 3x3 interactif où l'utilisateur doit identifier
 * le bit erroné. Page autonome sans header/footer pour intégration.
 */
class HammingView extends AbstractView
{
    /**
     * Données du carré et du résultat
     * 
     * @var array Contient :
     *            - 'square' : carré 3x3 à afficher
     *            - 'success' : résultat (1 = correct, 0 = incorrect, null = pas encore de réponse)
     *            - 'message' : message de feedback
     *            - 'streak' : nombre de victoires consécutives
     *            - 'level' : niveau actuel
     *            - 'target' : objectif de victoires pour passer au niveau suivant
     */
    private array $data;
    
    /**
     * Constructeur
     * 
     * @param array $data Données du carré et du résultat
     */
    public function __construct(array $data = [])
    {
        $this->data = $data;
    }
    
    /**
     * Retourne le chemin du template HTML
     * 
     * @return string Chemin vers le fichier hamming.html
     */
    function templatePath(): string
    {
        return __DIR__ . '/hamming.html';
    }
    
    /**
     * Retourne les clés de template à remplacer dans le HTML
     * 
     * Convertit le carré 3x3 en HTML et prépare le message de résultat
     * 
     * @return array Tableau associatif des clés de remplacement
     */
    function templateKeys(): array
    {
        $square = $this->data['square'] ?? [[0,0,0],[0,0,0],[0,0,0]];
        $success = $this->data['success'] ?? null;
        $message = $this->data['message'] ?? '';
        
        // Convertir le carré en HTML
        $squareHtml = '';
        for ($row = 0; $row < 3; $row++) {
            for ($col = 0; $col < 3; $col++) {
                $bit = (int)$square[$row][$col];
                $squareHtml .= '<div class="bit-cell" data-row="' . $row . '" data-col="' . $col . '">' . $bit . '</div>';
            }
        }
        
        // Préparer le message de résultat en HTML complet
        $resultHtml = '';
        if ($success === 1) {
            $resultHtml = '<div class="result-message success">' . htmlspecialchars($message) . '</div>';
        } elseif ($success === 0) {
            $resultHtml = '<div class="result-message error">' . htmlspecialchars($message) . '</div>';
        }
        
        $streak = isset($this->data['streak']) ? (int)$this->data['streak'] : 0;
        $target = isset($this->data['target']) ? (int)$this->data['target'] : 5;
        
        return [
            'SQUARE_HTML' => $squareHtml,
            'RESULT_MESSAGE_HTML' => $resultHtml,
            'SUCCESS_VALUE' => $success !== null ? ($success ? '1' : '0') : '',
            'STREAK' => (string)$streak,
            'TARGET' => (string)$target
        ];
    }
    
    /**
     * Affiche le header HTML de la page (sans header du site)
     * 
     * @return void
     */
    function renderHeader(): void
    {
        echo '
<!DOCTYPE html>
<html lang="fr">
    <head>
        <meta charset="UTF-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Carré de Hamming - CyberCigales</title>
        <link rel="icon" href="/images/favicon.svg" type="image/svg+xml">
        <style>
            * {
                margin: 0;
                padding: 0;
                box-sizing: border-box;
            }
            body {
                font-family: Arial, sans-serif;
                background: #fff;
                padding: 20px;
                display: flex;
                justify-content: center;
                align-items: center;
                min-height: 100vh;
            }
            .hamming-container {
                display: flex;
                justify-content: center;
                align-items: center;
                width: 100%;
            }
            .square-container {
                display: grid;
                grid-template-columns: repeat(3, 70px);
                grid-template-rows: repeat(3, 70px);
                gap: 4px;
                width: fit-content;
            }
            .bit-cell {
                width: 70px;
                height: 70px;
                display: flex;
                align-items: center;
                justify-content: center;
                font-size: 20px;
                background: #fafafa;
                border: 1px solid #ccc;
                cursor: pointer;
                user-select: none;
            }
            .bit-cell:hover {
                background: #f0f0f0;
            }
            .bit-cell:active {
                background: #e8e8e8;
            }
        </style>
    </head>
    <body>';
    }
    
    /**
     * Affiche le footer HTML avec le script JavaScript pour gérer les clics
     * 
     * @return void
     */
    function renderFooter(): void
    {
        $successValue = $this->data['success'] ?? null;
        $successParam = ($successValue !== null) ? ($successValue ? '1' : '0') : '';
        
        echo '
        <script>
            /**
             * EXPLICATION DU JAVASCRIPT :
             * 
             * 1. handleBitClick() : Fonction appelée quand on clique sur un bit
             *    - Récupère la position (row, col) du bit cliqué
             *    - Désactive les clics pour éviter les doubles clics
             *    - Envoie la réponse au serveur via AJAX (fetch)
             *    - Met à jour le carré si la réponse est correcte
             * 
             * 2. fetch() : Envoie une requête POST au serveur sans recharger la page
             *    - Envoie row et col dans le body
             *    - Le serveur répond avec du JSON (succès, nouveau carré, etc.)
             * 
             * 3. .then() : Traite la réponse du serveur
             *    - Si correct : génère un nouveau carré HTML et le remplace
             *    - Si incorrect : réactive les clics pour réessayer
             * 
             * 4. DOMContentLoaded : Attend que la page soit chargée
             *    - Attache la fonction handleBitClick à chaque cellule du carré
             */
            
            function handleBitClick() {
                // 1. Récupérer la position du bit cliqué
                const row = this.getAttribute("data-row");
                const col = this.getAttribute("data-col");
                const cells = document.querySelectorAll(".bit-cell");
                
                // 2. Désactiver les clics pour éviter les doubles clics
                cells.forEach(c => c.style.pointerEvents = "none");
                
                // 3. Préparer les données à envoyer
                const formData = new FormData();
                formData.append("row", row);
                formData.append("col", col);
                
                // 4. Envoyer la requête AJAX au serveur
                fetch(window.location.pathname, {
                    method: "POST",
                    body: formData,
                    headers: {
                        "X-Requested-With": "XMLHttpRequest"  // Indique que c\'est une requête AJAX
                    }
                })
                .then(response => response.json())  // Convertir la réponse en JSON
                .then(data => {
                    // 5. Si la réponse est correcte, mettre à jour le carré
                    if (data.newSquare && data.square) {
                        const squareContainer = document.getElementById("square-container");
                        let newSquareHtml = "";
                        
                        // 6. Générer le HTML du nouveau carré
                        for (let r = 0; r < 3; r++) {
                            for (let c = 0; c < 3; c++) {
                                const bit = data.square[r][c];
                                newSquareHtml += \'<div class="bit-cell" data-row="\' + r + \'" data-col="\' + c + \'">\' + bit + \'</div>\';
                            }
                        }
                        
                        // 7. Remplacer l\'ancien carré par le nouveau
                        squareContainer.innerHTML = newSquareHtml;
                        
                        // 8. Réactiver les clics sur les nouvelles cellules
                        const newCells = document.querySelectorAll(".bit-cell");
                        newCells.forEach(cell => {
                            cell.style.pointerEvents = "auto";
                            cell.addEventListener("click", handleBitClick);
                        });
                    } else {
                        // 9. Si incorrect, réactiver les clics pour réessayer
                        cells.forEach(c => c.style.pointerEvents = "auto");
                    }
                })
                .catch(error => {
                    // 10. En cas d\'erreur AJAX, fallback : recharger la page avec POST
                    console.error("Erreur:", error);
                    const form = document.createElement("form");
                    form.method = "POST";
                    form.action = window.location.pathname;
                    const rowInput = document.createElement("input");
                    rowInput.type = "hidden";
                    rowInput.name = "row";
                    rowInput.value = row;
                    const colInput = document.createElement("input");
                    colInput.type = "hidden";
                    colInput.name = "col";
                    colInput.value = col;
                    form.appendChild(rowInput);
                    form.appendChild(colInput);
                    document.body.appendChild(form);
                    form.submit();
                });
            }
            
            // 11. Au chargement de la page, attacher les event listeners
            document.addEventListener("DOMContentLoaded", function() {
                const cells = document.querySelectorAll(".bit-cell");
                cells.forEach(cell => {
                    cell.addEventListener("click", handleBitClick);
                });
            });
        </script>
    </body>
</html>';
    }
}

