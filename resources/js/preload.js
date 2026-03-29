// resources/js/preloader.js

(function () {
    const BAR_ID  = 'rob-pl-bar';
    const TXT_ID  = 'rob-pl-status';
    const ROOT_ID = 'rob-preloader';

    const steps = [
        [20, 'Memuat aset'],
        [45, 'Menginisialisasi'],
        [70, 'Menyiapkan peta'],
        [90, 'Hampir selesai'],
    ];

    const bar = document.getElementById(BAR_ID);
    const txt = document.getElementById(TXT_ID);

    if (!bar || !txt) return;

    let i = 0;
    const tick = setInterval(() => {
        if (i < steps.length) {
            bar.style.width = steps[i][0] + '%';
            txt.textContent = steps[i][1];
            i++;
        } else {
            clearInterval(tick);
        }
    }, 400);

    function dismiss() {
        clearInterval(tick);

        bar.style.width = '100%';
        txt.textContent = 'Siap!';

        setTimeout(() => {
            const el = document.getElementById(ROOT_ID);
            if (!el) return;
            el.style.transition = 'opacity 0.35s ease';
            el.style.opacity    = '0';
            setTimeout(() => el.remove(), 350);
        }, 300);
    }

    window.addEventListener('load', dismiss);
})();