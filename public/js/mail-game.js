/**
 * Logiciel de jeu "Mail Phishing" pour l'interface macOS
 * Version 3 : Destinataire unique, plus de scénarios, explications détaillées
 */
const MailGame = (function () {

    const userEmail = "lucie.bertrand@cybercigales.fr";

    // Emails Normaux (Inbox) - Réels de l'histoire ou contexte
    const normalEmails = [
        {
            id: 'n1',
            from: 'lehackeur@darkweb.net',
            senderName: 'Le Hackeur',
            to: userEmail,
            date: '9:00 AM',
            subject: 'Fait attention',
            snippet: 'Fais très attention à toi je connais tous tes mots de passes...',
            body: `<p>Fais très attention à toi je connais tous tes mots de passes.</p><p>C'est juste un conseil.</p>`,
            read: true,
            isGame: false
        },
        {
            id: 'n2',
            from: 'lehackeur@darkweb.net',
            senderName: 'Le Hackeur',
            to: userEmail,
            date: '9:35 AM',
            subject: 'Important',
            snippet: 'Réponds à mes messages sinon ça va mal se passer...',
            body: `<p>Réponds à mes messages sinon ça va mal se passer.</p><p>Je sais pas quoi dire mais c'est juste pour montrer que ça fonctionne.</p>`,
            read: false,
            isGame: false
        },
        {
            id: 'n3',
            from: 'younes@amis.fr',
            senderName: 'Younes',
            to: userEmail,
            date: '8:51 AM',
            subject: 'Soit prudente',
            snippet: 'J\'ai vu qu\'il y avait du vergla sur la route. Fais attention...',
            body: `<p>J'ai vu qu'il y avait du vergla sur la route. Fais attention dans les virages. Ça peut être dangereux.</p>`,
            read: true,
            isGame: false
        },
        {
            id: 'n4',
            from: 'adam@amis.fr',
            senderName: 'Adam',
            to: userEmail,
            date: 'Yesterday',
            subject: '<i class=\'fas fa-reply\'></i> Re: Attention il fait froid ce matin',
            snippet: 'Oui j\'ai vu qu\'il faisait froid ce matin mais j\'ai pris une veste tkt.',
            body: `<p>Oui j'ai vu qu'il faisait froid ce matin mais j'ai pris une veste tkt. Merci, c'est gentil en tout cas.</p>`,
            read: true,
            isGame: false
        },
        {
            id: 'n5',
            from: 'matis@amis.fr',
            senderName: 'Matis',
            to: userEmail,
            date: 'Yesterday',
            subject: '<i class=\'fas fa-reply\'></i> Re: Comment ça va',
            snippet: 'Ça va merci et toi ? Tu as passé...',
            body: `<p>Ça va merci et toi ? Tu as passé une bonne journée ?</p>`,
            read: true,
            isGame: false
        },
        // Ajout de quelques mails corpo normaux pour le réalisme
        {
            id: 'n6',
            from: 'it-support@cybercigales.fr',
            senderName: 'Support IT',
            to: userEmail,
            date: 'Lundi',
            subject: 'Maintenance planifiée serveur',
            snippet: 'Une maintenance des serveurs aura lieu ce samedi...',
            body: `<p>Bonjour,</p><p>Veuillez noter qu'une maintenance serveur aura lieu ce samedi de 22h à 02h. L'accès aux emails pourra être perturbé.</p><p>Cordialement,<br>L'équipe IT</p>`,
            read: true,
            isGame: false
        }
    ];

    // Emails de Jeu (Junk / Phishing / Legit mélangés) - 5 Scénarios Clés
    const gameEmails = [
        {
            id: 1,
            from: 'security@bank-verify-alert.com',
            senderName: 'Security Team',
            to: userEmail,
            date: '10:45',
            subject: 'ALERTE SECURITE - ACTION REQUISE IMMEDIATE',
            snippet: 'Nous avons détecté une activité suspecte sur votre compte...',
            body: `
                <img src="/assets/images/phishing/bank.png" style="width: 60px; margin-bottom: 20px;">
                <h2 style="color:#d70015; margin-top:0;">Alerte de Sécurité Critique</h2>
                <p>Cher client,</p>
                <p>Nous avons détecté une activité suspecte sur votre compte bancaire depuis une adresse IP inconnue (Russie).</p>
                <p><strong>Votre compte sera bloqué dans 24h sans validation de votre part.</strong></p>
                <div style="text-align: center;">
                    <a href="http://bank-verify-alert.com/login" class="email-cta-button urgent" target="_blank">VÉRIFIER MON IDENTITÉ MAINTENANT</a>
                </div>
            `,
            isPhishing: true,
            explanation: 'ALERTE ROUGE : Cet email joue sur la peur ("compte bloqué", "Russie") pour vous faire agir vite. De plus, regardez l\'expéditeur : "bank-verify-alert.com" n\'est PAS le site officiel de votre banque.',
            status: 'pending',
            isGame: true
        },
        {
            id: 2,
            from: 'service-client@netflix-fix-billing.com',
            senderName: 'Netflix Support',
            to: userEmail,
            date: '09:12',
            subject: 'Paiement refusé : Abonnement suspendu',
            snippet: 'Votre dernier paiement a échoué. Mettez à jour vos infos...',
            body: `
                <img src="/assets/images/phishing/netflix.png" style="width: 100%; max-width: 300px; border-radius: 8px; margin-bottom: 15px;">
                <h1>Netflix</h1>
                <p>Bonjour,</p>
                <p>Nous n'avons pas pu prélever votre abonnement mensuel.</p>
                <p>Pour continuer à profiter de vos séries préférées, veuillez mettre à jour vos informations de paiement ci-dessous.</p>
                <p><a href="http://netflix-fix-billing.com/update-payment" class="email-cta-button" target="_blank">Mettre à jour le paiement</a></p>
            `,
            isPhishing: true,
            explanation: 'Regardez l\'adresse email de l\'expéditeur : "netflix-fix-billing.com". Netflix utiliserait uniquement "netflix.com". C\'est une tentative pour voler votre carte bancaire.',
            status: 'pending',
            isGame: true
        },
        {
            id: 3,
            from: 'rh@cybercigales.fr',
            senderName: 'RH CyberCigales',
            to: 'equipe@cybercigales.fr',
            date: 'Hier',
            subject: 'Mise à jour mutuelle santé',
            snippet: 'Veuillez trouver ci-joint les nouveaux tableaux de garanties...',
            body: `
                <p>Bonjour à tous,</p>
                <p>Veuillez trouver sur le lien intranet ci-dessous les nouveaux tableaux de garanties de notre mutuelle santé pour 2026.</p>
                <p><span class="fake-link">Intranet : Documents Mutuelle 2026.pdf</span></p>
                <div class="email-signature"><p>Hélène, Service RH</p></div>
            `,
            isPhishing: false,
            explanation: 'Cet email est SUR. L\'expéditeur est bien interne "@cybercigales.fr", le ton est professionnel, et le lien pointe vers l\'intranet de l\'entreprise.',
            status: 'pending',
            isGame: true
        },
        {
            id: 4,
            from: 'chronopost-livraison@gmail.com',
            senderName: 'Chronopost Suivi',
            to: userEmail,
            date: 'Hier',
            subject: 'Votre colis est bloqué au centre de tri',
            snippet: 'Des frais de douane restent impayés (2.99€)...',
            body: `
                <img src="/assets/images/phishing/chronopost.png" style="width: 100%; max-width: 300px; border-radius: 8px; margin-bottom: 15px;">
                <h3>Statut de livraison : BLOQUÉ</h3>
                <p>Votre colis n°FR892382 a été suspendu car des frais de douane (2.99€) n'ont pas été réglés.</p>
                <p>Pour débloquer la livraison prévue demain, veuillez régulariser la situation :</p>
                <p><a href="http://chronopost-suivi-express.xyz/frais" class="email-cta-button" target="_blank">Payer les frais de douane</a></p>
            `,
            isPhishing: true,
            explanation: 'Chronopost n\'utilise JAMAIS une adresse "@gmail.com" pour ses communications officielles. C\'est une arnaque très courante pour récupérer vos numéros de carte bleue.',
            status: 'pending',
            isGame: true
        },
        {
            id: 5,
            from: 'microsoft-security@mso-auth-update.info',
            senderName: 'Microsoft 365',
            to: userEmail,
            date: '17 Dec',
            subject: 'Mot de passe expiré',
            snippet: 'Votre mot de passe expire aujourd\'hui. Conservez votre accès...',
            body: `
                <img src="/assets/images/phishing/microsoft.png" style="width: 120px; margin-bottom: 20px;">
                <p>Bonjour ${userEmail},</p>
                <p>Le mot de passe de votre compte professionnel expire aujourd'hui.</p>
                <p>Vous pouvez conserver votre mot de passe actuel en vous connectant ici :</p>
                <p><a href="http://mso-auth-update.info/reset-password" class="email-cta-button" target="_blank">Conserver mon mot de passe</a></p>
                <p>Microsoft Security Team</p>
            `,
            isPhishing: true,
            explanation: '"mso-auth-update.info" n\'est pas un domaine Microsoft. De plus, Microsoft ne vous demandera jamais de cliquer sur un lien pour "conserver" un mot de passe de cette manière.',
            status: 'pending',
            isGame: true
        }
    ];

    let currentMailbox = 'inbox'; // 'inbox' ou 'junk'
    let currentEmailId = null;
    let score = 0;

    // DOM Elements
    const els = {
        emailList: document.getElementById('email-list-container'),
        readingPane: document.getElementById('reading-pane'),
        scoreDisplay: document.getElementById('game-score').parentElement,
        gameControls: document.querySelector('.toolbar-game-controls'),
        scoreValue: document.getElementById('game-score'),
        inboxCount: document.getElementById('inbox-count'),
        junkCount: document.querySelector('li[data-mailbox="junk"] .icon-label'), // Pour ajouter un badge si on veut
        modal: document.getElementById('feedback-modal'),
        feedbackTitle: document.getElementById('feedback-title'),
        feedbackMessage: document.getElementById('feedback-message'),
        feedbackIcon: document.getElementById('feedback-icon'),
        endScreen: document.getElementById('end-screen'),
        finalScore: document.getElementById('final-score'),
        btnSafe: document.getElementById('btn-mark-safe'),
        btnPhish: document.getElementById('btn-report-phishing'),
        btnNext: document.getElementById('btn-next-scenario')
    };

    function init() {
        // Détection du mode jeu via URL
        const urlParams = new URLSearchParams(window.location.search);
        const isGameMode = urlParams.get('mode') === 'game';

        if (isGameMode) {
            setupGameMode();
            switchMailbox('junk');
        } else {
            switchMailbox('inbox');
            setupStandardMode();
        }

        els.btnSafe.addEventListener('click', () => handleDecision(false));
        els.btnPhish.addEventListener('click', () => handleDecision(true));

        els.btnNext.addEventListener('click', closeModal);
        els.readingPane.addEventListener('click', (e) => {
            if (e.target.tagName === 'A' || e.target.classList.contains('fake-link')) {
                e.preventDefault();
            }
        });

        // Toujours mettre à jour la visibilité au démarrage
        updateControlsVisibility();
    }

    function setupStandardMode() {
        document.querySelectorAll('.mailbox-list li').forEach(item => {
            item.addEventListener('click', () => {
                const mailbox = item.getAttribute('data-mailbox');
                if (mailbox) switchMailbox(mailbox);
            });
        });
    }

    function setupGameMode() {
        // En mode jeu, on cache la sidebar ou on désactive les autres items (sauf Junk)
        document.querySelectorAll('.mailbox-list li').forEach(item => {
            const mailbox = item.getAttribute('data-mailbox');
            if (mailbox !== 'junk') {
                item.style.display = 'none';
            }
        });

        // On modifie le titre 'Mailboxes'
        const title = document.querySelector('.mailbox-title');
        if (title) title.textContent = 'Mission Phishing';

        // Désactive les boutons de la toolbar standard
        const tbGroup = document.querySelector('.toolbar-group');
        if (tbGroup) tbGroup.style.display = 'none';

        // Désactive le tri
        const sortBar = document.querySelector('.sort-bar');
        if (sortBar) sortBar.style.display = 'none';
    }

    function switchMailbox(mailbox) {
        currentMailbox = mailbox;
        currentEmailId = null;

        document.querySelectorAll('.mailbox-list li').forEach(li => li.classList.remove('active-mailbox'));
        const activeLi = document.querySelector(`.mailbox-list li[data-mailbox="${mailbox}"]`);
        if (activeLi) activeLi.classList.add('active-mailbox');

        renderEmailList();

        els.readingPane.innerHTML = '<div class="placeholder">Sélectionnez un email pour le lire...</div>';

        updateControlsVisibility();
    }

    function updateControlsVisibility() {
        if (currentMailbox === 'junk') {
            els.gameControls.style.display = 'flex';
            // Update score display immediately
            els.scoreValue.textContent = score;
        } else {
            els.gameControls.style.display = 'none';
        }
    }

    function getEmailsForCurrentMailbox() {
        if (currentMailbox === 'inbox') return normalEmails;
        if (currentMailbox === 'junk') return gameEmails;
        return [];
    }

    function renderEmailList() {
        const emailsToRender = getEmailsForCurrentMailbox();
        els.emailList.innerHTML = '';

        if (emailsToRender.length === 0) {
            els.emailList.innerHTML = '<div class="placeholder" style="padding:20px;">Aucun message</div>';
            return;
        }

        emailsToRender.forEach(email => {
            const li = document.createElement('li');
            let isRead = email.read;
            if (email.isGame) isRead = (email.status === 'processed');

            li.className = `email-item ${isRead ? 'read' : ''}`;
            if (email.id === currentEmailId) li.classList.add('selected');

            let statusIcon = '';
            if (email.isGame && email.status === 'processed') {
                // Check if decision was correct is not stored on email object directly here for simplicity, 
                // but usually processed means done.
                statusIcon = '<i class="fas fa-check" style="color:#28cd41; margin-right:5px;"></i> ';
            }

            // Affichage propre
            li.innerHTML = `
                <div class="email-header">
                    <span class="sender">${statusIcon}${email.senderName}</span>
                    <span class="time">${email.date}</span>
                </div>
                <div class="subject">${email.subject}</div>
                <div class="snippet">${email.snippet}</div>
            `;

            li.addEventListener('click', () => selectEmail(email.id));
            els.emailList.appendChild(li);
        });
    }

    function selectEmail(id) {
        currentEmailId = id;
        const list = getEmailsForCurrentMailbox();
        const email = list.find(e => e.id === id);

        if (!email.isGame) email.read = true;

        renderEmailList();

        els.readingPane.innerHTML = `
            <h2>${email.subject}</h2>
            <div class="recipient-info">
                <strong>De :</strong> <span title="${email.from}">${email.senderName} &lt;${email.from}&gt;</span> <br>
                <strong>À :</strong> ${email.to}
            </div>
            <div class="email-content">
                ${email.body}
            </div>
        `;
    }

    function handleDecision(isReportPhishing) {
        if (currentMailbox !== 'junk') return;
        if (currentEmailId === null) return;

        const email = gameEmails.find(e => e.id === currentEmailId);

        if (email.status === 'processed') {
            alert("Vous avez déjà traité cet email.");
            return;
        }

        let isCorrect = false;
        if (email.isPhishing && isReportPhishing) isCorrect = true;
        if (!email.isPhishing && !isReportPhishing) isCorrect = true;

        if (isCorrect) score++;

        email.status = 'processed';

        showFeedback(isCorrect, email.explanation);

        els.scoreValue.textContent = score;

        checkEndGame();
    }

    function showFeedback(correct, explanation) {
        els.feedbackTitle.textContent = correct ? "Bien joué !" : "Erreur d'analyse";
        els.feedbackTitle.style.color = correct ? "#28cd41" : "#d70015";

        // Icône différente selon la réussite
        const iconClass = correct ? 'fa-check-circle' : 'fa-times-circle';
        els.feedbackIcon.className = `fas ${iconClass} feedback-icon`;
        els.feedbackIcon.classList.remove('correct', 'incorrect'); // Nettoyage anciennes classes
        els.feedbackIcon.classList.add(correct ? 'correct' : 'incorrect');

        els.feedbackMessage.textContent = explanation;
        els.modal.classList.remove('hidden');
    }

    function closeModal() {
        els.modal.classList.add('hidden');
        renderEmailList();

        const remaining = gameEmails.filter(e => e.status === 'pending').length;
        if (remaining === 0) {
            showEndScreen();
        }
    }

    function checkEndGame() {
        // Déclenché après fermeture modal
    }

    function showEndScreen() {
        els.finalScore.textContent = score;

        // Nettoyer anciens messages
        const oldMsg = els.endScreen.querySelector('p.custom-msg');
        if (oldMsg) oldMsg.remove();

        // Affichage de l'indice si score parfait (ou >= 5 pour être sûr)
        const clueContainer = document.getElementById('game-clue-container');
        if (score >= 5) {
            const msg = "Excellent ! Vous êtes un expert.";
            const p = document.createElement('p');
            p.textContent = msg;
            p.classList.add('custom-msg');
            p.style.marginTop = "10px";
            els.finalScore.parentElement.after(p);

            clueContainer.style.display = 'block';

            // Style "Popup Système"
            clueContainer.style.position = 'fixed';
            clueContainer.style.top = '50%';
            clueContainer.style.left = '50%';
            clueContainer.style.transform = 'translate(-50%, -50%)';
            clueContainer.style.zIndex = '99999';
            clueContainer.style.background = 'white';
            clueContainer.style.borderRadius = '12px';
            clueContainer.style.boxShadow = '0 20px 50px rgba(0,0,0,0.5)';
            clueContainer.style.width = '420px';
            clueContainer.style.maxWidth = '90%';
            clueContainer.style.overflow = 'hidden';

            clueContainer.innerHTML = `
                <div style="background: #22c55e; color: white; padding: 15px; text-align: center;">
                    <h3 style="margin:0; font-size: 1.2rem;">🌟 MISSION ACCOMPLIE 🌟</h3>
                </div>
                <div style="padding: 25px; text-align: center;">
                    <p style="font-size: 1.1rem; color: #333; margin-bottom: 20px;">Félicitations Agent, vous avez déjoué toutes les attaques.</p>
                    <p style="color: #666; font-size: 0.95rem; margin-bottom: 5px;">Voici votre clé de décryptage :</p>
                    <div style="background: #f3f4f6; padding: 15px; border: 2px dashed #ccc; border-radius: 8px; margin: 15px 0;">
                        <span style="font-family: monospace; font-size: 1.6rem; font-weight: bold; color: #15803d; letter-spacing: 2px;">PROTOC0L_GH0ST</span>
                    </div>
                </div>
                <div style="background: #f9fafb; padding: 10px; text-align: center;">
                    <button onclick="this.closest('#game-clue-container').style.display='none'" style="background:#5856d6; color:white; border:none; padding:8px 16px; border-radius:6px; cursor:pointer;">Fermer</button>
                </div>
            `;
        } else {
            const msg = "Pas mal, mais essayez encore pour le score parfait !";
            const p = document.createElement('p');
            p.textContent = msg;
            p.classList.add('custom-msg');
            p.style.marginTop = "10px";
            els.finalScore.parentElement.after(p);

            clueContainer.style.display = 'none';
        }

        els.endScreen.classList.remove('hidden');
    }

    return { init };
})();

document.addEventListener('DOMContentLoaded', MailGame.init);
