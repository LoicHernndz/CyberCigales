/**
 * Validation du mot de passe en temps réel
 * 
 * Utilisé sur les pages d'inscription et de réinitialisation de mot de passe.
 * Gère la barre de force et la validation de confirmation.
 * 
 * Éléments HTML attendus :
 * - #password ou #pwd : champ mot de passe principal
 * - #password_repeat ou #pwd-repeat : champ de confirmation
 * - #strengthFill : barre de progression de la force
 * - #strengthText : texte décrivant la force
 * - #req-length, #req-uppercase, #req-lowercase, #req-number : indicateurs (optionnels)
 */
document.addEventListener('DOMContentLoaded', function () {
    // Détection automatique des champs (signup ou reset password)
    var pwdInput = document.getElementById('password') || document.getElementById('pwd');
    var pwdRepeat = document.getElementById('password_repeat') || document.getElementById('pwd-repeat');
    var strengthFill = document.getElementById('strengthFill');
    var strengthText = document.getElementById('strengthText');

    if (!pwdInput) return;

    /**
     * Met à jour l'icône d'un critère (check/uncheck)
     */
    function updateRequirement(id, met) {
        var element = document.getElementById(id);
        if (!element) return;
        var icon = element.querySelector('.material-icons');
        if (met) {
            element.classList.add('met');
            if (icon) icon.textContent = 'check_circle';
        } else {
            element.classList.remove('met');
            if (icon) icon.textContent = 'radio_button_unchecked';
        }
    }

    /**
     * Évalue la force du mot de passe et met à jour l'interface
     */
    pwdInput.addEventListener('input', function () {
        var password = this.value;
        var strength = 0;

        var hasLength = password.length >= 8;
        var hasUppercase = /[A-Z]/.test(password);
        var hasLowercase = /[a-z]/.test(password);
        var hasNumber = /[0-9]/.test(password);

        // Mise à jour des indicateurs de critères (si présents)
        updateRequirement('req-length', hasLength);
        updateRequirement('req-uppercase', hasUppercase);
        updateRequirement('req-lowercase', hasLowercase);
        updateRequirement('req-number', hasNumber);

        if (hasLength) strength += 25;
        if (hasUppercase) strength += 25;
        if (hasLowercase) strength += 25;
        if (hasNumber) strength += 25;

        // Mise à jour de la barre de force
        if (strengthFill) {
            strengthFill.style.width = strength + '%';

            if (strength === 0) {
                strengthFill.className = 'strength-fill';
            } else if (strength <= 25) {
                strengthFill.className = 'strength-fill weak';
            } else if (strength <= 50) {
                strengthFill.className = 'strength-fill medium';
            } else if (strength <= 75) {
                strengthFill.className = 'strength-fill good';
            } else {
                strengthFill.className = 'strength-fill strong';
            }
        }

        if (strengthText) {
            if (strength === 0) {
                strengthText.textContent = 'Entrez un mot de passe';
            } else if (strength <= 25) {
                strengthText.textContent = 'Mot de passe faible';
            } else if (strength <= 50) {
                strengthText.textContent = 'Mot de passe moyen';
            } else if (strength <= 75) {
                strengthText.textContent = 'Bon mot de passe';
            } else {
                strengthText.textContent = 'Mot de passe fort';
            }
        }
    });

    // Validation de la confirmation
    if (pwdRepeat) {
        pwdRepeat.addEventListener('input', function () {
            if (this.value !== pwdInput.value) {
                this.setCustomValidity('Les mots de passe ne correspondent pas');
            } else {
                this.setCustomValidity('');
            }
        });
    }
});
