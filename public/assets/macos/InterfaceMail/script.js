/**
 * Script de gestion de l'interface de messagerie (Client-side).
 *
 * Ce script s'exécute au chargement du DOM. Il gère :
 * 1. L'écoute des clics sur la liste des e-mails (générée par PHP).
 * 2. La mise à jour dynamique du panneau de lecture (Reading Pane) sans rechargement de page.
 * 3. La gestion visuelle des états "lu" et "sélectionné".
 *
 * @file      interface-mail.js
 * @author    [Ton Nom]
 */
document.addEventListener('DOMContentLoaded', () => {

    /**
     * @type {HTMLElement} Le conteneur du panneau de lecture où le contenu de l'e-mail s'affiche.
     */
    const readingPane = document.getElementById('reading-pane');

    /**
     * Met à jour le HTML du panneau de lecture avec les données de l'e-mail fourni.
     *
     * @param {Object} emailData - L'objet e-mail désérialisé depuis le JSON.
     * @param {string} emailData.subject - Le sujet de l'e-mail.
     * @param {string} emailData.sender - Le nom ou l'adresse de l'expéditeur.
     * @param {string} emailData.content - Le corps du message (peut contenir du HTML).
     * @returns {void}
     */
    const updateReadingPane = (emailData) => {
        // Le contenu HTML est mis à jour avec les données de l'e-mail cliqué
        readingPane.innerHTML = `
            <h2>${emailData.subject}</h2>
            <div class="recipient-info">
                <strong>De :</strong> ${emailData.sender} <br>
                <strong>À :</strong> noname@noname.com
            </div>
            <div class="email-content">
                ${emailData.content}
            </div>
        `;
    };

    // --- Gère le clic sur un e-mail qui est DÉJÀ dans le DOM (rempli par PHP) ---

    /**
     * @type {NodeListOf<Element>} Liste de tous les éléments <li> représentant les e-mails.
     */
    const emailItems = document.querySelectorAll('.email-item');

    emailItems.forEach(item => {
        /**
         * Écouteur d'événement pour le clic sur un e-mail.
         * Gère le changement de classe CSS et le parsing des données JSON.
         */
        item.addEventListener('click', () => {
            // Désélectionner tous les autres e-mails
            emailItems.forEach(i => i.classList.remove('selected'));

            // Sélectionner l'e-mail cliqué et le marquer comme lu
            item.classList.add('selected');
            item.classList.add('read');

            // Récupérer les données JSON sérialisées par PHP dans l'attribut data-email
            const emailDataJson = item.getAttribute('data-email');

            try {
                // Décodage des données
                const emailData = JSON.parse(emailDataJson);

                // Mettre à jour le panneau de lecture
                updateReadingPane(emailData);
            } catch (e) {
                console.error("Erreur de parsing JSON de l'attribut data-email:", e);
                // Si l'e-mail n'a pas pu être parsé, on affiche le placeholder avec un message d'erreur
                readingPane.innerHTML = '<div class="placeholder">Erreur de chargement des données.</div>';
            }
        });
    });
});