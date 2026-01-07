/**
 * Have I Been Pwned - Script de verification
 * Conventions : S_ (string), I_ (int), B_ (bool), O_ (object), A_ (array)
 */

document.addEventListener('DOMContentLoaded', function() {
    
    // Elements du DOM
    let O_emailInput = document.getElementById('email-input');
    let O_searchBtn = document.getElementById('search-btn');
    let O_resultArea = document.getElementById('result-area');
    
    // Email secret pour l'enigme
    const S_SECRET_EMAIL = 'melina5672@gmail.com';
    
    /**
     * Verifie si l'email entre correspond a l'email compromis
     * @returns {void}
     */
    function checkEmail() {
        let S_email = O_emailInput.value.trim().toLowerCase();
        
        if (!S_email) {
            O_resultArea.innerHTML = '';
            return;
        }
        
        // Affichage du loader
        O_resultArea.innerHTML = '<div class="searching"><div class="loader"></div><span>Recherche en cours...</span></div>';
        
        // Simulation delai de recherche
        setTimeout(function() {
            if (S_email === S_SECRET_EMAIL) {
                displayCompromisedResult(S_email);
            } else {
                displaySafeResult(S_email);
            }
        }, 1200);
    }
    
    /**
     * Affiche le resultat pour un email compromis
     * @param {string} S_email - L'email verifie
     * @returns {void}
     */
    function displayCompromisedResult(S_email) {
        let S_html = '';
        
        S_html += '<div class="result-card compromised">';
        S_html += '    <div class="result-header">';
        S_html += '        <span class="alert-badge">COMPROMIS</span>';
        S_html += '        <h2>Cette adresse a fuite !</h2>';
        S_html += '    </div>';
        S_html += '    <div class="result-body">';
        S_html += '        <p class="email-display">' + escapeHtml(S_email) + '</p>';
        S_html += '        <p class="breach-count">Trouve dans <strong>1 fuite</strong> de donnees</p>';
        S_html += '        <div class="breach-detail">';
        S_html += '            <div class="breach-name">';
        S_html += '                <span>SocialConnect Database Leak</span>';
        S_html += '            </div>';
        S_html += '            <div class="breach-info">';
        S_html += '                <div class="info-row">';
        S_html += '                    <span class="label">Date de la fuite :</span>';
        S_html += '                    <span class="value">Decembre 2024</span>';
        S_html += '                </div>';
        S_html += '                <div class="info-row">';
        S_html += '                    <span class="label">Donnees exposees :</span>';
        S_html += '                    <span class="value">Email, Mot de passe, Date de naissance</span>';
        S_html += '                </div>';
        S_html += '                <div class="info-row">';
        S_html += '                    <span class="label">Mot de passe associe :</span>';
        S_html += '                    <span class="value password-hint">M3l1n@2024!</span>';
        S_html += '                </div>';
        S_html += '            </div>';
        S_html += '        </div>';
        S_html += '        <div class="warning-box">';
        S_html += '            <strong>Attention :</strong> Si vous utilisez encore ce mot de passe, changez-le immediatement sur tous vos comptes.';
        S_html += '        </div>';
        S_html += '    </div>';
        S_html += '</div>';
        
        O_resultArea.innerHTML = S_html;
    }
    
    /**
     * Affiche le resultat pour un email non compromis
     * @param {string} S_email - L'email verifie
     * @returns {void}
     */
    function displaySafeResult(S_email) {
        let S_html = '';
        
        S_html += '<div class="result-card safe">';
        S_html += '    <div class="result-header">';
        S_html += '        <span class="safe-badge">AUCUNE FUITE</span>';
        S_html += '        <h2>Bonne nouvelle !</h2>';
        S_html += '    </div>';
        S_html += '    <div class="result-body">';
        S_html += '        <p class="email-display">' + escapeHtml(S_email) + '</p>';
        S_html += '        <p>Cette adresse email n\'a pas ete trouvee dans notre base de donnees de fuites connues.</p>';
        S_html += '        <p class="note">Cela ne garantit pas une securite totale, mais c\'est bon signe !</p>';
        S_html += '    </div>';
        S_html += '</div>';
        
        O_resultArea.innerHTML = S_html;
    }
    
    /**
     * Echappe les caracteres HTML pour eviter les injections XSS
     * @param {string} S_text - Le texte a echapper
     * @returns {string} Le texte echappe
     */
    function escapeHtml(S_text) {
        let O_div = document.createElement('div');
        O_div.textContent = S_text;
        return O_div.innerHTML;
    }
    
    // Ecouteurs d'evenements
    O_searchBtn.addEventListener('click', checkEmail, false);
    
    O_emailInput.addEventListener('keypress', function(event) {
        if (event.key === 'Enter') {
            checkEmail();
        }
    }, false);
    
});
