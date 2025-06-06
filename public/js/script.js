document.addEventListener('DOMContentLoaded', function() {
    // Fade-out de mensagens de erro (se existirem)
    const erro = document.querySelector('.erro');
    if (erro) {
        setTimeout(function() {
            erro.classList.add('fade-out');
        }, 3000);
    }

    // Toggle do menu hambúrguer em telas mobile
    const navToggle = document.querySelector('.nav-toggle');
    const navList   = document.querySelector('.nav-list');

    if (navToggle && navList) {
        navToggle.addEventListener('click', function() {
            navList.classList.toggle('active');
            this.querySelector('.hamburger').classList.toggle('is-active');
        });
    }
});
