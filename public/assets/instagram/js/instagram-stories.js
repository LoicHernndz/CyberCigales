(function() {
    var container = document.getElementById('stories-container');
    var leftBtn = document.getElementById('stories-arrow-left');
    var rightBtn = document.getElementById('stories-arrow-right');
    if (!container || !leftBtn || !rightBtn) return;

    function updateArrows() {
        leftBtn.classList.toggle('visible', container.scrollLeft > 0);
        rightBtn.classList.toggle('visible', container.scrollLeft < container.scrollWidth - container.clientWidth - 1);
    }

    leftBtn.addEventListener('click', function() {
        container.scrollBy({ left: -200, behavior: 'smooth' });
    });
    rightBtn.addEventListener('click', function() {
        container.scrollBy({ left: 200, behavior: 'smooth' });
    });

    container.addEventListener('scroll', updateArrows);
    window.addEventListener('resize', updateArrows);
    updateArrows();
})();
