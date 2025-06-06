document.addEventListener('DOMContentLoaded', function() {
    const erro = document.querySelector('.erro');
    if (erro) {
        setTimeout(function() {
            erro.classList.add('fade-out');
        }, 3000);
    }
});
