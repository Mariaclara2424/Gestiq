<?php
require_once ROOT_PATH . '/helpers/Auth.php';
require_once ROOT_PATH . '/views/layouts/header.php';

$usuario     = Auth::user();
$nomeUsuario = htmlspecialchars($usuario['nome'] ?? 'Usuário');
$perfilRaw   = $usuario['perfil'] ?? 'colaborador';
$perfilMap   = [
    'admin'       => ['label' => 'Administrador', 'class' => 'role-admin'],
    'gestor'      => ['label' => 'Gestor',        'class' => 'role-gestor'],
    'colaborador' => ['label' => 'Colaborador',   'class' => 'role-colaborador'],
];
$perfilInfo  = $perfilMap[$perfilRaw] ?? ['label' => ucfirst($perfilRaw), 'class' => 'role-colaborador'];
$partes      = explode(' ', $nomeUsuario);
$iniciais    = strtoupper(substr($partes[0], 0, 1) . (isset($partes[1]) ? substr($partes[1], 0, 1) : ''));
$paginaAtual = 'projetos';

$departamentos = $departamentos ?? [];
$usuarios      = $usuarios      ?? [];
$status_list   = $status_list   ?? ['planejamento', 'em_andamento', 'concluido', 'cancelado'];
$erros         = $erros         ?? [];
$dados         = $dados         ?? [
    'titulo' => '', 'descricao' => '', 'id_departamento' => '',
    'id_responsavel' => '', 'data_inicio' => '', 'data_fim' => '', 'status' => 'planejamento'
];

$statusLabels = [
    'planejamento' => 'Planejamento',
    'em_andamento' => 'Em andamento',
    'concluido'    => 'Concluído',
    'cancelado'    => 'Cancelado',
];
?>

<div class="dash-wrapper">

  <!-- ══════════ SIDEBAR ══════════ -->
  <aside class="sidebar" id="sidebar">
    <div class="sidebar-brand">
      <div class="sidebar-logo-icon"><i class="bi bi-layers-fill"></i></div>
      <span class="sidebar-name">GestIQ</span>
    </div>
    <nav class="sidebar-nav">
      <a href="<?= BASE_URL ?>/dashboard" class="nav-item <?= $paginaAtual === 'dashboard' ? 'active' : '' ?>">
        <i class="bi bi-grid-1x2-fill"></i><span>Painel Geral</span>
      </a>
      <?php if (Auth::can('admin', 'gestor')): ?>
      <a href="<?= BASE_URL ?>/departamentos" class="nav-item <?= $paginaAtual === 'departamentos' ? 'active' : '' ?>">
        <i class="bi bi-diagram-3-fill"></i><span>Departamentos</span>
      </a>
      <a href="<?= BASE_URL ?>/cargos" class="nav-item <?= $paginaAtual === 'cargos' ? 'active' : '' ?>">
        <i class="bi bi-person-badge-fill"></i><span>Cargos</span>
      </a>
      <?php endif; ?>
      <a href="<?= BASE_URL ?>/projetos" class="nav-item <?= $paginaAtual === 'projetos' ? 'active' : '' ?>">
        <i class="bi bi-folder2-open"></i><span>Projetos</span>
      </a>
      <a href="<?= BASE_URL ?>/tarefas" class="nav-item <?= $paginaAtual === 'tarefas' ? 'active' : '' ?>">
        <i class="bi bi-check2-square"></i><span>Tarefas</span>
      </a>
      <?php if (Auth::can('admin')): ?>
      <a href="<?= BASE_URL ?>/usuarios" class="nav-item <?= $paginaAtual === 'usuarios' ? 'active' : '' ?>">
        <i class="bi bi-people-fill"></i><span>Usuários</span>
      </a>
      <?php endif; ?>
      <?php if (Auth::can('admin', 'gestor')): ?>
      <a href="<?= BASE_URL ?>/relatorios" class="nav-item <?= $paginaAtual === 'relatorios' ? 'active' : '' ?>">
        <i class="bi bi-bar-chart-line-fill"></i><span>Relatórios</span>
      </a>
      <?php endif; ?>
    </nav>
    <div class="sidebar-footer">
      <div class="sidebar-user">
        <div class="user-avatar"><?= $iniciais ?></div>
        <div class="user-info">
          <span class="user-name"><?= $nomeUsuario ?></span>
          <span class="role-badge <?= $perfilInfo['class'] ?>"><?= $perfilInfo['label'] ?></span>
        </div>
      </div>
      <a href="<?= BASE_URL ?>/logout" class="sidebar-logout">
        <i class="bi bi-box-arrow-left"></i><span>Sair do Sistema</span>
      </a>
    </div>
  </aside>

  <!-- ══════════ CONTEÚDO PRINCIPAL ══════════ -->
  <main class="dash-main">

    <div class="dash-topbar" id="dashTopbar">
      <button class="topbar-toggle" id="sidebarToggle" aria-label="Abrir menu">
        <i class="bi bi-list"></i>
      </button>
      <span class="topbar-brand-mobile">GestIQ</span>
      <div style="flex:1"></div>
      <div class="topbar-avatar-mobile"><?= $iniciais ?></div>
    </div>

    <div class="page-header">
      <div>
        <h1 class="page-title">Novo Projeto</h1>
        <p class="page-subtitle">
          <a href="<?= BASE_URL ?>/projetos" style="color:var(--t-muted);text-decoration:none;">
            <i class="bi bi-arrow-left me-1"></i>Voltar para Projetos
          </a>
        </p>
      </div>
    </div>

    <?php if (!empty($erros)): ?>
    <div class="flash-alert flash-danger d-flex align-items-start gap-2 mb-4">
      <i class="bi bi-exclamation-triangle-fill flex-shrink-0 mt-1"></i>
      <ul class="mb-0 ps-3">
        <?php foreach ($erros as $e): ?>
          <li><?= htmlspecialchars($e) ?></li>
        <?php endforeach; ?>
      </ul>
    </div>
    <?php endif; ?>

    <form method="POST" action="<?= BASE_URL ?>/projetos/criar" id="formProjeto" novalidate>

      <!-- Card principal -->
      <div style="background:var(--bg-card);border:1px solid rgba(124,58,237,.15);border-radius:var(--radius);padding:2rem;max-width:820px;margin-bottom:1.25rem;">

        <p style="color:var(--t-secondary);font-size:.75rem;font-weight:700;text-transform:uppercase;letter-spacing:.08em;margin-bottom:1.25rem;padding-bottom:.75rem;border-bottom:1px solid rgba(255,255,255,.06);">
          <i class="bi bi-folder2-open me-2" style="color:var(--c-purple);"></i>Informações do Projeto
        </p>

        <!-- Título -->
        <div class="mb-4">
          <label for="titulo" style="display:block;color:var(--t-secondary);font-size:.8rem;font-weight:600;text-transform:uppercase;letter-spacing:.06em;margin-bottom:.5rem;">
            Título do Projeto <span style="color:var(--c-danger);">*</span>
          </label>
          <input type="text" id="titulo" name="titulo"
            placeholder="Ex: Redesign do Portal Corporativo"
            value="<?= htmlspecialchars($dados['titulo']) ?>"
            maxlength="200" required
            style="width:100%;background:var(--bg-input);border:1px solid rgba(124,58,237,.25);border-radius:var(--radius-sm);color:var(--t-primary);padding:10px 14px;font-size:.9rem;outline:none;transition:border-color .2s;"
            onfocus="this.style.borderColor='var(--c-purple)'"
            onblur="this.style.borderColor='rgba(124,58,237,.25)'"
          >
        </div>

        <!-- Departamento + Responsável -->
        <div style="display:grid;grid-template-columns:1fr 1fr;gap:1.25rem;margin-bottom:1.25rem;">

          <div>
            <label for="id_departamento" style="display:block;color:var(--t-secondary);font-size:.8rem;font-weight:600;text-transform:uppercase;letter-spacing:.06em;margin-bottom:.5rem;">
              Departamento Vinculado
            </label>
            <select id="id_departamento" name="id_departamento"
              style="width:100%;background:var(--bg-input);border:1px solid rgba(124,58,237,.25);border-radius:var(--radius-sm);color:var(--t-primary);padding:10px 14px;font-size:.9rem;outline:none;appearance:none;cursor:pointer;transition:border-color .2s;"
              onfocus="this.style.borderColor='var(--c-purple)'"
              onblur="this.style.borderColor='rgba(124,58,237,.25)'"
            >
              <option value="" style="background:var(--bg-card);">— Nenhum —</option>
              <?php foreach ($departamentos as $d): ?>
                <option value="<?= $d['id_departamento'] ?>" style="background:var(--bg-card);"
                  <?= (string)$dados['id_departamento'] === (string)$d['id_departamento'] ? 'selected' : '' ?>>
                  <?= htmlspecialchars($d['nome']) ?>
                </option>
              <?php endforeach; ?>
            </select>
          </div>

          <div>
            <label for="id_responsavel" style="display:block;color:var(--t-secondary);font-size:.8rem;font-weight:600;text-transform:uppercase;letter-spacing:.06em;margin-bottom:.5rem;">
              Responsável pelo Projeto <span style="color:var(--c-danger);">*</span>
            </label>
            <select id="id_responsavel" name="id_responsavel" required
              style="width:100%;background:var(--bg-input);border:1px solid rgba(124,58,237,.25);border-radius:var(--radius-sm);color:var(--t-primary);padding:10px 14px;font-size:.9rem;outline:none;appearance:none;cursor:pointer;transition:border-color .2s;"
              onfocus="this.style.borderColor='var(--c-purple)'"
              onblur="this.style.borderColor='rgba(124,58,237,.25)'"
            >
              <option value="" style="background:var(--bg-card);">— Selecione um responsável —</option>
              <?php foreach ($usuarios as $u): ?>
                <option value="<?= $u['id_usuario'] ?>" style="background:var(--bg-card);"
                  <?= (string)$dados['id_responsavel'] === (string)$u['id_usuario'] ? 'selected' : '' ?>>
                  <?= htmlspecialchars($u['nome']) ?>
                </option>
              <?php endforeach; ?>
            </select>
          </div>

        </div>

        <!-- Datas + Status -->
        <div style="display:grid;grid-template-columns:1fr 1fr 1fr;gap:1.25rem;margin-bottom:1.25rem;">

          <div>
            <label for="data_inicio" style="display:block;color:var(--t-secondary);font-size:.8rem;font-weight:600;text-transform:uppercase;letter-spacing:.06em;margin-bottom:.5rem;">
              Data de Início
            </label>
            <input type="date" id="data_inicio" name="data_inicio"
              value="<?= htmlspecialchars($dados['data_inicio']) ?>"
              style="width:100%;background:var(--bg-input);border:1px solid rgba(124,58,237,.25);border-radius:var(--radius-sm);color:var(--t-primary);padding:10px 14px;font-size:.9rem;outline:none;transition:border-color .2s;color-scheme:dark;"
              onfocus="this.style.borderColor='var(--c-purple)'"
              onblur="this.style.borderColor='rgba(124,58,237,.25)'"
            >
          </div>

          <div>
            <label for="data_fim" style="display:block;color:var(--t-secondary);font-size:.8rem;font-weight:600;text-transform:uppercase;letter-spacing:.06em;margin-bottom:.5rem;">
              Previsão de Conclusão
            </label>
            <input type="date" id="data_fim" name="data_fim"
              value="<?= htmlspecialchars($dados['data_fim']) ?>"
              style="width:100%;background:var(--bg-input);border:1px solid rgba(124,58,237,.25);border-radius:var(--radius-sm);color:var(--t-primary);padding:10px 14px;font-size:.9rem;outline:none;transition:border-color .2s;color-scheme:dark;"
              onfocus="this.style.borderColor='var(--c-purple)'"
              onblur="this.style.borderColor='rgba(124,58,237,.25)'"
            >
          </div>

          <div>
            <label for="status" style="display:block;color:var(--t-secondary);font-size:.8rem;font-weight:600;text-transform:uppercase;letter-spacing:.06em;margin-bottom:.5rem;">
              Status Inicial
            </label>
            <select id="status" name="status"
              style="width:100%;background:var(--bg-input);border:1px solid rgba(124,58,237,.25);border-radius:var(--radius-sm);color:var(--t-primary);padding:10px 14px;font-size:.9rem;outline:none;appearance:none;cursor:pointer;transition:border-color .2s;"
              onfocus="this.style.borderColor='var(--c-purple)'"
              onblur="this.style.borderColor='rgba(124,58,237,.25)'"
            >
              <?php foreach ($status_list as $s): ?>
                <option value="<?= $s ?>" style="background:var(--bg-card);"
                  <?= $dados['status'] === $s ? 'selected' : '' ?>>
                  <?= $statusLabels[$s] ?? ucfirst($s) ?>
                </option>
              <?php endforeach; ?>
            </select>
          </div>

        </div>

        <!-- Descrição -->
        <div>
          <label for="descricao" style="display:block;color:var(--t-secondary);font-size:.8rem;font-weight:600;text-transform:uppercase;letter-spacing:.06em;margin-bottom:.5rem;">
            Descrição / Objetivos
          </label>
          <textarea id="descricao" name="descricao" rows="5"
            placeholder="Descreva os objetivos, escopo e principais entregas deste projeto..."
            style="width:100%;background:var(--bg-input);border:1px solid rgba(124,58,237,.25);border-radius:var(--radius-sm);color:var(--t-primary);padding:10px 14px;font-size:.9rem;outline:none;resize:vertical;transition:border-color .2s;"
            onfocus="this.style.borderColor='var(--c-purple)'"
            onblur="this.style.borderColor='rgba(124,58,237,.25)'"
          ><?= htmlspecialchars($dados['descricao']) ?></textarea>
        </div>

      </div>

      <!-- Botões -->
      <div class="d-flex gap-3 align-items-center" style="max-width:820px;">
        <button type="submit" class="btn-primary-action">
          <i class="bi bi-floppy-fill"></i> Salvar Projeto
        </button>
        <a href="<?= BASE_URL ?>/projetos"
          style="display:inline-flex;align-items:center;padding:9px 20px;border-radius:50px;border:1px solid rgba(255,255,255,.12);color:var(--t-secondary);font-size:.85rem;font-weight:600;text-decoration:none;transition:background .2s,color .2s;"
          onmouseover="this.style.background='var(--bg-hover)';this.style.color='var(--t-primary)'"
          onmouseout="this.style.background='transparent';this.style.color='var(--t-secondary)'"
        >
          Cancelar
        </a>
        <span id="msgErroJs" style="color:var(--c-danger);font-size:.85rem;display:none;">
          <i class="bi bi-exclamation-circle me-1"></i><span id="msgErroJsTexto"></span>
        </span>
      </div>

    </form>

  </main>
</div>

<script>
document.getElementById('formProjeto').addEventListener('submit', function(e) {
    const titulo       = document.getElementById('titulo').value.trim();
    const responsavel  = document.getElementById('id_responsavel').value;
    const msgErro      = document.getElementById('msgErroJs');
    const msgErroTexto = document.getElementById('msgErroJsTexto');

    // Oculta erro anterior
    msgErro.style.display = 'none';

    if (!titulo) {
        e.preventDefault();
        highlight('titulo', 'O título do projeto é obrigatório.');
        return;
    }

    if (!responsavel) {
        e.preventDefault();
        highlight('id_responsavel', 'Selecione um responsável para o projeto.');
        return;
    }

    // Log dos dados (para debug)
    const dados = {
        titulo:          titulo,
        id_departamento: document.getElementById('id_departamento').value,
        id_responsavel:  responsavel,
        data_inicio:     document.getElementById('data_inicio').value,
        data_fim:        document.getElementById('data_fim').value,
        status:          document.getElementById('status').value,
        descricao:       document.getElementById('descricao').value.trim(),
    };
    console.log('[GestIQ] Dados do formulário:', dados);
});

function highlight(fieldId, mensagem) {
    const field = document.getElementById(fieldId);
    field.style.borderColor = 'var(--c-danger)';
    field.focus();

    const msgErro      = document.getElementById('msgErroJs');
    const msgErroTexto = document.getElementById('msgErroJsTexto');
    msgErroTexto.textContent = mensagem;
    msgErro.style.display = 'inline-flex';

    field.addEventListener('input', function() {
        field.style.borderColor = 'rgba(124,58,237,.25)';
        msgErro.style.display = 'none';
    }, { once: true });
}
</script>

<?php require_once ROOT_PATH . '/views/layouts/footer.php'; ?>