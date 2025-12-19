const PhishingGame = (function() {
    
    // Scenarios data
    // Note: In a real app, images would be local files. Here using placeholders.
    const scenarios = [
        {
            id: 1,
            image: 'https://placehold.co/800x500/e0f7fa/006064?text=Banque+Security+Check%0A%0AAttention+votre+compte+va+etre+bloque!%0ACliquez+ici+pour+verifier+vos+infos+urgemment.',
            subject: 'ALERTE SECURITE - ACTION REQUISE IMMEDIATE',
            isPhishing: true,
            type: 'urgency',
            explanation: 'Ce mail utilise l\'urgence ("immédiate", "bloqué") pour vous faire paniquer et cliquer sans réfléchir. C\'est une technique classique.'
        },
        {
            id: 2,
            image: 'https://placehold.co/800x500/fff3e0/e65100?text=Amazon+Cadeau%0A%0AFelicitations!+Vous+avez+gagne+un+iPhone+15.%0AReclamez+votre+prix+maintenant+en+payant+les+frais+de+port.',
            subject: 'Votre colis est en attente !',
            isPhishing: true,
            type: 'offer',
            explanation: 'Si c\'est trop beau pour être vrai, c\'est du phishing. Un iPhone gratuit contre des frais de port est une arnaque courante.'
        },
        {
            id: 3,
            image: 'https://placehold.co/800x500/f3e5f5/4a148c?text=Direction+RH%0A%0AVeuillez+trouver+ci-joint+le+nouveau+protocole+sanitaire.%0A%0ACordialement,%0ADr.+Dupont',
            subject: 'Mise à jour protocole interne',
            isPhishing: false,
            type: 'none',
            explanation: 'Ce mail semble légitime. Le ton est professionnel, pas d\'urgence injustifiée, et l\'expéditeur semble cohérent avec le contenu.'
        },
        {
            id: 4,
            image: 'https://placehold.co/800x500/e8eaf6/1a237e?text=Paypal+Support%0A%0AVeuillez+vous+connecter+sur+paypa1-secure-verify.com%0Apour+confirmer+votre+transaction.',
            subject: 'Confirmation de paiement',
            isPhishing: true,
            type: 'fake_site',
            explanation: 'Regardez bien le lien : "paypa1" avec un chiffre 1 au lieu de "l". C\'est du typosquatting pour vous emmener sur un faux site.'
        },
        {
            id: 5,
            image: 'https://placehold.co/800x500/ffebee/b71c1c?text=Police+Nationale%0A%0AVous+etes+convoque+pour+une+infraction.%0AVeuillez+payer+l\'amende+ici+pour+eviter+les+poursuites.',
            subject: 'CONVOCATION JUDICIAIRE',
            isPhishing: true,
            type: 'auth',
            explanation: 'Les escrocs se font passer pour des autorités (Police, Impôts) pour vous intimider. La police n\'envoie jamais d\'amendes par mail simple.'
        }
    ];

    let currentStep = 0;
    let score = 0;
    
    // DOM Elements
    const els = {
        score: document.getElementById('score'),
        progressBar: document.getElementById('progress-bar'),
        subject: document.getElementById('fake-subject'),
        image: document.getElementById('email-image'),
        loader: document.getElementById('image-loader'),
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
        els.progressBar.style.width = `${(index / scenarios.length) * 100}%`;
        els.subject.textContent = scenario.subject;
        
        // Reset state
        els.phishingTypes.classList.add('hidden');
        els.modal.classList.add('hidden');
        els.loader.classList.remove('hidden');

        // Load image
        els.image.onload = () => els.loader.classList.add('hidden');
        els.image.src = scenario.image;
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
        
        // Final progress bar
        els.progressBar.style.width = '100%';
    }

    return {
        init,
        showPhishingTypes,
        makeDecision,
        nextScenario
    };
})();

document.addEventListener('DOMContentLoaded', PhishingGame.init);
