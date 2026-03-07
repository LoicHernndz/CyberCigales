// Horloge
function updateTime() {
    const now = new Date();
    const hhmm = String(now.getHours()).padStart(2, '0') + ':' + String(now.getMinutes()).padStart(2, '0');
    const dateStr = now.toLocaleDateString('fr-FR', { weekday: 'long', day: 'numeric', month: 'long' });

    if (document.getElementById('current-time'))
        document.getElementById('current-time').textContent = hhmm;
    if (document.getElementById('lock-big-time'))
        document.getElementById('lock-big-time').textContent = hhmm;
    if (document.getElementById('lock-big-date'))
        document.getElementById('lock-big-date').textContent = dateStr.charAt(0).toUpperCase() + dateStr.slice(1);
}
updateTime();
setInterval(updateTime, 1000);

// Animation bouton au clic
document.getElementById('loginBtn').addEventListener('click', function (e) {
    this.classList.add('loading');
    this.textContent = 'Redirection...';
});
