<?php

session_start(); // Iniciar a sessão

ob_start(); // Limpar o buffer de saida para evitar erro de redirecionamento
?>
<!DOCTYPE html>
<html lang="pt-br">

<head>
<meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Celke - WebSocket</title>
    <link rel="stylesheet" href="api/css/custom.css">
    <link href="//cdn.jsdelivr.net/npm/@sweetalert2/dist/sweetalert2.all.css" rel="stylesheet">
</head>

<body>
    <div class="container">
        <div class="conteudo-chat">
            <div class="header-chat">
                <div class="usuario-dados">
                        <h2>Chat</h2>
                        <!-- Imprimir o nome do usuário que está na sessão -->
                        <p>Bem-vindo: <span id="nome-usuario" class="nome-usuario"></span><?php echo $_SESSION['usuario']; ?></span></p>
                </div>
            </div>
            <div class="chat-box" id="chatBox">
                <span id="mensagem-chat"></span>
            </div>
            <!-- Campo para o usuário digitar a nova mensagem -->
            <form class="enviar-msg">
                <!-- <label>Nova mensagem: </label> -->
                <input type="text" name="mensagem" id="mensagem" class="campo-msg" placeholder="Digite a mensagem...">
                <input type="number" name="id-pc" id="id-pc" class="campo-msg" placeholder="Digite o number do Pc">
                <input type="text" name="status" id="status" class="campo-msg" placeholder="Digite o status do PC"><br><br>
                <input type="button" class="btn-enviar-msg" onclick="enviar()" value="Enviar"><br><br>
            </form>
        </div>
    </div>
    <script src="//cdn.jsdelivr.net/npm/sweetalert2@10/dist/sweetalert2.all.js"></script>
    <script src="https://code.jquery.com/jquery-3.5.1.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/js/bootstrap.min.js"></script>
    <script src="api/js/custom.js"></script>

</body>
</html>