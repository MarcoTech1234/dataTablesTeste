/************ ENVIAR E RECEBER MENSAGENS ***********/

// Recuperar o id que deve receber as mensagem do chat
const mensagemChat = document.getElementById('mensagem-chat');

// Endereço do websocket
const ws = new WebSocket('ws://localhost:8080');

// Realizar a conexão com websocket
ws.onopen = (e) => {
    //console.log('Conectado!');
}

// Receber a mensagem do WebSocket
ws.onmessage = (mensagemRecebida) => {

    // Ler as mensagem enviada pelo WebSocket
    let resultado = JSON.parse(mensagemRecebida.data);

    // Enviar a mensagem para o HTML, inserir no final da lista de mensagens
    mensagemChat.insertAdjacentHTML('beforeend', `<div class="msg-recebida">
            <div class="det-msg-recebida">
                <p class="texto-msg-recebida">${resultado.nome}: ${resultado.mensagem}</p>
            </div>
        </div>`);

    // Role para o final após adicionar as mensagens
    scrollBottom();
}

// Função para enviar a mensagem
const enviar = () => {

    // Recuperar o id do campo mensagem
    let mensagem = document.getElementById("mensagem");
    // Recuperar o id do campo do Pc
    let Pc = document.getElementById('id-pc');
    // Recuperar o id do campo do Pc
    let Status = document.getElementById('status');

    var nomeUsuario = document.getElementById('nome-usuario').textContent;

    // Verificar se existe o id do usuário
    // if (usuarioId === "") {
    //     alert("Erro: Necessário realizar o login para acessar a página!");
    //     window.location.href = "index.php";
    //     return;
    // }

    // Criar o array de dados para enviar para websocket
    let dados = {
        mensagem: `${mensagem.value}`,
        id_CH_equipamento: `${Pc.value}`,
        Status: `${Status.value}`
    }

    $.ajax({
        dataType: 'JSON',
        type: 'POST',
        assync: true,
        url: 'api/src/Datatables.php?operacao=verificar',
        data: dados,
        success: function(dados){
            console.log(dados)
            if(dados.type == "success"){
                // Enviar a mensagem para websocket
                ws.send(JSON.stringify(dados));
                
                // Enviar a mensagem para o HTML, inserir no final da lista de mensagens
                mensagemChat.insertAdjacentHTML('beforeend', `<div class="msg-enviada">
                <div class="det-msg-enviada">
                <p class="texto-msg-enviada">${nomeUsuario}: ${mensagem.value}</p>
                </div>
                </div>`);
                
                // Limpar o campo mensagem
                mensagem.value = '';
                Pc.value = '';
                Status.value = '';
                
                // Role para o final após adicionar as mensagens
                scrollBottom();
            }
            else {
                console.log("codigo legal meu parça");
                console.log(dados)
                if(dados.type == 'error'){

                    Swal.fire ({
                        icon: dados.type,
                        title: 'SysPed',
                        text: dados.mensagem
                    })
                }
            }
        }
    })
}

// Role para o final após adicionar as mensagens
function scrollBottom() {
    var chatBox = document.getElementById("chatBox");
    chatBox.scrollTop = chatBox.scrollHeight;
}

function myFunction(event) {
    if (event.keyCode === 13) {
        enviar();
    }
}

// Ouvindo um evento de keydown e chamando minha função de envio
document.addEventListener('keydown', myFunction);

/************ LISTAR AS MENSAGENS DO BANCO DE DADOS ***********/

// Quantidade de registros carregados
var offset = 0;

// Mover o scroll para o final
var roleFinal = true;

// Variável de controle para evitar carregamentos simultâneos
var carregandoRegistros = false;

// // Verifica se o usuário está a 10 pixels do topo
function verificarSroll() {
    var chatBox = document.getElementById("chatBox");

    // Verifica se o usuário está a 10 pixels do topo
    if (chatBox.scrollTop <= 10) {
        //console.log("Usuário está próximo ao topo.");

        // Carregar as mensagens
        carregarMsg();
    }

}

// Adicione o ouvinte de eventos de scroll
chatBox.addEventListener('scroll', verificarSroll);

// Carregar as mensagens do banco de dados
async function carregarMsg() {

    // Verificar se está carregando registro
    if(carregandoRegistros){
        // Se já estiver carregando registros, retorna para evitar carregamentos simultâneos
        return;
    }

    // Atualiza a variável de controle para indicar que está carregando registros
    carregandoRegistros = true;

    // Desativar temporariamente o evento de scroll durante o carregamento
    chatBox.onscroll = null;

    // Chamar o arquivo PHP responsável em recuperar usuários do banco de dados
    var dados = await fetch(`api/src/listar_registro.php?offset=${offset}`);

        // Ler os dados retornado do PHP
    var resposta = await dados.json();
    //console.log(resposta);

    // Acessa o IF quando o arquivo PHP retornar status TRUE
    if (resposta['status']) {

        // Somar a quantidade de registro recuperado
        offset += resposta['qtd_mensagens'];

        // Ler as mensagens e enviar para o chat
        resposta['dados'].map(item => {

            // Recuperar o id do usuário
            // var usuarioId = document.getElementById('usuario_id').value;
            // Enviar a mensagem para o HTML
            mensagemChat.insertAdjacentHTML('afterbegin', `<div class="msg-enviada">
                <div class="det-msg-enviada">
                    <p class="texto-msg-enviada"> ${item.mensagem}</p>
                </div>
            </div>`);

        });

        // Atualiza a variável de controle para indicar que não está mais carregando registros
        carregandoRegistros = false;

        // Reativar o evento de scroll após o carregamento
        chatBox.onscroll = verificarSroll;

        if(roleFinal){
            roleFinal = false;
            // Role para o final após adicionar as mensagens
            scrollBottom();
        }
    } else {

        // Enviar a mensagem para o HTML
        mensagemChat.insertAdjacentHTML('afterbegin', `<p style='color: #f00;'>${resposta['msg']}</p>`)
    }

}

// Carregar as mensagens inicialmente
carregarMsg();