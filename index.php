<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
require_once 'config/config.php';
require_once 'helpers/Auth.php';

// Carrega o Dompdf manualmente (sem Composer)
// Certifique-se de que a pasta libs/dompdf existe com o autoload.inc.php dentro
require_once __DIR__ . '/dompdf/autoload.inc.php';

$url    = trim($_GET['url'] ?? 'login', '/');
$parts  = explode('/', $url);
$route  = $parts[0] ?? 'login';
$id     = isset($parts[1]) && is_numeric($parts[1]) ? (int) $parts[1] : 0;
$action = $parts[2] ?? '';
$method = $_SERVER['REQUEST_METHOD'];

/* ------------------------------------------------------------------ */
/* Helper de flash                                                    */
/* ------------------------------------------------------------------ */
function renderFlash(): void
{
    if (!empty($_SESSION['flash'])) {
        $f = $_SESSION['flash'];
        echo '<div class="alert alert-' . htmlspecialchars($f['tipo']) . ' alert-dismissible fade show" role="alert">'
           . htmlspecialchars($f['msg'])
           . '<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>';
        unset($_SESSION['flash']);
    }
}

/* ------------------------------------------------------------------ */
/* ROTEAMENTO                                                         */
/* ------------------------------------------------------------------ */
switch ($route) {

    /* AUTH */
    case 'login':
        require_once 'controllers/AuthController.php';
        $c = new AuthController();
        $method === 'POST' ? $c->doLogin() : $c->showLogin();
        break;

    /* CADASTRO */
    case 'cadastro':
        require_once 'controllers/AuthController.php';
        $c = new AuthController();
        $method === 'POST' ? $c->doCadastro() : $c->showCadastro();
        break;

    /* RECUPERAR SENHA */
    case 'recuperar-senha':
        require_once 'controllers/AuthController.php';
        $c = new AuthController();
        $method === 'POST' ? $c->processarRecuperacao() : $c->showRecuperarSenha();
        break;

    case 'logout':
        require_once 'controllers/AuthController.php';
        (new AuthController())->doLogout();
        break;

    /* DASHBOARD */
    case '':
    case 'dashboard':
        require_once 'controllers/DashboardController.php';
        (new DashboardController())->index();
        break;

    /* DEPARTAMENTOS */
    case 'departamentos':
        require_once 'controllers/DepartamentoController.php';
        $c = new DepartamentoController();
        if ($id && $action === 'editar') {
            $method === 'POST' ? $c->update($id) : $c->edit($id);
        } elseif ($id && $action === 'excluir') {
            $c->destroy($id);
        } elseif ($id) {
            $c->show($id);
        } elseif ($url === 'departamentos/criar') {
            $method === 'POST' ? $c->store() : $c->create();
        } else {
            $c->index();
        }
        break;

    /* CARGOS */
    case 'cargos':
        require_once 'controllers/CargoController.php';
        $c = new CargoController();
        if ($id && $action === 'editar') {
            $method === 'POST' ? $c->update($id) : $c->edit($id);
        } elseif ($id && $action === 'excluir') {
            $c->destroy($id);
        } elseif ($id) {
            $c->show($id);
        } elseif ($url === 'cargos/criar') {
            $method === 'POST' ? $c->store() : $c->create();
        } else {
            $c->index();
        }
        break;

    /* USUÁRIOS */
    case 'usuarios':
        require_once 'controllers/UsuarioController.php';
        $c = new UsuarioController();
        if ($id && $action === 'editar') {
            $method === 'POST' ? $c->update($id) : $c->edit($id);
        } elseif ($id && $action === 'excluir') {
            $c->destroy($id);
        } elseif ($id && $action === 'toggle') {
            $c->toggleAtivo($id);
        } elseif ($id) {
            $c->show($id);
        } elseif ($url === 'usuarios/criar') {
            $method === 'POST' ? $c->store() : $c->create();
        } else {
            $c->index();
        }
        break;

    /* PERFIL */
    case 'perfil':
        require_once 'controllers/UsuarioController.php';
        $c = new UsuarioController();
        $method === 'POST' ? $c->atualizarPerfil() : $c->perfil();
        break;

    /* PROJETOS */
    case 'projetos':
        require_once 'controllers/ProjetoController.php';
        $c = new ProjetoController();
        if ($id && $action === 'editar') {
            $method === 'POST' ? $c->update($id) : $c->edit($id);
        } elseif ($id && $action === 'excluir') {
            $c->destroy($id);
        } elseif ($id && $action === 'status') {
            $c->atualizarStatus($id);
        } elseif ($id) {
            $c->show($id);
        } elseif ($url === 'projetos/criar') {
            $method === 'POST' ? $c->store() : $c->create();
        } else {
            $c->index();
        }
        break;

    /* TAREFAS */
    case 'tarefas':
        require_once 'controllers/TarefaController.php';
        $c = new TarefaController();
        if ($id && $action === 'editar') {
            $method === 'POST' ? $c->update($id) : $c->edit($id);
        } elseif ($id && $action === 'excluir') {
            $c->destroy($id);
        } elseif ($id && $action === 'status') {
            $c->atualizarStatus($id);
        } elseif ($id) {
            $c->show($id);
        } elseif ($url === 'tarefas/criar') {
            $method === 'POST' ? $c->store() : $c->create();
        } else {
            $c->index();
        }
        break;

    /* COMENTÁRIOS */
    case 'comentarios':
        require_once 'controllers/ComentarioController.php';
        $c = new ComentarioController();
        if ($id && $action === 'novo') {
            $c->store($id);
        } elseif ($id && $action === 'editar') {
            $method === 'POST' ? $c->update($id) : $c->edit($id);
        } elseif ($id && $action === 'excluir') {
            $c->destroy($id);
        }
        break;

    /* RELATÓRIOS */
    case 'relatorios':
        require_once 'controllers/RelatorioController.php';
        $c = new RelatorioController();
        if ($url === 'relatorios/projetos') {
            $c->projetos();
        } elseif ($url === 'relatorios/tarefas') {
            $c->tarefas();
        } elseif ($url === 'relatorios/usuarios/pdf') { // ← ROTA DO PDF DE USUÁRIOS
            $c->usuariosPdf();
        } else {
            $c->index();
        }
        break;

    /* AGENDA */
    case 'agenda':
        require_once 'controllers/AgendaController.php';
        $c = new AgendaController();
        if ($url === 'agenda/criar') {
            $method === 'POST' ? $c->store() : $c->create();
        } else {
            $c->dashboard();
        }
        break;

    /* 404 */
    default:
        http_response_code(404);
        require_once 'views/layouts/header.php';
        echo '<div class="container mt-5"><div class="alert alert-warning"><h4>404 — Página não encontrada</h4><a href="'.BASE_URL.'/dashboard" class="btn btn-primary btn-sm mt-2">Voltar ao Dashboard</a></div></div>';
        require_once 'views/layouts/footer.php';
        break;
}