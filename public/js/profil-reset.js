document.addEventListener('DOMContentLoaded', function () {
    var btn = document.getElementById('btn-reset-game');
    if (!btn) return;

    btn.addEventListener('click', function () {
        if (confirm('Êtes-vous sûr de vouloir réinitialiser toute votre progression de l\'escape game ? (Scores, chats, etc. seront perdus définitivement)')) {
            localStorage.removeItem('lemonde_notif_triggered');
            localStorage.removeItem('instagram_notifications');
            localStorage.setItem('instagram_unread_count', '0');
            localStorage.removeItem('mailActiveAccount');
            window.dispatchEvent(new Event("storage"));

            document.getElementById('form-reset-game').submit();
        }
    });
});
