<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://fonts.googleapis.com/css?family=Russo+One" rel="stylesheet">
    <title>Rechlytics - Página Inicial</title>

</head>
<body>

<div class="container">
<br>
<svg viewBox="0 0 1320 300">
	<text x="50%" y="50%" dy=".35em" text-anchor="middle">
		Seja Bem-vindo 
		a Rechlytics
	</text>
 
</svg>	
<br><br>
    <h1 class="animation">
      Sua plataforma para análise e visualização de dados.
    </h1>
   
  </div>

  
    <?php
    $base_url = $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['HTTP_HOST'] . dirname($_SERVER['SCRIPT_NAME']);
    ?>
    
    <div class="login">
    <div class="pagina">
    <section class="pagina"><a href="<?php echo $base_url; ?>/views/login.php">
    <span class="text" >L O G I N</span>
    <span class="line -right"></span>
    <span class="line -top"></span>
    <span class="line -left"></span>
    <span class="line -bottom"></span>
  </a>
</section>
    </div>
    <div class="login">
    <div class="pagina">
    <section class="pagina"><a href="<?php echo $base_url; ?>/views/cadastro.php">
    <span class="text" >C A D A S T R O</span>
    <span class="line -right"></span>
    <span class="line -top"></span>
    <span class="line -left"></span>
    <span class="line -bottom"></span>
   
  </a>
</section>
    </div>
    </div>

    </div>
    
   <div class="button-container"> 
<button class="google-btn">
   <img src="https://img.icons8.com/color/48/000000/google-logo.png" alt="Google Logo">
    <span>Continue with Google</span>
</button>
   </div>

<br>

<div class="button-container"> 
<button class="google-btn">
   <img src="https://img.icons8.com/?size=100&id=62856&format=png&color=000000" alt="Google Logo">
    <span>Continue with GitHub</span>
</button>
</div>

<br>

<div class="button-container"> 
<br><br><br><br><br><br>
</button>
</div>

</body>


<style>


.button-container {
    display: flex;        /* Tornando o contêiner flexível */
    flex-wrap: wrap;      /* Permite que os itens quebrem para a linha seguinte */
    justify-content: center; /* Alinha os itens à esquerda */
    gap: 10px;            /* Espaçamento entre os itens, se necessário */
    top: -55%;
}

.google-btn {
    display: flex;
    align-items: center;
    justify-content: flex-start;
    padding: 7px 17px;
    background-color: #ffffff;
    border: 1px solid #dfdfdf;
    border-radius: 4px;
    color: #333333;
    font-weight: bold;
    cursor: pointer;
    transition: background-color 0.3s ease;
}

.google-btn img {
    width: 30px;
    margin-right: 10px;
}

.google-btn:hover {
    background-color: #f1f1f1;
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
    padding: 0.7em 0.84em;
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
    right: 0.84em;
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


svg {
	font-family: 'Russo One', sans-serif;
	position: absolute; 
	width: 100%; height: 100%;
  margin-top: 43vh;
}
svg text {
	text-transform: uppercase;
	animation: stroke 5s infinite alternate;
	stroke-width: 2;
	stroke: #365fa0;
	font-size: 65px;
}
@keyframes stroke {
	0%   {
		fill: rgba(72,138,20,0); stroke: rgba(54,95,160,1);
		stroke-dashoffset: 25%; stroke-dasharray: 0 50%; stroke-width: 2;
	}
	70%  {fill: rgba(72,138,20,0); stroke: rgba(54,95,160,1); }
	80%  {fill: rgba(72,138,20,0); stroke: rgba(54,95,160,1); stroke-width: 3; }
	100% {
		fill: rgba(54,95,160,1); stroke: rgba(0, 35, 90, 0); 
		stroke-dashoffset: -25%; stroke-dasharray: 50% 0; stroke-width: 0;
	}
}

.container {
  display: flex;
  justify-content:center;
  align-content: center;
  flex-direction: column;
  height: 100vh;
  width: 100vw;
 
}

h1 {
  text-align: center;
  text-transform: uppercase;
  font-family: Helvetica, Arial, sans-serif;
  font-size: 10px;
  letter-spacing: 4px;
  color: azure;
  margin-top: -13vh;
}
h1 span {
  display: inline-block;
  animation: slideLeft 1.5s forwards;
  opacity: 0;
  transition-timing-function: cubic-bezier(0.075, 0.82, 0.165, 1);
}

@keyframes slideLeft {
  from {
    opacity: 0;
    transform: translateX(200px);
  } 
  to {
    opacity: 1;
    transform: translateX(0%);
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

</style>

<script>


var spanText = function spanText(text) {
  var string = text.innerText;
  var spaned = '';
  for (var i = 0; i < string.length; i++) {
    if(string.substring(i, i + 1) === ' ') spaned += string.substring(i, i + 1);
    else spaned += '<span>' + string.substring(i, i + 1) + '</span>';
  }
  text.innerHTML = spaned;
}

var headline = document.querySelector("h1");

spanText(headline);

let animations = document.querySelectorAll('.animation');

animations.forEach(animation => {
  let letters = animation.querySelectorAll('span');
  letters.forEach((letter, i) => {
    letter.style.animationDelay = (i * 0.1) + 's';
  })
})



</script>






</html>

