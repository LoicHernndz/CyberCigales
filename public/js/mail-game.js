/**
 * Interface Mail macOS pour l'escape game
 * Système multi-comptes (compte utilisateur + compte hackeur)
 */
const MailGame = (function () {

    const userEmail = "lucie.bertrand@cybercigales.fr";

    // --- Données du compte hackeur (Histoire 1 : K0de_Breaker) ---
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

    // Boîte mail de Lucie
    const normalEmails = [
        {
            id: 'lucie-1',
            from: 'k0de_breaker@darkweb.net',
            senderName: 'K0de_Breaker',
            to: userEmail,
            date: '08:03',
            subject: '⚠️ Dernier avertissement',
            snippet: 'Tu fouines un peu trop à mon goût...',
            body: `<p>Salut <strong>Lucie</strong>,</p>

                <p>Tu ne me connais pas. Mais moi, je sais <strong>exactement</strong> qui tu es.</p>

                <p>Je sais que tu poses des questions. Je sais que tu fouilles là où il ne faut pas. Tes petites "recherches" sur la cybersécurité, tes cours, tes exercices… Tu crois que personne ne regarde ?</p>

                <p><strong>Arrête. Maintenant.</strong></p>

                <p>Tu n'as aucune idée de ce que tu es en train de remuer. Si tu continues à avancer dans cette direction, je te garantis que tu vas le regretter. Je ne suis pas le genre de personne qu'on provoque sans conséquences.</p>

                <p>Considère ceci comme ton <strong>premier et dernier avertissement</strong>.</p>

                <p style="color: #ff4444; font-family: monospace; font-weight: bold;">— K0de_Breaker</p>

                <p style="font-size: 0.85rem; color: #888; margin-top: 20px; font-style: italic;">P.S. : Joli mot de passe d'ailleurs. Tu devrais le changer… si ce n'est pas déjà trop tard.</p>`,
            read: false,
            isGame: false
        }
    ];

    // Emails Junk (spam, phishing, etc. — affichage lecture seule)
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
            isPhishing: true
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
            isPhishing: true
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
            isPhishing: false
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
            isPhishing: true
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
            isPhishing: true
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
            isPhishing: false
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
            isPhishing: true
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
            isPhishing: true
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
            isPhishing: true
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
            isPhishing: true
        }
    ];

    let currentMailbox = 'inbox'; // 'inbox' ou 'junk'
    let currentEmailId = null;

    // DOM Elements
    const els = {
        emailList: document.getElementById('email-list-container'),
        readingPane: document.getElementById('reading-pane'),
        inboxCount: document.getElementById('inbox-count')
    };

    function init() {
        switchMailbox('inbox');
        setupStandardMode();

        els.readingPane.addEventListener('click', (e) => {
            if (e.target.tagName === 'A' || e.target.classList.contains('fake-link')) {
                e.preventDefault();
            }
        });

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

    function switchMailbox(mailbox) {
        currentMailbox = mailbox;
        currentEmailId = null;

        document.querySelectorAll('.mailbox-list li').forEach(li => li.classList.remove('active-mailbox'));
        const activeLi = document.querySelector(`.mailbox-list li[data-mailbox="${mailbox}"]`);
        if (activeLi) activeLi.classList.add('active-mailbox');

        renderEmailList();

        els.readingPane.innerHTML = '<div class="placeholder">Sélectionnez un email pour le lire...</div>';
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
            let isRead = email.read !== false;

            li.className = `email-item ${isRead ? 'read' : ''}`;
            if (email.id === currentEmailId) li.classList.add('selected');

            let statusIcon = '';

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

        email.read = true;

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
            // Persister le compte actif pour le navigateur
            localStorage.setItem('mailActiveAccount', account);
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

                // Réinitialiser la mailbox et afficher les emails hackeur
                currentMailbox = 'inbox';
                currentEmailId = null;
                renderEmailList();
                els.readingPane.innerHTML = '<div class="placeholder">Boîte vide — aucun message pour l\'instant.</div>';

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

            // Restaurer le compte actif depuis le stockage (si la page est rechargée)
            const savedAccount = localStorage.getItem('mailActiveAccount');
            if (savedAccount === 'hacker') {
                switchToAccount('hacker');
            }
        }

        return { init, switchToAccount, openLoginModal };
    })();

    return { init };
})();

document.addEventListener('DOMContentLoaded', MailGame.init);
