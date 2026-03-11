var INTRO_STORAGE_KEY = 'escape_game_intro_seen';

var INTRO_LINES = [
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

function playIntro() {
    var overlay = document.getElementById('intro-overlay');
    if (!overlay) return;

    var textEl = document.getElementById('intro-text');
    var skipBtn = document.getElementById('intro-skip');
    var startBtn = document.getElementById('intro-start');
    var cursor = document.getElementById('intro-cursor');

    // Reset
    textEl.textContent = '';
    startBtn.style.display = 'none';
    if (cursor) cursor.style.display = '';
    overlay.style.opacity = '1';
    overlay.style.display = 'flex';

    var fullText = INTRO_LINES.join('\n');
    var charIndex = 0;
    var typingTimer = null;

    function typeNext() {
        if (charIndex < fullText.length) {
            textEl.textContent += fullText[charIndex];
            charIndex++;
            textEl.parentElement.scrollTop = textEl.parentElement.scrollHeight;
            typingTimer = setTimeout(typeNext, 20);
        } else {
            if (cursor) cursor.style.display = 'none';
            startBtn.style.display = 'inline-block';
        }
    }

    function closeIntro() {
        clearTimeout(typingTimer);
        localStorage.setItem(INTRO_STORAGE_KEY, '1');
        overlay.style.opacity = '0';
        setTimeout(function() { overlay.style.display = 'none'; }, 500);
    }

    // Remove old listeners by cloning
    var newSkip = skipBtn.cloneNode(true);
    skipBtn.parentNode.replaceChild(newSkip, skipBtn);
    var newStart = startBtn.cloneNode(true);
    startBtn.parentNode.replaceChild(newStart, startBtn);

    newSkip.addEventListener('click', function() {
        if (charIndex < fullText.length) {
            clearTimeout(typingTimer);
            textEl.textContent = fullText;
            charIndex = fullText.length;
            if (cursor) cursor.style.display = 'none';
            newStart.style.display = 'inline-block';
        } else {
            closeIntro();
        }
    });

    newStart.addEventListener('click', closeIntro);

    typeNext();
}

// Auto-play on first visit
(function() {
    if (!localStorage.getItem(INTRO_STORAGE_KEY)) {
        playIntro();
    }
})();
