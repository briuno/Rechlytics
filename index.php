<?php
    // Calcula a URL base conforme a localização deste arquivo.
    // Caso seu DocumentRoot esteja apontando para “public/”, 
    // basta utilizar $_SERVER['REQUEST_SCHEME'].'://'.$_SERVER['HTTP_HOST'].
    $base_url = $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['HTTP_HOST'] . rtrim(dirname($_SERVER['SCRIPT_NAME']), '/');
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rechlytics – Dashboards Power BI Interativos</title>

    <!-- Favicon (pode apontar para uma URL externa ou local opcional) -->
    <link rel="icon" href="https://cdn-icons-png.flaticon.com/512/3099/3099237.png" type="image/png">

    <!-- CSS principal -->
    <link rel="stylesheet" href="<?php echo $base_url; ?>/css/estilos.css">
    <!-- JS principal (contém apenas lógica de fade-out de erros e toggle de menu) -->
    <script src="<?php echo $base_url; ?>/js/script.js" defer></script>
</head>
<body>
    <!-- ================================
         HEADER / NAVBAR
         ================================ -->
    <header class="header">
        <div class="header-container">
            <a href="<?php echo $base_url; ?>" class="logo">
                <!-- Usando ícone de BI do Flaticon (URL externa) -->
                <img src="https://cdn-icons-png.flaticon.com/512/3099/3099237.png" alt="Rechlytics Logo">
            </a>
            <nav class="nav">
                <ul class="nav-list">
                    <li><a href="#sobre">Sobre</a></li>
                    <li><a href="#funcionalidades">Funcionalidades</a></li>
                    <li><a href="#contato">Contato</a></li>
                    <li><a href="<?php echo $base_url; ?>/views/login.php" class="btn-login">Login</a></li>
                    <li><a href="<?php echo $base_url; ?>/views/cadastro.php" class="btn-cadastro">Cadastro</a></li>
                </ul>
                <button class="nav-toggle" aria-label="Abrir menu">
                    <span class="hamburger"></span>
                </button>
            </nav>
        </div>
    </header>

    <!-- ================================
         HERO SECTION
         ================================ -->
    <section class="hero">
        <div class="hero-overlay"></div>
        <div class="hero-content container">
            <h1 class="hero-title">Rechlytics</h1>
            <p class="hero-subtitle">
                Plataforma Web para disponibilizar dashboards interativos do Power BI de forma segura e eficiente.
            </p>
            <a href="<?php echo $base_url; ?>/views/cadastro.php" class="btn-hero">Comece Agora</a>
        </div>
    </section>

    <!-- ================================
         SEÇÃO SOBRE
         ================================ -->
    <section id="sobre" class="sobre section container">
        <div class="section-header">
            <h2>Sobre o Rechlytics</h2>
        </div>
        <div class="sobre-content">
            <p>
                O <strong>Rechlytics</strong> é uma solução corporativa que centraliza seus relatórios Power BI em um único portal,
                garantindo controle de acesso, escalabilidade e suporte especializado. Administradores podem gerenciar dashboards,
                distribuir visualizações personalizadas e acompanhar métricas críticas em tempo real.
            </p>
            <p>
                Usuários finais acessam relatórios de qualquer dispositivo, sem complicações de licenciamento local
                ou necessidade de instalação de softwares adicionais. Toda a infraestrutura é orquestrada para garantir
                alta disponibilidade e performance.
            </p>
        </div>
    </section>

    <!-- ================================
         SEÇÃO FUNCIONALIDADES
         ================================ -->
    <section id="funcionalidades" class="funcionalidades section container">
        <div class="section-header">
            <h2>Funcionalidades</h2>
        </div>
        <div class="features-grid">
            <!-- Feature 1 -->
            <div class="feature-item">
                <!-- Ícone “Integração” via URL (Unsplash) -->
                <img src="https://images.unsplash.com/photo-1588032784869-d0169b0fcff9?ixlib=rb-1.2.1&auto=format&fit=crop&w=80&h=80&q=80" 
                     alt="Integração Power BI">
                <h3>Integração Power BI</h3>
                <p>
                    Conecte relatórios direto do Power BI Service e disponibilize dashboards atualizados
                    automaticamente para seus clientes.
                </p>
            </div>
            <!-- Feature 2 -->
            <div class="feature-item">
                <!-- Ícone “Segurança” via URL (Unsplash) -->
                <img src="https://images.unsplash.com/photo-1581094335763-90b0b842c6dd?ixlib=rb-1.2.1&auto=format&fit=crop&w=80&h=80&q=80" 
                     alt="Controle de Acesso">
                <h3>Controle de Acesso</h3>
                <p>
                    Defina perfis de usuário, permissão por nível e políticas de segurança para garantir que
                    cada colaborador veja apenas o que precisa.
                </p>
            </div>
            <!-- Feature 3 -->
            <div class="feature-item">
                <!-- Ícone “Dashboard” via URL (Unsplash) -->
                <img src="https://images.unsplash.com/photo-1523275335684-37898b6baf30?ixlib=rb-1.2.1&auto=format&fit=crop&w=80&h=80&q=80" 
                     alt="Dashboard Centralizado">
                <h3>Dashboard Centralizado</h3>
                <p>
                    Acesse todos os seus relatórios em um único painel, com métricas consolidadas, notificações
                    de atualização e histórico de visualizações.
                </p>
            </div>
        </div>
    </section>

    <!-- ================================
         CTA SECUNDÁRIA
         ================================ -->
    <section class="cta-second container">
        <h2>Pronto para transformar dados em decisões estratégicas?</h2>
        <a href="<?php echo $base_url; ?>/views/cadastro.php" class="btn-secondary">Solicite Seu Acesso</a>
    </section>

    <!-- ================================
         RODAPÉ / CONTATO
         ================================ -->
    <footer id="contato" class="footer">
        <div class="footer-container section container">
            <div class="footer-col">
                <h4>Contato</h4>
                <p>E-mail: <a href="mailto:suporte@rechlytics.com.br">suporte@rechlytics.com.br</a></p>
                <p>Telefone: (11) 4000-1234</p>
            </div>
            <div class="footer-col">
                <h4>Redes Sociais</h4>
                <ul class="social-list">
                    <li><a href="https://www.linkedin.com" target="_blank" rel="noopener">LinkedIn</a></li>
                    <li><a href="https://twitter.com" target="_blank" rel="noopener">Twitter</a></li>
                    <li><a href="https://github.com" target="_blank" rel="noopener">GitHub</a></li>
                </ul>
            </div>
            <div class="footer-col">
                <h4>Endereço</h4>
                <p>
                    Av. Paulista, 1000<br>
                    São Paulo, SP<br>
                    CEP: 01310-100
                </p>
            </div>
        </div>
        <div class="footer-bottom">
            <p>© 2025 Rechlytics. Todos os direitos reservados.</p>
        </div>
    </footer>
</body>
</html>
teste