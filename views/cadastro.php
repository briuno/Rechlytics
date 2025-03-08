<?php
session_start();
include __DIR__ . '/../config/db.php';
include __DIR__ . '/../controllers/log.php'; // Para registrar o cadastro
include __DIR__ . '/../controllers/email.php'; // Para enviar e-mail de ativação

// Caminho base dinâmico com domínio correto
$base_url = rtrim((isset($_SERVER['HTTPS']) ? "https" : "http") . "://" . $_SERVER['HTTP_HOST'] . dirname($_SERVER['SCRIPT_NAME'], 2), '/');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nome = trim($_POST['nome']);
    $email = trim($_POST['email']);
    $senha = $_POST['senha'];
    $confirma_senha = $_POST['confirma_senha'];
    $cpf = trim($_POST['cpf']);
    $telefone = trim($_POST['telefone']);
    $endereco = trim($_POST['endereco']);
    $empresa = trim($_POST['empresa']);

    // Prevenir XSS
    $nome = htmlspecialchars($nome, ENT_QUOTES, 'UTF-8');
    $email = htmlspecialchars($email, ENT_QUOTES, 'UTF-8');
    $cpf = htmlspecialchars($cpf, ENT_QUOTES, 'UTF-8');
    $telefone = htmlspecialchars($telefone, ENT_QUOTES, 'UTF-8');
    $endereco = htmlspecialchars($endereco, ENT_QUOTES, 'UTF-8');
    $empresa = htmlspecialchars($empresa, ENT_QUOTES, 'UTF-8');

    // Verificar se as senhas coincidem
    if ($senha !== $confirma_senha) {
        $_SESSION['msg'] = "⚠ As senhas não coincidem!";
        header("Location: $base_url/views/cadastro.php");
        exit();
    }

    // Verificar se o e-mail ou CPF já está cadastrado
    $stmt = $conn->prepare("SELECT id FROM usuarios WHERE email = ? OR cpf = ?");
    $stmt->bind_param("ss", $email, $cpf);
    $stmt->execute();
    $stmt->store_result();
    
    if ($stmt->num_rows > 0) {
        $_SESSION['msg'] = "⚠ Este e-mail ou CPF já está cadastrado.";
        header("Location: $base_url/views/cadastro.php");
        exit();
    }

    // Criar hash seguro da senha
    $senha_hash = password_hash($senha, PASSWORD_DEFAULT);

    // Cadastrar usuário
    $stmt = $conn->prepare("INSERT INTO usuarios (nome, email, senha, cpf, telefone, endereco, empresa, tipo, email_verificado, reset_token, reset_token_expira) 
                            VALUES (?, ?, ?, ?, ?, ?, ?, 'cliente', 0, ?, DATE_ADD(NOW(), INTERVAL 24 HOUR))");
    $token = bin2hex(random_bytes(16));
    $stmt->bind_param("ssssssss", $nome, $email, $senha_hash, $cpf, $telefone, $endereco, $empresa, $token);
    
    if ($stmt->execute()) {
        $usuario_id = $stmt->insert_id;
        registrarLog($conn, $usuario_id, "Novo usuário cadastrado");

        // Enviar e-mail de ativação
        $ativacao_link = "https://rechlytics.com/views/auth/ativar_conta.php?email=$email&token=$token";
        enviarEmail($email, "Confirme seu cadastro", "Clique no link para ativar sua conta: $ativacao_link");

        $_SESSION['msg'] = "✅ Cadastro realizado com sucesso! Verifique seu e-mail para ativar sua conta.";
        header("Location: $base_url/views/login.php");
        exit();
    } else {
        $_SESSION['msg'] = "❌ Erro ao cadastrar usuário.";
        header("Location: $base_url/views/cadastro.php");
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Cadastro - Rechlytics</title>
</head>
<body>

<div class="waviy">
   <span style="--i:1">C</span>
    <span style="--i:6"></span>
   <span style="--i:2">A</span>
    <span style="--i:6"></span>
   <span style="--i:3">D</span>
    <span style="--i:6"></span>
   <span style="--i:4">A</span>
    <span style="--i:6"></span>
   <span style="--i:5">S</span>
   <span style="--i:6"></span>
   <span style="--i:6">T</span>
   <span style="--i:6"></span>
   <span style="--i:6">R</span>
   <span style="--i:7"></span>
   <span style="--i:6">A</span>
   <span style="--i:8"></span>
   <span style="--i:9">R</span>
  </div>

    
    <BR><BR><BR><BR><BR>
    <?php
    if (isset($_SESSION['msg'])) {
        echo "<p style='color: red;'>" . $_SESSION['msg'] . "</p>";
        unset($_SESSION['msg']);
    }
    ?>
    
    <form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="POST">
        <label ></label>
        
        <input type="text" name="nome" class="login-username" autofocus="true" required placeholder="Nome"  />

        <label></label>
        <input type="text" name="email" class="login-username" autofocus="true" required placeholder="E-mail"  />
        
        <label></label>
        <input type="text" name="cpf" class="login-username" autofocus="true" required placeholder="CPF"  />
        

        <label></label>
        <input type="text" name="telefone" class="login-username" autofocus="true" required  placeholder="Telefone" />
       

        <label></label>
        <input type="text" name="endereco" class="login-username" autofocus="true" required  placeholder="Endereço" />
     

        <label></label>
        <input type="text" name="empresa" class="login-username" autofocus="true" required placeholder="Empresa" />
   

        <label for="senha"></label>
        <input type="password" name="senha" class="login-username" autofocus="true" required minlength="8" placeholder="Senha"/>
        
        
        <label></label>
        <input type="password" name="confirma_senha" class="login-username" required minlength="8"placeholder="Confirma Senha"/>
       

        <button type="submit">Cadastrar</button>
    </form>

    <p><a href="<?php echo $base_url; ?>/views/login.php">🔙 Já tem uma conta? Faça login</a></p>



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

        .login-username, .login-password {
  background: transparent;
  border: none;
  border-bottom: 1px solid rgba(255, 255, 255, 0.5);
  color: white;
  padding: 0.9rem;
  transition: 250ms background ease-in;
  width: 35%; /* Ajusta a largura */
  text-align:start; /* Alinha o texto dentro do input */
margin-top: 15px;
  margin-left: 30%;
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



    </style>
</body>
</html>
