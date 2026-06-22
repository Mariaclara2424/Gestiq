<?php
require_once ROOT_PATH . '/helpers/Auth.php';
require_once ROOT_PATH . '/models/Tarefa.php';
require_once ROOT_PATH . '/AgendaService.php'; 

class AgendaController
{
    private Tarefa $modelTarefa;
    private AgendaService $agendaService;
    private int $usuario_id = 1;

    public function __construct()
    {
        Auth::check();
        $this->modelTarefa   = new Tarefa();
        $this->agendaService = new AgendaService();
    }

    private function getProjetos(): array {
        try {
            $pdo = new PDO("mysql:host=localhost;dbname=gestiq;charset=utf8", "root", "");
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $stmt = $pdo->query("SELECT id_projeto, titulo AS nome FROM projetos ORDER BY titulo ASC");
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) { return []; }
    }

    // NOVA FUNÇÃO: Busca os usuários para o convite
    private function getUsuarios(): array {
        try {
            $pdo = new PDO("mysql:host=localhost;dbname=gestiq;charset=utf8", "root", "");
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            // Busca o nome e email na tabela usuarios
            $stmt = $pdo->query("SELECT nome, email FROM usuarios WHERE email IS NOT NULL AND email != '' ORDER BY nome ASC");
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) { return []; }
    }

    public function dashboard(): void {
        $filtros = ['_usuario_id' => $this->usuario_id, '_perfil' => Auth::user()['perfil']];
        $tarefas = $this->modelTarefa->listar($filtros);
        $eventos = [];
        $erroCalendario = null;
        try {
            $agora = (new DateTime())->format(DateTime::ATOM);
            $limite = (new DateTime('+30 days'))->format(DateTime::ATOM);
            $eventos = $this->agendaService->listarEventos($this->usuario_id, $agora, $limite);
        } catch (Exception $e) { $erroCalendario = $e->getMessage(); }
        require ROOT_PATH . '/views/agenda/dashboard.php';
    }

    public function create(): void {
        $erros = [];
        $dados = ['titulo' => '', 'descricao' => '', 'data_inicio' => '', 'data_fim' => '', 'id_projeto' => ''];
        $projetos = $this->getProjetos();
        $usuarios = $this->getUsuarios(); // Envia os usuários para a View
        require ROOT_PATH . '/views/agenda/criar.php';
    }

    public function store(): void {
        $dados = [
            'titulo'      => trim($_POST['titulo'] ?? ''),
            'descricao'   => trim($_POST['descricao'] ?? ''),
            'data_inicio' => trim($_POST['data_inicio'] ?? ''),
            'data_fim'    => trim($_POST['data_fim'] ?? ''),
            'id_projeto'  => $_POST['id_projeto'] ?? null, 
        ];

        // Recebe os e-mails selecionados (se nenhum for marcado, fica um array vazio)
        $emailsSelecionados = $_POST['participantes'] ?? [];

        $erros = [];
        if ($dados['titulo'] === '') $erros[] = 'O título é obrigatório.';
        if ($dados['data_inicio'] === '') $erros[] = 'A data/hora de início é obrigatória.';
        if ($dados['data_fim'] === '') $erros[] = 'A data/hora de término é obrigatória.';
        if (empty($dados['id_projeto'])) $erros[] = 'O projeto é obrigatório. Por favor, selecione um projeto.';
        if ($dados['data_inicio'] !== '' && $dados['data_fim'] !== '' && $dados['data_fim'] <= $dados['data_inicio']) {
            $erros[] = 'O término deve ser posterior ao início.';
        }

        if (!empty($erros)) {
            $projetos = $this->getProjetos(); 
            $usuarios = $this->getUsuarios();
            require ROOT_PATH . '/views/agenda/criar.php';
            return;
        }

        $tz = new DateTimeZone('America/Sao_Paulo');
        $inicioISO = (new DateTime($dados['data_inicio'], $tz))->format(DateTime::ATOM);   
        $fimISO = (new DateTime($dados['data_fim'], $tz))->format(DateTime::ATOM);

        $dadosTarefa = [
            'titulo'          => $dados['titulo'],
            'descricao'       => $dados['descricao'], // <--- A VÍRGULA QUE FALTAVA ESTÁ AQUI
            'id_projeto'      => $dados['id_projeto'],
            'id_responsavel'  => $this->usuario_id,
            'data_vencimento' => (new DateTime($dados['data_fim'], $tz))->format('Y-m-d'),     
            'status'          => 'pendente',
            'prioridade'      => 'media',
        ];

        $idTarefa = $this->modelTarefa->criar($dadosTarefa);
        if (!$idTarefa) {
            $_SESSION['flash'] = ['tipo' => 'danger', 'msg'  => 'Erro ao salvar a tarefa no banco de dados.'];
            header('Location: ' . BASE_URL . '/agenda');
            exit;
        }

        try {
            // Agora enviamos o array de e-mails lá pro Google!
            $this->agendaService->criarEvento(
                $this->usuario_id, $dados['titulo'], $dados['descricao'], $inicioISO, $fimISO, $emailsSelecionados
            );
            $_SESSION['flash'] = ['tipo' => 'success', 'msg' => 'Reunião agendada e convites enviados por e-mail com sucesso!'];
        } catch (Exception $e) {
            $_SESSION['flash'] = ['tipo' => 'warning', 'msg' => 'Salvo, mas erro no Google: ' . htmlspecialchars($e->getMessage())];
        }
        header('Location: ' . BASE_URL . '/agenda');
        exit;
    }
}