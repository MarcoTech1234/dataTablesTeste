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
    <title>chat</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css" >
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css" >
    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.20/dist/sweetalert2.css" rel="stylesheet">

</head>
<body>
    
    <h2>Formulario de Acesso ao Chat</h2>

    <?php
    // Receber os dados do formulario
    $dados = filter_input_array(INPUT_POST, FILTER_DEFAULT);
    //var_dump($dados);
    // Acessa o IF quando o usuario clicar no botão acessar do formulário
    if(!empty($dados['acessar'])){
        //var_dump($dados);

        // Criar a sessão e atribuir nome do usuario
        $_SESSION['usuario'] = $dados['usuario'];

        // Redirecionar usuario ao chat
        header("Location: chat.php");
    }
    ?>
    <!-- Inicio do formulário para o usuário acessar o chat -->
    <form method="POST" action="">
        <label>Nome: </label>
        <input type="text" name="usuario" placeholder="Digite o nome"><br><br>

        <input type="submit" name="acessar" value="Acessar"><br><br>
    </form>

    <!--Começo do dataTables dos Pc -->
    <div class="row mt-5">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <div class="row">
                    <div class="col-12 col-sm-10">
                        <h4 class="text-primary">
                            Gerenciamento de clientes
                        </h4>
                    </div>
                    <div class="col-12 col-sm-2">
                        <button class="btn btn-primary btn-new btn-block">
                            Novo registro
                        </button>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table id="table-cliente" class="table table-hover">
                            <thead>
                            <tr>
                                <th class="text-center">Nome</th>
                                <th class="text-center">Status</th>
                                <th class="text-center">ID</th>
                                <th class="text-center">Ações</th>
                            </tr>
                            </thead>
                    </table>
                </div>
            </div>
            <div class="card-footer">
                <div class="row">
                    <div class="col-12 border-top">
                        <p class="text-center">
                            Todos os direitos reservados <br>
                            Desenvolvido por: Marco Antonio
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div id="modal-cliente" class="modal fade" tabindex="-1" data-backdrop="static" role="dialog" aria-labelledby="my-modal-title" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="my-modal-title">Title</h5>
                <button class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="form-cliente" action="" method="post">
                <div class="form-group">
                    <label>Nome</label>
                    <input id="NOME" class="form-control" type="text" name="NOME">
                </div>
                <input type="hidden" name="ID" id="ID">
                </form>  
            </div>
            <div class="modal-footer">
                <div class="row justify-content-end">
                    <div class="col-6 co-sm-2">
                        <button data-dismiss="modal" class="btn btn-secondary">Fechar</button>
                    </div>
                    <div class="col-6 co-sm-2">
                        <button data-dismiss="modal" class="btn btn-info btn-save">Salvar</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script src="https://code.jquery.com/jquery-3.5.1.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.3/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/js/bootstrap.min.js"></script>
<script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.20/dist/sweetalert2.all.js"></script>
<script src="api/ClienteController.js"></script>
<!-- Fim do formulário para o usuário acessar o chat -->
</body>
</html>