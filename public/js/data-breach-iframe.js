if (window !== window.parent) {
    document.querySelector('.breach-page').classList.add('in-iframe');
    var hdr = document.querySelector('.site-header');
    if (hdr) hdr.style.display = 'none';
    var main = document.querySelector('.main-content');
    if (main) main.style.padding = '0';
}
