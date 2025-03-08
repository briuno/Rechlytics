<?php 
session_start();

// Caminho base dinâmico com domínio correto
$base_url = rtrim((isset($_SERVER['HTTPS']) ? "https" : "http") . "://" . $_SERVER['HTTP_HOST'] . dirname($_SERVER['SCRIPT_NAME'], 2), '/');

// Verificar se o usuário está logado
if (isset($_SESSION['usuario_id'])) {
    // Redirecionamento conforme o tipo de usuário
    header("Location: " . ($_SESSION['usuario_tipo'] == 'admin' ? "$base_url/views/admin/admin_dashboard.php" : "$base_url/views/dashboard.php"));
    exit();
}

// Capturar mensagem de erro da sessão, se existir
$mensagem_erro = isset($_SESSION['erro_login']) ? $_SESSION['erro_login'] : '';
unset($_SESSION['erro_login']); // Remover erro após exibição
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Rechlytics</title>
    <script>
        // Exibir pop-up de erro se houver mensagem
        window.onload = function() {
            var mensagemErro = "<?php echo isset($mensagem_erro) ? addslashes($mensagem_erro) : ''; ?>";
            if (mensagemErro) {
                alert(mensagemErro);
            }
        };
    </script>
</head>
<body>
<br><br><br><br>

<div class="waviy">
   <span style="--i:1">L</span>
    <span style="--i:6"></span>
   <span style="--i:2">O</span>
    <span style="--i:6"></span>
   <span style="--i:3">G</span>
    <span style="--i:6"></span>
   <span style="--i:4">I</span>
    <span style="--i:6"></span>
   <span style="--i:5">N</span>

  </div>
<br><br><br>
    <form action="<?php echo $base_url; ?>/controllers/auth.php" method="POST">

    <form class="login-form">
  <label for="email"></label>
  <input type="email" id="email" class="login-username" autofocus="true" required="true" placeholder="Email" />

  <label for="senha"></label>
  <input type="password" id="senha" class="login-password" required="true" placeholder="Password" />

  <p class="login-forgot-pass"><a href="<?php echo $base_url; ?>/views/auth/esq_senha.php">Esqueci minha senha</a></p>
 

<button class="a" type="submit" name="login">Entrar</button>
    
    </form>


</form>
<br><br>
<div class="underlay-photo"></div>
<div class="underlay-black"></div> 
    <div class="login">
    <div class="pagina">
        
    <section class="pagina"><a>   
    <span class="text" >. L O G A R . </span>

    <span class="line -right"></span>
    <span class="line -top"></span>
    <span class="line -left"></span>
    <span class="line -bottom"></span>

    </form>

</body>


<style>


@import url('https://fonts.googleapis.com/css2?family=Alfa+Slab+One&display=swap');



.waviy {
  position: absolute; /* Posiciona no centro */
  top: 10%; /* Move para o meio */
  left: 50%; /* Move para o meio */
  transform: translate(-50%, -50%); /* Ajusta para ficar exatamente no centro */
  -webkit-box-reflect: below -20px linear-gradient(transparent, rgba(0,0,0,.2));
  font-size: 40px;
  text-align: center; /* Garante que o texto fique centralizado */
}

.waviy span {
  font-family: 'Alfa Slab One', cursive;
  position: relative;
  display: inline-block;
  color: #fff;
  text-transform: uppercase;
  animation: waviy 1s infinite;
  animation-delay: calc(.1s * var(--i));
}

@keyframes waviy {
  0%, 40%, 100% {
    transform: translateY(0);
  }
  20% {
    transform: translateY(-20px);
  }
}

body {
     background-image: url("https://i.imgur.com/IqhJZmI.jpeg");
     overflow: hidden;
     height:100vh;
    width: 100%;
    background-position:center;
    background-repeat: no-repeat;
    background-size: cover;
   background-attachment: fixed;
     
}

.login {
    display: flex;
    justify-content:center; 
    align-items:center; 
    gap: 10px; 
    flex-wrap: wrap; 
    position: relative;
    top: -9%;
}


.pagina a {
    color: white;
    padding: 0.6em 0.84em;
    display: inline-block;
    border: 3px solid transparent;
    position: relative;
    font-size: 1.0em;
    cursor: pointer;
    letter-spacing: 0.07em;
    text-decoration: none;


}
.pagina a .text {
    font-family: proxima-nova, monospace;
    transform: translateY(0.7em);
    display: flex;
    transition: transform 0.4s cubic-bezier(.2,0,0,1) 0.4s;
}
.pagina a::after {
    content: '';
    position: absolute;
    bottom: -3px;
    left: 0.84em;
    right: 8.84em;
    height: 3px;
    background:rgb(0, 86, 177);
    transition: transform 0.8s cubic-bezier(1,0,.37,1) 0.2s,
                right 0.2s cubic-bezier(.04,.48,0,1) 0.6s,
                left 0.4s cubic-bezier(.04,.48,0,1) 0.6s;
    transform-origin: left;
}
.pagina .line {
    position: absolute;
    background: rgb(0, 86, 177);;
}

.pagina .line.-right,
.pagina .line.-left {
    width: 3px;
    bottom: -3px;
    top: -3px;
    transform: scaleY(0);
}

.pagina .line.-top,
.pagina .line.-bottom {
    height: 3px;
    left: -3px;
    right: -3px;
    transform: scaleX(0);
}

.pagina .line.-right {
    right: -3px;
    transition: transform 0.1s cubic-bezier(1,0,.65,1.01) 0.23s;
}

.pagina .line.-top {
    top: -3px;
    transition: transform 0.08s linear 0.43s;
}

.pagina .line.-left {
    left: -3px;
    transition: transform 0.08s linear 0.51s;
}

.pagina .line.-bottom {
    bottom: -3px;
    transition: transform 0.3s cubic-bezier(1,0,.65,1.01);
}

.pagina a:hover .text {
    transform: translateY(0);
    transition: transform 0.6s cubic-bezier(.2,0,0,1) 0.4s;
}

.pagina a:hover::after {
    transform: scaleX(0);
    right: -3px;
    left: -3px;
    transform-origin: right;
    transition: transform 0.2s cubic-bezier(1,0,.65,1.01) 0.17s,
                right 0.2s cubic-bezier(1,0,.65,1.01),
                left 0s 0.3s;
}

.pagina a:hover .line {
    transform: scale(1);
}

.pagina a:hover .line.-right {
    transition: transform 0.1s cubic-bezier(1,0,.65,1.01) 0.2s;
}

.pagina a:hover .line.-top {
    transition: transform 0.08s linear 0.4s;
}

.pagina a:hover .line.-left {
    transition: transform 0.08s linear 0.48s;
}

.pagina a:hover .line.-bottom {
    transition: transform 0.5s cubic-bezier(0,.53,.29,1) 0.56s;
}


.login-form {
  display: flex;
  flex-direction: column; /* Organiza os elementos verticalmente */
  justify-content: center; /* Centraliza verticalmente */
  align-items: center; /* Centraliza horizontalmente */
  height: 100vh; /* Faz com que ocupe toda a tela */
}

.login-text {
  color: white;
  font-size: 1.5rem;
  margin: 0 auto;
  max-width: 50%;
  padding: 0.5rem;
  text-align: center;
}



.login-username, .login-password {
  background: transparent;
  border: none;
  border-bottom: 1px solid rgba(255, 255, 255, 0.5);
  color: white;
  padding: 0.9rem;
  transition: 250ms background ease-in;
  width: 40%; /* Ajusta a largura */
  text-align: start; /* Alinha o texto dentro do input */
margin-top: 50px;
  margin-left: 30%;
}




.login-forgot-pass:hover {
  opacity: 1;
}

</style>
</html>

