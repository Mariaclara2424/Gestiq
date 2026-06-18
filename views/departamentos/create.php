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
$paginaAtual = 'departamentos';
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
        <h1 class="page-title">Novo Departamento</h1>
        <p class="page-subtitle">
          <a href="<?= BASE_URL ?>/departamentos" style="color: var(--t-muted); text-decoration: none;">
            <i class="bi bi-arrow-left me-1"></i>Voltar para Departamentos
          </a>
        </p>
      </div>
    </div>

    <!-- Alerta de erro de validação -->
    <?php if (!empty($erro)): ?>
    <div class="flash-alert flash-danger d-flex align-items-center gap-2 mb-4">
      <i class="bi bi-exclamation-triangle-fill flex-shrink-0"></i>
      <div><?= $erro ?></div>
    </div>
    <?php endif; ?>

    <!-- Card formulário -->
    <div style="
      background: var(--bg-card);
      border: 1px solid rgba(124,58,237,.15);
      border-radius: var(--radius);
      padding: 2rem;
      max-width: 600px;
    ">
      <form method="POST" action="<?= BASE_URL ?>/departamentos/criar">

        <div class="mb-4">
          <label for="nome" style="display:block; color: var(--t-secondary); font-size: 0.8rem; font-weight: 600; text-transform: uppercase; letter-spacing: .06em; margin-bottom: .5rem;">
            Nome do Departamento <span style="color: var(--c-danger);">*</span>
          </label>
          <input
            type="text"
            id="nome"
            name="nome"
            placeholder="Ex: Tecnologia da Informação"
            value="<?= htmlspecialchars($_POST['nome'] ?? '') ?>"
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
          <p style="color: var(--t-muted); font-size: 0.78rem; margin-top: .35rem;">Máximo de 100 caracteres. O nome deve ser único.</p>
        </div>

        <div class="mb-4">
          <label for="descricao" style="display:block; color: var(--t-secondary); font-size: 0.8rem; font-weight: 600; text-transform: uppercase; letter-spacing: .06em; margin-bottom: .5rem;">
            Descrição
          </label>
          <textarea
            id="descricao"
            name="descricao"
            rows="4"
            placeholder="Descreva brevemente as responsabilidades deste departamento..."
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
          ><?= htmlspecialchars($_POST['descricao'] ?? '') ?></textarea>
        </div>

        <div class="d-flex gap-3 pt-1">
          <button type="submit" class="btn-primary-action">
            <i class="bi bi-check-lg"></i> Criar Departamento
          </button>
          <a href="<?= BASE_URL ?>/departamentos" style="
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