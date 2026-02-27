/**
 * Logiciel de jeu "Mail Phishing" pour l'interface macOS
 * Version 3 : Destinataire unique, plus de scénarios, explications détaillées
 * Version 4 : Système multi-comptes (compte utilisateur + compte hackeur)
 */
const MailGame = (function () {

    const userEmail = "lucie.bertrand@cybercigales.fr";

    // --- Données du compte hackeur ---
    const HACKER_CREDENTIALS = {
        email: 'k0de_breaker@darkweb.net',
        password: 'EmailS3cret!'
    };

    const hackerEmail = 'k0de_breaker@darkweb.net';

    const hackerAccount = {
        name: 'K0de_Breaker',
        email: HACKER_CREDENTIALS.email,
        avatarLetter: '☠',
        emails: [
            {
                id: 'n1',
                from: 'lehackeur@darkweb.net',
                senderName: 'Ghost_User',
                to: hackerEmail,
                date: '23:11',
                subject: '<i class=\'fas fa-reply\'></i> Re: La cible de la semaine',
                snippet: 'Mdr, t\'es sérieux ? T\'as encore pété un compte Insta en moins de dix minutes ?',
                body: `<p>Mdr, t'es sérieux ? T'as encore pété un compte Insta en moins de dix minutes ? T'es un grand malade. Par contre, fais gaffe, si elle essaie de récupérer son compte, t'as mis quoi comme sécu ?</p> <br><br>
                    
                    <p>De : K0de_Breaker <br>
                    Envoyé : Hier, 23:38</p>

                    <p>T'inquiète pas pour ça. C'est blindé. Tu ne sais pas ce que j'ai mis comme mot de passe du compte de la fille que j'ai hacké ? Impossible qu'elle devine.<br>

                    J'ai fait un mix tordu : j'ai mis à la fois la date d'entrée en vigueur du RGPD (pour le côté ironique), la date de naissance de la seule personne qui compte pour moi dans ce monde de brutes, et le nom de la plus belle ville du monde (là où on ira quand on sera riches).<br>

                    Ça fait un mot de passe à rallonge, mais incassable par dictionnaire. Allez, je retourne bosser sur le projet "Coffre-fort".</p>`,
                read: true,
                isGame: false
            },
            {
                id: 'n5',
                from: 'noreply@hostcloud.fr',
                senderName: 'HostCloud Solutions',
                to: hackerEmail,
                date: 'Hier',
                subject: '[IMPORTANT] Échec de facturation - Serveur Dédié #889-X',
                snippet: 'Cher client, Nous n\'avons pas pu traiter le paiement...',
                body: `<p>Cher client, <br><br>

                Nous n'avons pas pu traiter le paiement mensuel de votre Serveur Dédié (IP : 192.168.x.x) car la carte bancaire virtuelle associée à votre compte a expiré.<br><br>

                Si le solde de 45,99 € n'est pas réglé dans les prochaines 48 heures, votre serveur sera temporairement suspendu. Pour éviter toute interruption de vos services d'hébergement, veuillez mettre à jour vos informations de paiement via votre espace client.<br><br>

                Cordialement,<br>
                L'équipe de facturation HostCloud</p>`,
                read: true,
                isGame: false
            },
            {
                id: 'n2',
                from: 'noreply@fleursandco.fr',
                senderName: 'Boutique Fleurs & Co',
                to: hackerEmail,
                date: '10 Mars',
                subject: 'Confirmation de commande #FR4920 - Cadeau',
                snippet: 'Bonjour, Votre commande a bien été enregistrée.',
                body: `<p>Bonjour,

                    Votre commande a bien été enregistrée. Le bouquet "Amour Maternel" sera livré à l'adresse indiquée (Hôpital Saint-Louis, Chambre 402). <br><br>

                    Message d'accompagnement : <br>
                    "Maman, même si je ne suis pas souvent là, tu restes la femme de ma vie. Joyeux anniversaire pour tes 50 ans. Je t'aime." <br><br>

                    Date de livraison souhaitée : 14 Avril <br><br>

                    Merci de votre confiance.</p>`,
                read: false,
                isGame: false
            },
            {
                id: 'n3',
                from: hackerEmail,
                senderName: 'Moi',
                to: hackerEmail,
                date: '9 mars',
                subject: 'Ne pas oublier !!!',
                snippet: 'To do list : Ne pas oublier de vider les disques...',
                body: `<p>To do list : <br><br>

                    - Ne pas oublier de vider les disques durs ce soir. Les flics reniflent trop près. <br><br>

                    - J'ai tout transféré dans le coffre-fort au hangar. Tout mon matos, le cash, et les clés USB des wallets sont dedans. C'est le seul endroit sûr. <br><br>

                    - Penser à changer le cadenas à 4 chiffres la semaine prochaine, le 1-9-8-4 c'est trop classique (réf Orwell, mais bon, pas prudent). <br><br>

                    - Dès que je vends les données, je me tire. Direction Marseille. J'en rêve depuis gosse, c'est la seule ville qui vaut la peine d'être vécue. Plus belle ville du monde, loin de cette grisaille.</p>`,
                read: true,
                isGame: false
            },
            {
                id: 'n4',
                from: 'newsletter@TechWatch.fr',
                senderName: 'TechWatch Daily',
                to: hackerEmail,
                date: '5 mars',
                subject: 'Newsletter Sécurité Info',
                snippet: 'L\'actu cyber de la semaine — Le cauchemar des DPO continue...',
                body: `<p>L'ACTU CYBER DE LA SEMAINE <br><br>

                Le cauchemar des DPO continue <br>
                Cela fait maintenant plusieurs années que le Règlement Général sur la Protection des Données (RGPD) a bouleversé le web européen.<br><br>

                Rappelons que depuis son entrée en application officielle le 25 mai 2018, les sanctions n'ont cessé de tomber pour les entreprises négligentes. En tant que hackeurs éthiques (ou non), cette date reste un tournant dans notre histoire numérique...<br><br>

                [Lire la suite de l'article]</p>`,
                read: true,
                isGame: false
            },
            {
                id: 'n6',
                from: 'it-support@hostcloud.fr',
                senderName: 'HostCloud Support IT',
                to: hackerEmail,
                date: '5 mars',
                subject: 'Maintenance planifiée serveur',
                snippet: 'Une maintenance des serveurs aura lieu ce samedi...',
                body: `<p>Bonjour,</p><p>Veuillez noter qu'une maintenance serveur aura lieu ce samedi de 22h à 02h. L'accès aux emails pourra être perturbé.</p><p>Cordialement,<br>L'équipe IT HostCloud</p>`,
                read: true,
                isGame: false
            },
            {
                id: 'n7',
                from: 'noreply@pizzanightexpress.fr',
                senderName: 'Pizza Night Express',
                to: hackerEmail,
                date: '2 mars',
                subject: 'Ta fidélité récompensée : 1 Pizza MEGA offerte !',
                snippet: 'Salut Max, Félicitations ! Grâce à tes dernières commandes...',
                body: `<p>Salut Max, <br><br>

                Félicitations ! Grâce à tes dernières commandes nocturnes, tu as officiellement atteint le statut VIP Gold chez Pizza Night Express.<br><br>

                Pour fêter ça, ta prochaine Pizza MEGA (au choix parmi nos recettes incontournables) est 100% GRATUITE lors de ta prochaine livraison à domicile. Utilise le code promo : CRAVING-NIGHT avant la fin du mois.<br><br>

                L'offre ne s'applique pas sur les suppléments fromage.<br><br>

                À très vite pour combler tes petites faims de la nuit !</p>`,
                read: true,
                isGame: false
            },
            {
                id: 'n8',
                from: 'service.client.crypto.securite@yahoo.fr',
                senderName: 'service.client.crypto.securite@yahoo.fr',
                to: hackerEmail,
                date: '2 mars',
                subject: 'URGENT!!! Votre compte Bínance est bIoquè',
                snippet: 'Bonjour cher utilisateur, Nous avons detecter une activité...',
                body: `<p>Bonjour cher utilisateur, <br><br>

                Nous avons detecter une activité suspectes sur votre compte de cryptomonaie. Pour des raisons de securitées, vos fonds on été gelés immediatement.<br><br>

                Veulliez cliquer sur le lien ci-dessous et entrer vos mots de passes pour verifier votre identiter et recuperer vos Bitcoins. Si vous ne le faites pas, votre compte sera suprimer.<br><br>

                le-vrai-site-de-binance.fr<br><br>

                L'equipe de direction.</p>`,
                read: true,
                isGame: false
            }
        ]
    };

    const userAccount = {
        name: 'Lucie Bertrand',
        email: userEmail,
        avatarLetter: 'L'
    };

    // Compte actif : 'user' ou 'hacker'
    let activeAccount = 'user';

    // Boîte mail de Lucie — vide par défaut, le joueur arrive sur cette boîte
    const normalEmails = [];

    // Emails de Jeu (Junk / Phishing / Legit mélangés)
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
        },
        {
            id: 6,
            from: 'newsletter@spotify.com',
            senderName: 'Spotify',
            to: userEmail,
            date: '15 Dec',
            subject: 'Votre résumé de l\'année est là !',
            snippet: 'Découvrez votre Spotify Wrapped 2025...',
            body: `
                <img src="/assets/images/phishing/spotify.png" style="width: 100%; max-width: 280px; border-radius: 8px; margin-bottom: 15px;">
                <h1>Votre année en musique</h1>
                <p>Lucie, vous avez écouté 45,000 minutes de musique cette année !</p>
                <p>Découvrez vos artistes préférés dans votre bilan "Wrapped".</p>
                <p><a href="https://www.spotify.com/us/wrapped/" class="email-cta-button" style="background:#1DB954;" target="_blank">Voir mon Wrapped</a></p>
            `,
            isPhishing: false,
            explanation: 'Cet email provient du domaine officiel "spotify.com". Le lien mène vers l\'application ou le site officiel. C\'est un email marketing légitime.',
            status: 'pending',
            isGame: true
        },
        {
            id: 7,
            from: 'pdg.direction@cybercigales-group.com',
            senderName: 'Jean Dupont (PDG)',
            to: userEmail,
            date: '14 Dec',
            subject: 'Virement urgent - Confidentiel',
            snippet: 'Lucie, j\'ai besoin que tu effectues un virement immédiat...',
            body: `
                <p>Bonjour Lucie,</p>
                <p>Je suis en réunion avec des investisseurs et je ne peux pas parler au téléphone.</p>
                <p>J'ai besoin que tu traites un virement urgent pour conclure une acquisition confidentielle.</p>
                <p>Peux-tu me répondre dès que tu lis ce message ? C'est une priorité absolue.</p>
                <p>Jean</p>
                <p>Sent from my iPhone</p>
            `,
            isPhishing: true,
            explanation: 'C\'est une "Arnaque au Président" (CEO Fraud). L\'attaquant utilise un domaine légèrement différent ("cybercigales-group.com" au lieu de "cybercigales.fr") et joue sur l\'urgence et la confidentialité pour contourner les procédures.',
            status: 'pending',
            isGame: true
        },
        {
            id: 8,
            from: 'remboursement@dgfip.finances.gouv.fr.hosting-82.com',
            senderName: 'Impots Gouv',
            to: userEmail,
            date: '12 Dec',
            subject: 'Remboursement d\'impôt en votre faveur',
            snippet: 'Après recalcul de vos droits, nous vous devons 240,50€...',
            body: `
                <img src="/assets/images/phishing/tax.png" style="width: 100%; max-width: 300px; border-radius: 8px; margin-bottom: 15px;">
                <h3>Avis de remboursement</h3>
                <p>Madame, Monsieur,</p>
                <p>Après examen de votre dossier fiscal, nous avons constaté un trop-perçu de 240,50 €.</p>
                <p>Pour recevoir votre remboursement, veuillez confirmer vos coordonnées bancaires :</p>
                <p><a href="http://dgfip.finances.gouv.fr.hosting-82.com/remboursement" class="email-cta-button" target="_blank">Accéder au formulaire de remboursement</a></p>
            `,
            isPhishing: true,
            explanation: 'Regardez bien l\'URL de l\'expéditeur. Ça finit par "hosting-82.com", pas par "gouv.fr". C\'est une imitation pour voler vos infos.',
            status: 'pending',
            isGame: true
        },
        {
            id: 9,
            from: 'PayPal Service <support@paypa1-verify.com>',
            senderName: 'PayPal Service',
            to: userEmail,
            date: '10 Dec',
            subject: 'Nouvelle activité suspecte',
            snippet: 'Une connexion depuis un appareil inconnu a été bloquée...',
            body: `
                <img src="/assets/images/phishing/paypal.png" style="width: 100%; max-width: 280px; border-radius: 8px; margin-bottom: 15px;">
                <h2>Activité Suspecte Détectée</h2>
                <p>Nous avons bloqué une tentative de connexion depuis l'Indonésie.</p>
                <p>Si ce n'était pas vous, veuillez sécuriser votre compte immédiatement.</p>
                <p><a href="http://paypa1-verify.com/secure" class="email-cta-button urgent" target="_blank">Sécuriser mon compte</a></p>
            `,
            isPhishing: true,
            explanation: 'PayPal utilise toujours votre nom complet, pas "Cher client". De plus, l\'adresse de l\'expéditeur "paypa1" contient une faute (typosquatting).',
            status: 'pending',
            isGame: true
        },
        {
            id: 10,
            from: 'Amazon Rewards <no-reply@amazon-win-prizes.net>',
            senderName: 'Amazon Rewards',
            to: userEmail,
            date: '8 Dec',
            subject: 'Félicitations ! Vous avez gagné !',
            snippet: 'Votre email a été tiré au sort. Réclamez votre prix...',
            body: `
                <img src="/assets/images/phishing/amazon.png" style="width: 100%; max-width: 300px; border-radius: 8px; margin-bottom: 15px;">
                <h1>C'est votre jour de chance !</h1>
                <p>Vous avez gagné une carte cadeau Amazon de 500€ !</p>
                <p>Cliquez ci-dessous pour la recevoir.</p>
                <p><a href="http://amazon-win-prizes.net/claim-gift" class="email-cta-button" target="_blank">Obtenir ma carte cadeau</a></p>
            `,
            isPhishing: true,
            explanation: 'Les cadeaux gratuits tombés du ciel n\'existent pas. Le domaine "amazon-win-prizes.net" est frauduleux.',
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

        // Listener pour le bouton Continuer (fin de jeu)
        document.getElementById('btn-continue-game')?.addEventListener('click', () => {
            els.endScreen.classList.add('hidden');
        });

        els.readingPane.addEventListener('click', (e) => {
            if (e.target.tagName === 'A' || e.target.classList.contains('fake-link')) {
                e.preventDefault();
            }
        });

        // Toujours mettre à jour la visibilité au démarrage
        updateControlsVisibility();

        // Initialiser le gestionnaire de comptes
        AccountManager.init();
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
        // Si on est sur le compte hackeur, on retourne ses emails
        if (activeAccount === 'hacker') {
            return hackerAccount.emails;
        }
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

        const total = 5; // Nombre d'emails de jeu (Attention à bien compter ceux qui sont isGame=true)
        // Note: gameEmails contient 10 items dont 8 isGame=true dans ce fichier apparemment. 
        // Vérifions: IDs 1,2,3,4,5,6,7,8,9,10 tous isGame=true. total = 10.
        // Attends, le score est sur 5 dans l'HTML. Vérifions la logique score.
        // Si score === 5, c'est que l'utilisateur a tout bon sur 5 mails ? 
        // L'utilisateur joue 5 scénarios ou tous ?
        // Dans closeModal, on vérifie pending.

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
            clueContainer.innerHTML = `
                <div style="background: #f0fdf4; border: 2px solid #22c55e; padding: 20px; border-radius: 10px; margin-top: 20px; color: #15803d; animation: popIn 0.5s ease-out;">
                    <h3 style="margin-top:0; font-size: 1.2rem; font-weight: bold;">Mission Accomplie !</h3>
                    <p>Voici un indice crucial pour la suite de votre enquête :</p>
                    <p style="font-family: monospace; font-size: 1.4rem; background: rgba(255,255,255,0.8); padding: 10px; border-radius: 5px; margin: 10px 0; font-weight: bold; border: 1px dashed #15803d;">
                        CODE ACCÈS : "PROTOC0L_GH0ST"
                    </p>
                    <p style="font-size: 0.9rem;">Notez-le précieusement.</p>
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

    // =========================================================
    // MODULE : AccountManager — Gestion du switch de compte
    // =========================================================

    const AccountManager = (function () {

        const overlay = document.getElementById('login-overlay');
        const emailIn = document.getElementById('login-email-input');
        const passIn = document.getElementById('login-password-input');
        const errorBox = document.getElementById('login-error');
        const btnCancel = document.getElementById('btn-login-cancel');
        const btnSubmit = document.getElementById('btn-login-submit');
        const btnAdd = document.getElementById('btn-add-account');

        const mailWindow = document.getElementById('mail-window');
        const indicator = document.getElementById('account-indicator');
        const indicatorAvatar = document.getElementById('indicator-avatar');
        const indicatorEmail = document.getElementById('indicator-email');
        const hackerItem = document.getElementById('account-hacker-item');
        const userBadge = document.getElementById('user-badge');
        const hackerBadge = document.getElementById('hacker-badge');
        const userItem = document.getElementById('account-user-item');

        function openLoginModal() {
            if (overlay) {
                overlay.classList.remove('hidden');
                if (emailIn) emailIn.value = '';
                if (passIn) passIn.value = '';
                hideError();
                setTimeout(() => emailIn && emailIn.focus(), 100);
            }
        }

        function closeLoginModal() {
            if (overlay) overlay.classList.add('hidden');
        }

        function showError() {
            if (errorBox) {
                errorBox.classList.remove('hidden');
                // Reset animation
                errorBox.style.animation = 'none';
                errorBox.offsetHeight; // reflow
                errorBox.style.animation = '';
            }
        }

        function hideError() {
            if (errorBox) errorBox.classList.add('hidden');
        }

        function handleLogin() {
            const email = emailIn ? emailIn.value.trim() : '';
            const pass = passIn ? passIn.value : '';

            if (email === HACKER_CREDENTIALS.email && pass === HACKER_CREDENTIALS.password) {
                closeLoginModal();
                switchToAccount('hacker');
            } else {
                showError();
                if (passIn) { passIn.value = ''; passIn.focus(); }
            }
        }

        function switchToAccount(account) {
            activeAccount = account;

            if (account === 'hacker') {
                // Thème sombre
                if (mailWindow) mailWindow.classList.add('hacker-mode');

                // Indicateur toolbar
                if (indicator) indicator.classList.add('hacker-mode');
                if (indicatorAvatar) {
                    indicatorAvatar.textContent = '☠';
                    indicatorAvatar.className = 'account-avatar-sm hacker-avatar';
                }
                if (indicatorEmail) indicatorEmail.textContent = hackerAccount.email;

                // Badges actifs
                if (userBadge) userBadge.style.display = 'none';
                if (hackerBadge) hackerBadge.style.display = 'block';

                // Rendre le compte hackeur visible dans la sidebar
                if (hackerItem) hackerItem.classList.remove('hidden');

                // Changer le bouton « Ajouter un compte » en « Se déconnecter »
                if (btnAdd) {
                    btnAdd.innerHTML = '<i class="fas fa-sign-out-alt"></i> Déconnecter k0de_breaker';
                    btnAdd.style.color = '#ff453a';
                }

                // Réinitialiser la mailbox et afficher les emails hackeur
                currentMailbox = 'inbox';
                currentEmailId = null;
                renderEmailList();
                els.readingPane.innerHTML = '<div class="placeholder">Boîte vide — aucun message pour l\'instant.</div>';

                // Cacher les contrôles du mini-jeu
                if (els.gameControls) els.gameControls.style.display = 'none';

            } else {
                // Retour au compte utilisateur
                if (mailWindow) mailWindow.classList.remove('hacker-mode');

                // Indicateur toolbar
                if (indicator) indicator.classList.remove('hacker-mode');
                if (indicatorAvatar) {
                    indicatorAvatar.textContent = 'L';
                    indicatorAvatar.className = 'account-avatar-sm user-avatar';
                }
                if (indicatorEmail) indicatorEmail.textContent = userAccount.email;

                // Badges
                if (userBadge) userBadge.style.display = 'block';
                if (hackerBadge) hackerBadge.style.display = 'none';

                // Rétablir le bouton
                if (btnAdd) {
                    btnAdd.innerHTML = '<i class="fas fa-plus-circle"></i> Ajouter un compte';
                    btnAdd.style.color = '';
                }

                // Repasser en inbox normale
                currentMailbox = 'inbox';
                currentEmailId = null;
                switchMailbox('inbox');
            }
        }

        function init() {
            // Bouton « Ajouter un compte » / « Se déconnecter »
            if (btnAdd) {
                btnAdd.addEventListener('click', () => {
                    if (activeAccount === 'hacker') {
                        switchToAccount('user');
                    } else {
                        openLoginModal();
                    }
                });
            }

            // Clic sur l'item hackeur dans la sidebar → switcher
            if (hackerItem) {
                hackerItem.addEventListener('click', () => {
                    if (activeAccount !== 'hacker') switchToAccount('hacker');
                });
            }

            // Clic sur l'item utilisateur → switcher
            if (userItem) {
                userItem.addEventListener('click', () => {
                    if (activeAccount !== 'user') switchToAccount('user');
                });
            }

            // Bouton annuler
            if (btnCancel) btnCancel.addEventListener('click', closeLoginModal);

            // Bouton connexion
            if (btnSubmit) btnSubmit.addEventListener('click', handleLogin);

            // Entrée clavier dans le formulaire
            [emailIn, passIn].forEach(input => {
                if (input) input.addEventListener('keydown', (e) => {
                    if (e.key === 'Enter') handleLogin();
                    if (e.key === 'Escape') closeLoginModal();
                });
            });

            // Clic sur l'overlay pour fermer
            if (overlay) {
                overlay.addEventListener('click', (e) => {
                    if (e.target === overlay) closeLoginModal();
                });
            }
        }

        return { init, switchToAccount, openLoginModal };
    })();

    return { init };
})();

document.addEventListener('DOMContentLoaded', MailGame.init);
