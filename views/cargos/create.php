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
$paginaAtual = 'cargos';

// Garante que $departamentos e $dados existam mesmo se o controller não passar
$departamentos = $departamentos ?? [];
$dados         = $dados ?? [];
$erros         = $erros ?? [];
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

    <!-- Topbar mobile -->
    <div class="dash-topbar" id="dashTopbar">
      <button class="topbar-toggle" id="sidebarToggle" aria-label="Abrir menu">
        <i class="bi bi-list"></i>
      </button>
      <span class="topbar-brand-mobile">GestIQ</span>
      <div style="flex:1"></div>
      <div class="topbar-avatar-mobile"><?= $iniciais ?></div>
    </div>

    <!-- Cabeçalho -->
    <div class="page-header">
      <div>
        <h1 class="page-title">Novo Cargo</h1>
        <p class="page-subtitle">
          <a href="<?= BASE_URL ?>/cargos" style="color: var(--t-muted); text-decoration: none;">
            <i class="bi bi-arrow-left me-1"></i>Voltar para Cargos
          </a>
        </p>
      </div>
    </div>

    <!-- Alertas de erro de validação -->
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

    <!-- Card do formulário -->
    <div style="
      background: var(--bg-card);
      border: 1px solid rgba(124,58,237,.15);
      border-radius: var(--radius);
      padding: 2rem;
      max-width: 600px;
    ">
      <form method="POST" action="<?= BASE_URL ?>/cargos/criar">

        <!-- Nome -->
        <div class="mb-4">
          <label for="nome" style="display:block; color: var(--t-secondary); font-size: 0.8rem; font-weight: 600; text-transform: uppercase; letter-spacing: .06em; margin-bottom: .5rem;">
            Nome do Cargo <span style="color: var(--c-danger);">*</span>
          </label>
          <input
            type="text"
            id="nome"
            name="nome"
            placeholder="Ex: Desenvolvedor PHP"
            value="<?= htmlspecialchars($dados['nome'] ?? $_POST['nome'] ?? '') ?>"
            maxlength="100"
            required
            autofocus
            style="
              width: 100%;
              background: var(--bg-input);
              border: 1px solid rgba(124,58,237,.25);
              border-radius: var(--radius-sm);
              color: var(--t-primary);
              padding: 10px 14px;
              font-size: .9rem;
              outline: none;
              transition: border-color .2s;
            "
            onfocus="this.style.borderColor='var(--c-purple)'"
            onblur="this.style.borderColor='rgba(124,58,237,.25)'"
          >
          <p style="color: var(--t-muted); font-size: 0.78rem; margin-top: .35rem;">Máximo de 100 caracteres.</p>
        </div>

        <!-- Departamento -->
        <div class="mb-4">
          <label for="id_departamento" style="display:block; color: var(--t-secondary); font-size: 0.8rem; font-weight: 600; text-transform: uppercase; letter-spacing: .06em; margin-bottom: .5rem;">
            Departamento
          </label>
          <select
            id="id_departamento"
            name="id_departamento"
            style="
              width: 100%;
              background: var(--bg-input);
              border: 1px solid rgba(124,58,237,.25);
              border-radius: var(--radius-sm);
              color: var(--t-primary);
              padding: 10px 14px;
              font-size: .9rem;
              outline: none;
              transition: border-color .2s;
              appearance: none;
              cursor: pointer;
            "
            onfocus="this.style.borderColor='var(--c-purple)'"
            onblur="this.style.borderColor='rgba(124,58,237,.25)'"
          >
            <option value="" style="background: var(--bg-card);">— Nenhum departamento —</option>
            <?php foreach ($departamentos as $depto): ?>
              <option
                value="<?= $depto['id_departamento'] ?>"
                style="background: var(--bg-card);"
                <?= (string)($dados['id_departamento'] ?? $_POST['id_departamento'] ?? '') === (string)$depto['id_departamento'] ? 'selected' : '' ?>
              >
                <?= htmlspecialchars($depto['nome']) ?>
              </option>
            <?php endforeach; ?>
          </select>
          <p style="color: var(--t-muted); font-size: 0.78rem; margin-top: .35rem;">Opcional. Vincula este cargo a um departamento.</p>
        </div>

        <!-- Descrição -->
        <div class="mb-4">
          <label for="descricao" style="display:block; color: var(--t-secondary); font-size: 0.8rem; font-weight: 600; text-transform: uppercase; letter-spacing: .06em; margin-bottom: .5rem;">
            Descrição
          </label>
          <textarea
            id="descricao"
            name="descricao"
            rows="4"
            placeholder="Descreva as responsabilidades deste cargo..."
            style="
              width: 100%;
              background: var(--bg-input);
              border: 1px solid rgba(124,58,237,.25);
              border-radius: var(--radius-sm);
              color: var(--t-primary);
              padding: 10px 14px;
              font-size: .9rem;
              outline: none;
              resize: vertical;
              transition: border-color .2s;
            "
            onfocus="this.style.borderColor='var(--c-purple)'"
            onblur="this.style.borderColor='rgba(124,58,237,.25)'"
          ><?= htmlspecialchars($dados['descricao'] ?? $_POST['descricao'] ?? '') ?></textarea>
        </div>

        <!-- Botões -->
        <div class="d-flex gap-3 pt-1">
          <button type="submit" class="btn-primary-action">
            <i class="bi bi-check-lg"></i> Criar Cargo
          </button>
          <a href="<?= BASE_URL ?>/cargos"
            style="
              display: inline-flex;
              align-items: center;
              padding: 9px 20px;
              border-radius: 50px;
              border: 1px solid rgba(255,255,255,.12);
              color: var(--t-secondary);
              font-size: .85rem;
              font-weight: 600;
              text-decoration: none;
              transition: background .2s, color .2s;
            "
            onmouseover="this.style.background='var(--bg-hover)'; this.style.color='var(--t-primary)'"
            onmouseout="this.style.background='transparent'; this.style.color='var(--t-secondary)'"
          >
            Cancelar
          </a>
        </div>

      </form>
    </div>

  </main>
</div>

<?php require_once ROOT_PATH . '/views/layouts/footer.php'; ?>