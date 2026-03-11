(function() {
    var STORAGE_KEY = 'escape_game_intro_seen';
    var overlay = document.getElementById('intro-overlay');
    if (!overlay) return;

    // Si déjà vu, ne pas afficher
    if (localStorage.getItem(STORAGE_KEY)) {
        overlay.remove();
        return;
    }

    // Afficher l'overlay
    overlay.style.display = 'flex';

    var textEl = document.getElementById('intro-text');
    var skipBtn = document.getElementById('intro-skip');
    var startBtn = document.getElementById('intro-start');
    var cursor = document.getElementById('intro-cursor');

    var lines = [
        "                    ESCAPE GAME — CYBERCIGALES",
        "",
        "> Initialisation du système...",
        "> Connexion sécurisée établie.",
        "",
        "Bienvenue, agent.",
        "",
        "Ce parcours peut se faire seul ou en équipe.",
        "",
        "Si vous jouez en équipe, voici une répartition possible :",
        "  • Un joueur aide mel_133 (Melina)",
        "  • Un joueur aide leo_creative (Léo)",
        "  • Un joueur aide alex_photo (Alexandre)",
        "",
        "Si vous jouez seul, vous devrez aider les trois.",
        "",
        "— CONTEXTE —",
        "",
        "Vous êtes passionné(e) d'informatique et de cybersécurité.",
        "Plusieurs de vos amis sur Instagram se sont fait pirater.",
        "Certains reçoivent des messages chiffrés étranges,",
        "d'autres ont vu leurs données fuiter sur le dark web...",
        "",
        "Grâce à vos connaissances — et celles que vous allez",
        "acquérir au cours de ce parcours — vous devrez les aider",
        "à déchiffrer des messages suspects, retrouver des mots",
        "de passe compromis, et sécuriser leurs comptes.",
        "",
        "> Bonne chance, agent. Le système est entre vos mains."
    ];

    var fullText = lines.join('\n');
    var charIndex = 0;
    var speed = 20;
    var typingTimer = null;

    function typeNext() {
        if (charIndex < fullText.length) {
            textEl.textContent += fullText[charIndex];
            charIndex++;
            // Scroll vers le bas
            textEl.parentElement.scrollTop = textEl.parentElement.scrollHeight;
            typingTimer = setTimeout(typeNext, speed);
        } else {
            // Typing terminé, montrer le bouton commencer
            if (cursor) cursor.style.display = 'none';
            startBtn.style.display = 'inline-block';
        }
    }

    function closeIntro() {
        clearTimeout(typingTimer);
        localStorage.setItem(STORAGE_KEY, '1');
        overlay.style.opacity = '0';
        setTimeout(function() { overlay.remove(); }, 500);
    }

    skipBtn.addEventListener('click', function() {
        // Si le texte n'est pas fini, afficher tout d'un coup
        if (charIndex < fullText.length) {
            clearTimeout(typingTimer);
            textEl.textContent = fullText;
            charIndex = fullText.length;
            if (cursor) cursor.style.display = 'none';
            startBtn.style.display = 'inline-block';
        } else {
            closeIntro();
        }
    });

    startBtn.addEventListener('click', closeIntro);

    // Lancer le typewriter
    typeNext();
})();
