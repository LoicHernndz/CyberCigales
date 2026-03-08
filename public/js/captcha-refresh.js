document.addEventListener('DOMContentLoaded', function () {
    var btn = document.querySelector('.btn-captcha-refresh');
    var img = document.getElementById('captcha-img');
    if (!btn || !img) return;

    function refreshCaptcha() {
        img.src = '/captcha?ts=' + Date.now();
    }

    btn.addEventListener('click', refreshCaptcha);
    img.addEventListener('click', refreshCaptcha);
    img.style.cursor = 'pointer';
});
