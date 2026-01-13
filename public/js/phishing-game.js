const PhishingGame = (function() {
    
    // Scenarios data with HTML content
    const scenarios = [
        {
            id: 1,
            from: 'Security <security@bank-verify-alert.com>',
            to: 'votre.nom@gmail.com',
            date: '19 Dec 2025, 10:45',
            subject: 'ALERTE SECURITE - ACTION REQUISE IMMEDIATE',
            body: `
                <h2>Alerte de Sécurité Critique</h2>
                <p>Cher client,</p>
                <p>Nous avons détecté une activité suspecte sur votre compte bancaire. Par mesure de sécurité, nous avons temporairement restreint l'accès à vos fonds.</p>
                <p><strong>Si vous n'agissez pas dans les 24 heures, votre compte sera définitivement bloqué.</strong></p>
                <div style="text-align: center;">
                    <a href="#" class="email-cta-button urgent">VÉRIFIER MON IDENTITÉ MAINTENANT</a>
                </div>
                <p>Ne répondez pas à cet email. Ceci est un message automatique.</p>
                <div class="email-signature">
                    <p>Service Sécurité<br>Groupe Bancaire International</p>
                </div>
            `,
            isPhishing: true,
            type: 'urgency',
            explanation: 'Ce mail utilise l\'urgence ("immédiate", "bloqué") pour vous faire paniquer. De plus, l\'adresse de l\'expéditeur "bank-verify-alert.com" est suspecte.'
        },
        {
            id: 2,
            from: 'Amazon Rewards <no-reply@amazon-win-prizes.net>',
            to: 'votre.nom@gmail.com',
            date: '18 Dec 2025, 14:20',
            subject: 'Votre colis est en attente !',
            body: `
                <h1>Félicitations !</h1>
                <p>Vous avez été sélectionné pour gagner le nouvel <strong>iPhone 15 Pro</strong> !</p>
                <p>Notre tirage au sort annuel a désigné votre adresse email comme grande gagnante.</p>
                <p>Pour recevoir votre cadeau, il vous suffit de régler les frais de port (2.99€).</p>
                <div style="text-align: center;">
                    <a href="#" class="email-cta-button">RÉCLAMER MON IPHONE</a>
                </div>
                <p>Offre valable uniquement aujourd'hui.</p>
                <div class="email-signature">
                    <p>L'équipe Amazon Rewards</p>
                </div>
            `,
            isPhishing: true,
            type: 'offer',
            explanation: 'Si c\'est trop beau pour être vrai, c\'est du phishing. Notez l\'adresse en ".net" qui n\'est pas le domaine officiel d\'Amazon.'
        },
        {
            id: 3,
            from: 'RH CyberCigales <rh@cybercigales.fr>',
            to: 'equipe@cybercigales.fr',
            date: '19 Dec 2025, 09:00',
            subject: 'Mise à jour protocole interne',
            body: `
                <p>Bonjour à tous,</p>
                <p>Veuillez trouver ci-dessous le lien vers le nouveau protocole sanitaire mis en place à partir de la semaine prochaine.</p>
                <p>Merci de bien vouloir en prendre connaissance avant lundi.</p>
                <p><span class="fake-link">Consulter le protocole sur l\'intranet (PDF)</span></p>
                <p>Si vous avez des questions, n\'hésitez pas à me contacter.</p>
                <div class="email-signature">
                    <p>Cordialement,<br><strong>Dr. Dupont</strong><br>Direction des Ressources Humaines<br>CyberCigales</p>
                </div>
            `,
            isPhishing: false,
            type: 'none',
            explanation: 'Ce mail est légitime. L\'adresse d\'expédition correspond au domaine de l\'entreprise (@cybercigales.fr) et le contenu est cohérent.'
        },
        {
            id: 4,
            from: 'PayPal Service <support@paypa1-verify.com>',
            to: 'votre.nom@gmail.com',
            date: '17 Dec 2025, 23:10',
            subject: 'Confirmation de paiement',
            body: `
                <h3>Reçu de votre transaction</h3>
                <p>Vous avez envoyé un paiement de 499,00 € à <strong>Gaming Store LLC</strong>.</p>
                <p>Si vous n\'êtes pas à l\'origine de cette transaction, veuillez annuler le paiement immédiatement via notre centre de résolution sécurisé.</p>
                <p>Cliquez sur le lien ci-dessous pour contester :</p>
                <p><span class="fake-link">https://www.paypa1-secure-verify.com/dispute/ref=84521</span></p>
                <div class="email-signature">
                    <p>Merci,<br>PayPal</p>
                </div>
            `,
            isPhishing: true,
            type: 'fake_site',
            explanation: 'L\'expéditeur utilise "paypa1" (avec un 1). C\'est une tentative de tromperie visuelle très courante (typosquatting).'
        },
        {
            id: 5,
            from: 'Police Nationale <ne-pas-repondre@gouv-amendes-fr.info>',
            to: 'citoyen@france.fr',
            date: '16 Dec 2025, 11:05',
            subject: 'CONVOCATION JUDICIAIRE',
            body: `
                <div style="border-left: 4px solid #b71c1c; padding-left: 15px;">
                    <h2 style="color: #b71c1c;">AVIS DE CONTRAVENTION</h2>
                </div>
                <p>Madame, Monsieur,</p>
                <p>Vous avez fait l\'objet d\'un flash radar le 14/12/2025. L\'amende forfaitaire est de 135€.</p>
                <p>Conformément à l\'article 529 du code de procédure pénale, vous devez régler cette somme sous 3 jours pour éviter des poursuites judiciaires.</p>
                <div style="text-align: center;">
                    <a href="#" class="email-cta-button urgent">PAYER L\'AMENDE</a>
                </div>
                <p>En l\'absence de règlement, le dossier sera transmis au tribunal de grande instance.</p>
                <div class="email-signature">
                    <p>Ministère de l\'Intérieur<br>Agence Nationale de Traitement Automatisé des Infractions</p>
                </div>
            `,
            isPhishing: true,
            type: 'auth',
            explanation: 'Les sites gouvernementaux utilisent ".gouv.fr". L\'extension ".info" et le nom de domaine fantaisiste sont des preuves de phishing.'
        }
    ];

    let currentStep = 0;
    let score = 0;
    
    // DOM Elements
    const els = {
        score: document.getElementById('score'),
        subjectBar: document.getElementById('fake-subject-bar'),
        emailFrom: document.getElementById('email-from'),
        emailTo: document.getElementById('email-to'),
        emailDate: document.getElementById('email-date'),
        emailSubject: document.getElementById('email-subject'),
        emailBody: document.getElementById('email-body'),
        phishingTypes: document.getElementById('phishing-types'),
        modal: document.getElementById('feedback-modal'),
        feedbackTitle: document.getElementById('feedback-title'),
        feedbackMessage: document.getElementById('feedback-message'),
        feedbackIcon: document.getElementById('feedback-icon'),
        endScreen: document.getElementById('end-screen'),
        finalScore: document.getElementById('final-score'),
        gameArea: document.getElementById('game-area')
    };

    function init() {
        loadScenario(0);
        
        // Disable links in fake emails
        document.addEventListener('click', function(e) {
            if (e.target.closest('.email-body-content a') || e.target.closest('.email-body-content .fake-link')) {
                e.preventDefault();
            }
        });
    }

    function loadScenario(index) {
        if (index >= scenarios.length) {
            endGame();
            return;
        }

        const scenario = scenarios[index];
        currentStep = index;
        
        // Update UI
        els.score.textContent = score;
        els.subjectBar.textContent = scenario.subject;
        els.emailFrom.textContent = scenario.from;
        els.emailTo.textContent = scenario.to;
        els.emailDate.textContent = scenario.date;
        els.emailSubject.textContent = scenario.subject;
        
        // Inject HTML content
        els.emailBody.innerHTML = scenario.body;
        
        // Reset state
        els.phishingTypes.classList.add('hidden');
        els.modal.classList.add('hidden');
    }

    function showPhishingTypes() {
        els.phishingTypes.classList.remove('hidden');
    }

    function makeDecision(isPhishingChoice, typeChoice = 'none') {
        const scenario = scenarios[currentStep];
        let correct = false;

        if (scenario.isPhishing === false) {
            // It's legitimate
            if (isPhishingChoice === false) correct = true;
        } else {
            // It's phishing
            if (isPhishingChoice === true && typeChoice === scenario.type) correct = true;
        }

        if (correct) score++;
        
        showFeedback(correct, scenario.explanation);
    }

    function showFeedback(correct, explanation) {
        els.feedbackTitle.textContent = correct ? "Correct !" : "Incorrect";
        els.feedbackTitle.style.color = correct ? "#27c93f" : "#ff5f56";
        els.feedbackIcon.className = `feedback-icon ${correct ? 'correct' : 'incorrect'}`;
        els.feedbackMessage.textContent = explanation;
        
        els.modal.classList.remove('hidden');
    }

    function nextScenario() {
        loadScenario(currentStep + 1);
    }

    function endGame() {
        els.gameArea.style.display = 'none';
        els.endScreen.classList.remove('hidden');
        els.finalScore.textContent = score;
    }

    return {
        init,
        showPhishingTypes,
        makeDecision,
        nextScenario
    };
})();

document.addEventListener('DOMContentLoaded', PhishingGame.init);