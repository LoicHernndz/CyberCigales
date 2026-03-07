document.addEventListener('DOMContentLoaded', () => {
    const overlay = document.getElementById('insta-login-overlay');
    const usernameIn = document.getElementById('insta-login-username');
    const passwordIn = document.getElementById('insta-login-password');
    const errorBox = document.getElementById('insta-login-error');
    const btnProfile = document.getElementById('btn-profile-switch');
    const btnSubmit = document.getElementById('insta-login-submit');
    const btnCancel = document.getElementById('insta-login-cancel');

    // Ouvrir la modale
    btnProfile.addEventListener('click', () => {
        overlay.classList.remove('hidden');
        usernameIn.value = '';
        passwordIn.value = '';
        errorBox.classList.add('hidden');
        setTimeout(() => usernameIn.focus(), 100);
    });

    // Fermer la modale
    btnCancel.addEventListener('click', () => overlay.classList.add('hidden'));
    overlay.addEventListener('click', (e) => {
        if (e.target === overlay) overlay.classList.add('hidden');
    });

    // Validation
    function tryLogin() {
        const user = usernameIn.value.trim().toLowerCase();
        const pass = passwordIn.value.toLowerCase();

        if (user === 'mel_133' && pass === 'marseille14042018') {
            overlay.classList.add('hidden');
            window.location.href = '/instagram/user/mel_133';
        } else {
            errorBox.classList.remove('hidden');
            errorBox.style.animation = 'none';
            errorBox.offsetHeight;
            errorBox.style.animation = '';
            passwordIn.value = '';
            passwordIn.focus();
        }
    }

    btnSubmit.addEventListener('click', tryLogin);
    [usernameIn, passwordIn].forEach(input => {
        input.addEventListener('keydown', (e) => {
            if (e.key === 'Enter') tryLogin();
            if (e.key === 'Escape') overlay.classList.add('hidden');
        });
    });
});
