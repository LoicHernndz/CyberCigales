/**
 * Have I Been Pwned - Script de verification
 * Conventions : S_ (string), I_ (int), B_ (bool), O_ (object), A_ (array)
 */

document.addEventListener('DOMContentLoaded', function() {

    // Elements du DOM
    let O_emailInput = document.getElementById('email-input');
    let O_searchBtn = document.getElementById('search-btn');
    let O_resultArea = document.getElementById('result-area');

    // Emails secrets pour l'enigme
    const S_SECRET_EMAIL = 'lucie.bertrand@cybercigales.fr';
    const S_HACKER_EMAIL = 'k0de_breaker@darkweb.net';
    const S_LEAKED_PASSWORD = 'Luc1e@2024!';

    /**
     * Verifie si l'email entre correspond a un email compromis
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
            } else if (S_email === S_HACKER_EMAIL) {
                displayHackerResult(S_email);
            } else {
                displaySafeResult(S_email);
            }
        }, 1200);
    }

    /**
     * Affiche le resultat pour l'email de Lucie (compromis + challenge mdp)
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
        S_html += '                    <span class="value password-hint">' + escapeHtml(S_LEAKED_PASSWORD) + '</span>';
        S_html += '                </div>';
        S_html += '            </div>';
        S_html += '        </div>';
        S_html += '        <div class="warning-box">';
        S_html += '            <strong>Attention :</strong> Si vous utilisez encore ce mot de passe, changez-le immediatement sur tous vos comptes.';
        S_html += '        </div>';

        // Section quiz de securite
        S_html += '        <div class="security-quiz" id="security-quiz">';
        S_html += '            <h3 class="quiz-title">Quiz de securite</h3>';
        S_html += '            <p class="quiz-desc">Avant de securiser votre compte, prouvez vos connaissances en cybersecurite. Repondez correctement a toutes les questions pour debloquer le changement de mot de passe :</p>';
        // Q1
        S_html += '            <div class="quiz-question" id="quiz-q1">';
        S_html += '                <p class="question-text">1. Pourquoi le mot de passe "Luc1e@2024!" est-il considere comme faible malgre ses caracteres speciaux ?</p>';
        S_html += '                <label class="quiz-option"><input type="radio" name="q1" value="a"> Il est trop court (moins de 8 caracteres)</label>';
        S_html += '                <label class="quiz-option"><input type="radio" name="q1" value="b"> Il contient des informations personnelles facilement devinables</label>';
        S_html += '                <label class="quiz-option"><input type="radio" name="q1" value="c"> Il utilise des chiffres a la place de lettres, ce qui est interdit</label>';
        S_html += '                <div class="quiz-feedback" id="feedback-q1"></div>';
        S_html += '            </div>';
        // Q2
        S_html += '            <div class="quiz-question" id="quiz-q2">';
        S_html += '                <p class="question-text">2. Qu\'est-ce que le phishing (hameconnage) ?</p>';
        S_html += '                <label class="quiz-option"><input type="radio" name="q2" value="a"> Un virus qui se propage automatiquement entre les ordinateurs</label>';
        S_html += '                <label class="quiz-option"><input type="radio" name="q2" value="b"> Une technique utilisant de faux messages (emails, SMS, sites web) pour tromper les victimes et voler leurs informations</label>';
        S_html += '                <label class="quiz-option"><input type="radio" name="q2" value="c"> Un logiciel qui bloque l\'acces a votre ordinateur contre une rancon</label>';
        S_html += '                <div class="quiz-feedback" id="feedback-q2"></div>';
        S_html += '            </div>';
        // Q3
        S_html += '            <div class="quiz-question" id="quiz-q3">';
        S_html += '                <p class="question-text">3. Apres une fuite de donnees, que faire en priorite ?</p>';
        S_html += '                <label class="quiz-option"><input type="radio" name="q3" value="a"> Ne rien faire, les entreprises gerent ca automatiquement</label>';
        S_html += '                <label class="quiz-option"><input type="radio" name="q3" value="b"> Supprimer son compte email pour ne plus etre cible</label>';
        S_html += '                <label class="quiz-option"><input type="radio" name="q3" value="c"> Changer immediatement son mot de passe sur tous les comptes qui utilisaient le meme</label>';
        S_html += '                <div class="quiz-feedback" id="feedback-q3"></div>';
        S_html += '            </div>';
        // Q4
        S_html += '            <div class="quiz-question" id="quiz-q4">';
        S_html += '                <p class="question-text">4. Quel est le meilleur moyen de gerer ses mots de passe au quotidien ?</p>';
        S_html += '                <label class="quiz-option"><input type="radio" name="q4" value="a"> Utiliser le meme mot de passe partout pour ne pas l\'oublier</label>';
        S_html += '                <label class="quiz-option"><input type="radio" name="q4" value="b"> Les ecrire sur un post-it colle sur l\'ecran</label>';
        S_html += '                <label class="quiz-option"><input type="radio" name="q4" value="c"> Utiliser un gestionnaire de mots de passe (Bitwarden, KeePass, etc.)</label>';
        S_html += '                <div class="quiz-feedback" id="feedback-q4"></div>';
        S_html += '            </div>';
        // Q5
        S_html += '            <div class="quiz-question" id="quiz-q5">';
        S_html += '                <p class="question-text">5. Comment reconnaitre un email de phishing ?</p>';
        S_html += '                <label class="quiz-option"><input type="radio" name="q5" value="a"> Il vient toujours d\'un pays etranger et est ecrit dans une autre langue</label>';
        S_html += '                <label class="quiz-option"><input type="radio" name="q5" value="b"> Il cree un sentiment d\'urgence, contient des liens suspects et l\'adresse de l\'expediteur ne correspond pas au service officiel</label>';
        S_html += '                <label class="quiz-option"><input type="radio" name="q5" value="c"> Il contient toujours des pieces jointes infectees par des virus</label>';
        S_html += '                <div class="quiz-feedback" id="feedback-q5"></div>';
        S_html += '            </div>';
        S_html += '            <button type="button" id="quiz-validate-btn" class="btn-quiz-validate">Valider le quiz</button>';
        S_html += '            <div id="quiz-success-msg" class="quiz-success-msg" style="display:none;">';
        S_html += '                <span class="success-icon">&#10003;</span> Bravo ! Vous maitrisez les bases de la cybersecurite. Retournez envoyer un message a Leo pour lui dire que vous avez termine !';
        S_html += '            </div>';
        S_html += '        </div>';

        // Section challenge changement de mot de passe (masquee avant quiz)
        S_html += '        <div class="password-change-section" id="password-section" style="display:none;">';
        S_html += '            <h3 class="password-change-title">Securisez votre compte</h3>';
        S_html += '            <p class="password-change-desc">Creez un nouveau mot de passe qui respecte les criteres de securite :</p>';
        S_html += '            <div class="password-input-wrapper">';
        S_html += '                <input type="password" id="new-password-input" placeholder="Nouveau mot de passe..." autocomplete="off">';
        S_html += '                <button type="button" id="toggle-password-btn" class="toggle-password-btn" title="Afficher/masquer">';
        S_html += '                    <span id="eye-icon">&#128065;</span>';
        S_html += '                </button>';
        S_html += '            </div>';
        S_html += '            <ul class="criteria-list" id="criteria-list">';
        S_html += '                <li id="crit-length"><span class="crit-icon">&#10007;</span> Au moins 12 caracteres</li>';
        S_html += '                <li id="crit-upper"><span class="crit-icon">&#10007;</span> Au moins une majuscule</li>';
        S_html += '                <li id="crit-lower"><span class="crit-icon">&#10007;</span> Au moins une minuscule</li>';
        S_html += '                <li id="crit-number"><span class="crit-icon">&#10007;</span> Au moins un chiffre</li>';
        S_html += '                <li id="crit-special"><span class="crit-icon">&#10007;</span> Au moins un caractere special (!@#$%...)</li>';
        S_html += '                <li id="crit-similar"><span class="crit-icon">&#10007;</span> Different du mot de passe fuite</li>';
        S_html += '            </ul>';
        S_html += '            <button type="button" id="btn-change-password" class="btn-change-password" disabled>Changer le mot de passe</button>';
        S_html += '            <div id="password-success-msg" class="password-success-msg" style="display:none;">';
        S_html += '                <span class="success-icon">&#10003;</span> Mot de passe change avec succes ! Vos comptes sont securises.';
        S_html += '            </div>';
        S_html += '        </div>';

        S_html += '    </div>';
        S_html += '</div>';

        O_resultArea.innerHTML = S_html;
        setupQuiz();
        setupPasswordChallenge();
    }

    /**
     * Affiche le resultat pour l'email du hacker (compromis + mdp permute)
     * @param {string} S_email - L'email verifie
     * @returns {void}
     */
    function displayHackerResult(S_email) {
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
        S_html += '                <span>DarkForum Underground Leak</span>';
        S_html += '            </div>';
        S_html += '            <div class="breach-info">';
        S_html += '                <div class="info-row">';
        S_html += '                    <span class="label">Date de la fuite :</span>';
        S_html += '                    <span class="value">Janvier 2025</span>';
        S_html += '                </div>';
        S_html += '                <div class="info-row">';
        S_html += '                    <span class="label">Donnees exposees :</span>';
        S_html += '                    <span class="value">Email, Mot de passe, Adresse IP</span>';
        S_html += '                </div>';
        S_html += '                <div class="info-row">';
        S_html += '                    <span class="label">Mot de passe associe :</span>';
        S_html += '                    <span class="value password-hint">rlcseavleuin</span>';
        S_html += '                </div>';
        S_html += '            </div>';
        S_html += '        </div>';
        S_html += '        <div class="warning-box">';
        S_html += '            <strong>Information :</strong> Ce mot de passe semble chiffre ou encode. Il a peut-etre ete protege par un algorithme de chiffrement.';
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
     * Configure le quiz de securite
     * @returns {void}
     */
    function setupQuiz() {
        let O_validateBtn = document.getElementById('quiz-validate-btn');
        let O_quiz = document.getElementById('security-quiz');
        let O_passwordSection = document.getElementById('password-section');

        if (!O_validateBtn) return;

        const A_CORRECT = { q1: 'b', q2: 'b', q3: 'c', q4: 'c', q5: 'b' };
        const A_EXPLANATIONS = {
            q1: '<strong>Le mot de passe "Luc1e@2024!" contient le prenom "Lucie" (avec le classique remplacement i→1) et l\'annee "2024".</strong> Ces informations sont souvent trouvables en quelques secondes sur les reseaux sociaux.<br><br>Les pirates utilisent des <em>attaques par dictionnaire</em> qui testent automatiquement des millions de combinaisons : prenoms + dates, noms + annees, mots courants avec des substitutions (a→@, e→3, i→1...). Un mot de passe comme "Luc1e@2024!" serait craque en quelques minutes malgre ses caracteres speciaux, car il suit un schema previsible.<br><br><strong>Le saviez-vous ?</strong> 81% des violations de donnees impliquent des mots de passe faibles ou reutilises. Un bon mot de passe devrait etre une <em>phrase de passe</em> aleatoire (ex: "ChaiseBleueVolerRapide42!") plutot qu\'un mot modifie.',
            q2: '<strong>Le phishing (hameconnage en francais) est une technique de manipulation psychologique.</strong> Le pirate se fait passer pour une entite de confiance (banque, service en ligne, collegue) via un faux email, SMS ou site web pour vous inciter a reveler vos identifiants, coordonnees bancaires ou informations personnelles.<br><br>Il existe plusieurs variantes : le <em>spear phishing</em> (cible une personne precise), le <em>smishing</em> (par SMS), le <em>vishing</em> (par appel telephonique), et le <em>whaling</em> (vise les dirigeants d\'entreprise).<br><br><strong>Le saviez-vous ?</strong> 91% des cyberattaques commencent par un email de phishing. En 2023, les attaques de phishing ont cause plus de 10 milliards de dollars de pertes dans le monde. Un email de phishing bien concu peut tromper meme des experts en securite !',
            q3: '<strong>Quand un mot de passe fuite, les pirates pratiquent le "credential stuffing" :</strong> ils testent automatiquement vos identifiants sur des centaines d\'autres sites (Gmail, Facebook, Amazon, votre banque...) en quelques secondes grace a des robots.<br><br>C\'est pourquoi il faut agir immediatement : changez votre mot de passe sur le service compromis ET sur tous les comptes ou vous utilisiez le meme mot de passe. Activez aussi l\'authentification a deux facteurs (2FA) partout ou c\'est possible.<br><br><strong>Le saviez-vous ?</strong> 65% des gens reutilisent le meme mot de passe sur plusieurs sites. Lors de la fuite de LinkedIn en 2012, des millions de comptes sur d\'autres plateformes ont ete compromis dans les heures qui ont suivi, simplement parce que les utilisateurs avaient le meme mot de passe partout.',
            q4: '<strong>Un gestionnaire de mots de passe est l\'outil indispensable pour votre securite en ligne.</strong> Il genere des mots de passe uniques et ultra-complexes pour chaque site, les stocke de facon chiffree, et les remplit automatiquement quand vous en avez besoin. Vous n\'avez qu\'un seul mot de passe maitre a retenir.<br><br>Des solutions gratuites et open-source comme <em>Bitwarden</em> ou <em>KeePass</em> sont recommandees par les experts en cybersecurite. Les navigateurs (Chrome, Firefox) proposent aussi des gestionnaires integres, mais ils sont moins securises qu\'un outil dedie.<br><br><strong>Le saviez-vous ?</strong> Sans gestionnaire, une personne moyenne doit retenir environ 100 mots de passe differents. C\'est humainement impossible, ce qui explique pourquoi tant de gens reutilisent les memes. Un post-it sur l\'ecran est la pire solution : en entreprise, c\'est la premiere chose qu\'un intrus cherche !',
            q5: '<strong>Les emails de phishing jouent sur l\'urgence et la peur</strong> ("Votre compte sera bloque dans 24h", "Activite suspecte detectee") pour vous empecher de reflechir. Voici les signes revelateurs :<br><br>&#8226; <em>L\'adresse de l\'expediteur</em> ne correspond pas au domaine officiel (ex: "securite@g00gle-support.com" au lieu de "@google.com")<br>&#8226; <em>Les liens</em> menent vers des URLs suspectes (survolez-les sans cliquer pour verifier !)<br>&#8226; <em>Le ton est alarmiste</em> avec une demande d\'action immediate<br>&#8226; <em>Des fautes subtiles</em> dans le texte ou le design<br><br><strong>Le saviez-vous ?</strong> Les pirates modernes utilisent l\'intelligence artificielle pour creer des emails de phishing quasi parfaits, sans fautes et avec un design identique aux vrais. Le reflexe le plus sur : ne jamais cliquer sur un lien dans un email, mais aller directement sur le site officiel en tapant l\'adresse dans votre navigateur.'
        };

        O_validateBtn.addEventListener('click', function() {
            let I_correct = 0;
            let I_total = 5;

            for (let i = 1; i <= I_total; i++) {
                let S_name = 'q' + i;
                let O_selected = document.querySelector('input[name="' + S_name + '"]:checked');
                let O_feedback = document.getElementById('feedback-' + S_name);
                let O_question = document.getElementById('quiz-' + S_name);

                if (!O_selected) {
                    O_feedback.innerHTML = 'Selectionnez une reponse.';
                    O_feedback.className = 'quiz-feedback error';
                    continue;
                }

                if (O_selected.value === A_CORRECT[S_name]) {
                    I_correct++;
                    O_feedback.innerHTML = A_EXPLANATIONS[S_name];
                    O_feedback.className = 'quiz-feedback correct';
                    O_question.classList.add('answered-correct');
                    O_question.classList.remove('answered-wrong');
                } else {
                    O_feedback.innerHTML = A_EXPLANATIONS[S_name];
                    O_feedback.className = 'quiz-feedback error';
                    O_question.classList.add('answered-wrong');
                    O_question.classList.remove('answered-correct');
                }
            }

            if (I_correct === I_total) {
                document.getElementById('quiz-success-msg').style.display = 'flex';
                O_validateBtn.style.display = 'none';
                O_passwordSection.style.display = 'block';
                localStorage.setItem('hibp_quiz_completed', 'true');

                // Desactiver les radios
                let A_radios = O_quiz.querySelectorAll('input[type="radio"]');
                for (let j = 0; j < A_radios.length; j++) {
                    A_radios[j].disabled = true;
                }
            }
        });

        // Si le quiz a deja ete complete, sauter directement au changement de mdp
        if (localStorage.getItem('hibp_quiz_completed') === 'true') {
            O_quiz.style.display = 'none';
            O_passwordSection.style.display = 'block';
        }

        // Si le mdp a deja ete change, afficher le message de succes
        if (localStorage.getItem('hibp_password_changed') === 'true') {
            O_quiz.style.display = 'none';
            O_passwordSection.style.display = 'block';
            let O_pwdInput = document.getElementById('new-password-input');
            let O_changeBtn = document.getElementById('btn-change-password');
            let O_successMsg = document.getElementById('password-success-msg');
            if (O_pwdInput) O_pwdInput.disabled = true;
            if (O_changeBtn) O_changeBtn.style.display = 'none';
            if (O_successMsg) O_successMsg.style.display = 'flex';
        }
    }

    /**
     * Configure le challenge de changement de mot de passe
     * @returns {void}
     */
    function setupPasswordChallenge() {
        let O_passwordInput = document.getElementById('new-password-input');
        let O_changeBtn = document.getElementById('btn-change-password');
        let O_toggleBtn = document.getElementById('toggle-password-btn');
        let O_successMsg = document.getElementById('password-success-msg');

        if (!O_passwordInput) return;

        // Toggle visibilite du mot de passe
        O_toggleBtn.addEventListener('click', function() {
            let S_type = O_passwordInput.type === 'password' ? 'text' : 'password';
            O_passwordInput.type = S_type;
            document.getElementById('eye-icon').innerHTML = S_type === 'password' ? '&#128065;' : '&#128274;';
        });

        // Validation en temps reel
        O_passwordInput.addEventListener('input', function() {
            let S_pwd = O_passwordInput.value;

            let B_minLength = S_pwd.length >= 12;
            let B_hasUpper = /[A-Z]/.test(S_pwd);
            let B_hasLower = /[a-z]/.test(S_pwd);
            let B_hasNumber = /[0-9]/.test(S_pwd);
            let B_hasSpecial = /[!@#$%^&*()_+\-=\[\]{};':"\\|,.<>\/?]/.test(S_pwd);
            let B_notSimilar = checkNotSimilar(S_pwd);

            updateCriteria('crit-length', B_minLength);
            updateCriteria('crit-upper', B_hasUpper);
            updateCriteria('crit-lower', B_hasLower);
            updateCriteria('crit-number', B_hasNumber);
            updateCriteria('crit-special', B_hasSpecial);
            updateCriteria('crit-similar', B_notSimilar);

            let B_allValid = B_minLength && B_hasUpper && B_hasLower && B_hasNumber && B_hasSpecial && B_notSimilar;
            O_changeBtn.disabled = !B_allValid;
        });

        // Soumission du nouveau mot de passe
        O_changeBtn.addEventListener('click', function() {
            localStorage.setItem('hibp_password_changed', 'true');
            O_passwordInput.disabled = true;
            O_changeBtn.style.display = 'none';
            O_successMsg.style.display = 'flex';

            // Auto-avancement du chat Lucas (incremente la progression en BDD)
            fetch("/instagram/chat/response?name=" + encodeURIComponent("leo_creative") + "&message=" + encodeURIComponent("hibp_done"))
                .catch(function() {});
        });
    }

    /**
     * Verifie que le mot de passe est suffisamment different du mot de passe fuite
     * @param {string} S_pwd - Le nouveau mot de passe
     * @returns {boolean} true si suffisamment different
     */
    function checkNotSimilar(S_pwd) {
        if (S_pwd.length === 0) return false;
        let S_pwdLower = S_pwd.toLowerCase();
        let S_leakedLower = S_LEAKED_PASSWORD.toLowerCase();
        // Pas le meme mot de passe
        if (S_pwdLower === S_leakedLower) return false;
        // Ne doit pas contenir "lucie" (derive du nom d'utilisateur)
        if (S_pwdLower.includes('lucie')) return false;
        // Ne doit pas contenir "2024"
        if (S_pwd.includes('2024')) return false;
        return true;
    }

    /**
     * Met a jour l'affichage d'un critere de validation
     * @param {string} S_id - L'id de l'element li
     * @param {boolean} B_valid - Si le critere est valide
     * @returns {void}
     */
    function updateCriteria(S_id, B_valid) {
        let O_el = document.getElementById(S_id);
        if (!O_el) return;
        let O_icon = O_el.querySelector('.crit-icon');
        if (B_valid) {
            O_el.classList.add('valid');
            O_el.classList.remove('invalid');
            O_icon.innerHTML = '&#10003;';
        } else {
            O_el.classList.remove('valid');
            O_el.classList.add('invalid');
            O_icon.innerHTML = '&#10007;';
        }
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
