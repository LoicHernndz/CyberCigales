// Validation du mot de passe en temps réel
const pwdInput = document.getElementById('password');
const pwdRepeat = document.getElementById('password_repeat');
const strengthFill = document.getElementById('strengthFill');
const strengthText = document.getElementById('strengthText');

if (pwdInput) {
    pwdInput.addEventListener('input', function() {
        const password = this.value;
        let strength = 0;
        
        // Critères de validation
        if (password.length >= 8) strength += 25;
        if (/[A-Z]/.test(password)) strength += 25;
        if (/[a-z]/.test(password)) strength += 25;
        if (/[0-9]/.test(password)) strength += 25;
        
        // Mise à jour de la barre de force
        strengthFill.style.width = strength + '%';
        
        if (strength === 0) {
            strengthFill.className = 'strength-fill';
            strengthText.textContent = 'Entrez un mot de passe';
        } else if (strength <= 25) {
            strengthFill.className = 'strength-fill weak';
            strengthText.textContent = 'Mot de passe faible';
        } else if (strength <= 50) {
            strengthFill.className = 'strength-fill medium';
            strengthText.textContent = 'Mot de passe moyen';
        } else if (strength <= 75) {
            strengthFill.className = 'strength-fill good';
            strengthText.textContent = 'Bon mot de passe';
        } else {
            strengthFill.className = 'strength-fill strong';
            strengthText.textContent = 'Mot de passe fort';
        }
    });
}

// Validation de la confirmation
if (pwdRepeat) {
    pwdRepeat.addEventListener('input', function() {
        if (this.value !== pwdInput.value) {
            this.setCustomValidity('Les mots de passe ne correspondent pas');
        } else {
            this.setCustomValidity('');
        }
    });
}
