<?php
require_once ROOT_PATH . '/views/layouts/header.php';

$kpiProjetos   = $kpiProjetos   ?? ['total' => 0];
$kpiTarefas    = $kpiTarefas    ?? ['total' => 0, 'atrasadas' => 0];
$taxaConclusao = $taxaConclusao ?? 0;

$usuario     = Auth::user();
$nomeUsuario = htmlspecialchars($usuario['nome'] ?? 'Usuário');
$emailUsuario = htmlspecialchars($usuario['email'] ?? '');
$perfilRaw   = $usuario['perfil'] ?? 'colaborador';

$perfilMap = [
  'admin'       => ['label' => 'Administrador', 'class' => 'role-admin'],
  'gestor'      => ['label' => 'Gestor',        'class' => 'role-gestor'],
  'colaborador' => ['label' => 'Colaborador',   'class' => 'role-colaborador'],
];
$perfilInfo = $perfilMap[$perfilRaw] ?? ['label' => ucfirst($perfilRaw), 'class' => 'role-colaborador'];

$partes   = explode(' ', $nomeUsuario);
$iniciais = strtoupper(substr($partes[0], 0, 1) . (isset($partes[1]) ? substr($partes[1], 0, 1) : ''));

$paginaAtual = 'dashboard';
?>

<div class="dash-wrapper">

  <!-- ══════════════════════════════════════
       SIDEBAR
  ══════════════════════════════════════ -->
  <aside class="sidebar" id="sidebar">

    <!-- Logo -->
    <div class="sidebar-brand">
      <div class="sidebar-logo-icon">
        <i class="bi bi-layers-fill"></i>
      </div>
      <span class="sidebar-name">GestIQ</span>
    </div>

    <!-- Navegação principal -->
    <nav class="sidebar-nav">

      <a href="<?= BASE_URL ?>/dashboard"
         class="nav-item <?= $paginaAtual === 'dashboard' ? 'active' : '' ?>">
        <i class="bi bi-grid-1x2-fill"></i>
        <span>Painel Geral</span>
      </a>

      <?php if (Auth::can('admin', 'gestor')): ?>
      <a href="<?= BASE_URL ?>/departamentos"
         class="nav-item <?= $paginaAtual === 'departamentos' ? 'active' : '' ?>">
        <i class="bi bi-diagram-3-fill"></i>
        <span>Departamentos</span>
      </a>
      <?php endif; ?>

      <?php if (Auth::can('admin', 'gestor')): ?>
      <a href="<?= BASE_URL ?>/cargos"
         class="nav-item <?= $paginaAtual === 'cargos' ? 'active' : '' ?>">
        <i class="bi bi-person-badge-fill"></i>
        <span>Cargos</span>
      </a>
      <?php endif; ?>

      <a href="<?= BASE_URL ?>/projetos"
         class="nav-item <?= $paginaAtual === 'projetos' ? 'active' : '' ?>">
        <i class="bi bi-folder2-open"></i>
        <span>Projetos</span>
      </a>

      <a href="<?= BASE_URL ?>/tarefas"
         class="nav-item <?= $paginaAtual === 'tarefas' ? 'active' : '' ?>">
        <i class="bi bi-check2-square"></i>
        <span>Tarefas</span>
      </a>

      <!-- AGENDA — link novo -->
      <a href="<?= BASE_URL ?>/agenda"
         class="nav-item <?= $paginaAtual === 'agenda' ? 'active' : '' ?>">
        <i class="bi bi-calendar2-week"></i>
        <span>Agenda</span>
      </a>

      <?php if (Auth::can('admin')): ?>
      <a href="<?= BASE_URL ?>/usuarios"
         class="nav-item <?= $paginaAtual === 'usuarios' ? 'active' : '' ?>">
        <i class="bi bi-people-fill"></i>
        <span>Usuários</span>
      </a>
      <?php endif; ?>

      <?php if (Auth::can('admin', 'gestor')): ?>
      <a href="<?= BASE_URL ?>/relatorios"
         class="nav-item <?= $paginaAtual === 'relatorios' ? 'active' : '' ?>">
        <i class="bi bi-bar-chart-line-fill"></i>
        <span>Relatórios</span>
      </a>
      <?php endif; ?>

    </nav>

    <!-- Rodapé: perfil + sair -->
    <div class="sidebar-footer">
      <div class="sidebar-user">
        <div class="user-avatar"><?= $iniciais ?></div>
        <div class="user-info">
          <span class="user-name"><?= $nomeUsuario ?></span>
          <span class="role-badge <?= $perfilInfo['class'] ?>"><?= $perfilInfo['label'] ?></span>
        </div>
      </div>
      <a href="<?= BASE_URL ?>/logout" class="sidebar-logout">
        <i class="bi bi-box-arrow-left"></i>
        <span>Sair do Sistema</span>
      </a>
    </div>

  </aside>
  <!-- /SIDEBAR -->

  <!-- ══════════════════════════════════════
       CONTEÚDO PRINCIPAL
  ══════════════════════════════════════ -->
  <main class="dash-main">

    <!-- Topbar mobile (visível só em telas pequenas via CSS) -->
    <div class="dash-topbar" id="dashTopbar">
      <button class="topbar-toggle" id="sidebarToggle" aria-label="Abrir menu">
        <i class="bi bi-list"></i>
      </button>
      <span class="topbar-brand-mobile">GestIQ</span>
      <div style="flex:1"></div>
      <div class="topbar-avatar-mobile"><?= $iniciais ?></div>
    </div>

    <!-- Cabeçalho da página -->
    <div class="page-header">
      <div>
        <h1 class="page-title">Painel Geral</h1>
        <p class="page-subtitle">Resumo de Projetos e Tarefas</p>
      </div>
    </div>

    <!-- KPI Cards -->
    <div class="kpi-grid">

      <div class="kpi-card kpi-purple">
        <div class="kpi-card-top" style="display:flex;align-items:center;justify-content:space-between;width:100%;">
          <span class="kpi-card-label">TOTAL DE PROJETOS</span>
          <div class="kpi-card-icon kpi-icon-purple" style="width:34px;height:34px;display:flex;align-items:center;justify-content:center;border-radius:8px;flex-shrink:0;">
            <i class="bi bi-folder2-open"></i>
          </div>
        </div>
        <div class="kpi-card-value"><?= (int)$kpiProjetos['total'] ?></div>
      </div>

      <div class="kpi-card kpi-emerald">
        <div class="kpi-card-top" style="display:flex;align-items:center;justify-content:space-between;width:100%;">
          <span class="kpi-card-label">CONCLUÍDAS</span>
          <div class="kpi-card-icon kpi-icon-emerald" style="width:34px;height:34px;display:flex;align-items:center;justify-content:center;border-radius:8px;flex-shrink:0;">
            <i class="bi bi-check-circle-fill"></i>
          </div>
        </div>
        <div class="kpi-card-value"><?= (int)($kpiTarefas['total'] - $kpiTarefas['atrasadas']) ?></div>
      </div>

      <div class="kpi-card kpi-amber">
        <div class="kpi-card-top" style="display:flex;align-items:center;justify-content:space-between;width:100%;">
          <span class="kpi-card-label">ATRASADAS</span>
          <div class="kpi-card-icon kpi-icon-amber" style="width:34px;height:34px;display:flex;align-items:center;justify-content:center;border-radius:8px;flex-shrink:0;">
            <i class="bi bi-exclamation-triangle-fill"></i>
          </div>
        </div>
        <div class="kpi-card-value"><?= (int)$kpiTarefas['atrasadas'] ?></div>
      </div>

      <div class="kpi-card kpi-magenta">
        <div class="kpi-card-top" style="display:flex;align-items:center;justify-content:space-between;width:100%;">
          <span class="kpi-card-label">CONCLUSÃO</span>
          <div class="kpi-card-icon kpi-icon-magenta" style="width:34px;height:34px;display:flex;align-items:center;justify-content:center;border-radius:8px;flex-shrink:0;">
            <i class="bi bi-graph-up-arrow"></i>
          </div>
        </div>
        <div class="kpi-card-value"><?= (float)$taxaConclusao ?>%</div>
      </div>

    </div>

    <!-- Gráficos -->
    <div class="charts-grid">
      <div class="chart-card">
        <div class="chart-card-header"><span>Projetos por Mês</span></div>
        <canvas id="chartMensal"></canvas>
      </div>
      <div class="chart-card">
        <div class="chart-card-header"><span>Por Departamento</span></div>
        <canvas id="chartDept"></canvas>
      </div>
    </div>

    <!-- Atividades recentes + Tarefas urgentes -->
    <div class="tables-grid">

      <div class="table-card">
        <div class="table-card-header">
          <i class="bi bi-lightning-charge-fill" style="color:#F59E0B"></i>
          <span>Atividades Recentes do Fluxo de Trabalho</span>
        </div>
        <div class="activity-table-head">
          <span>Atividade / Registro</span>
        </div>
        <?php
          $atividades = array_merge(
            array_map(fn($p) => ['tipo' => 'projeto', 'texto' => 'Projeto "' . $p['titulo'] . '" atualizado', 'data' => $p['data_fim'] ?? null], $projetosRecentes ?? []),
            array_map(fn($t) => ['tipo' => 'tarefa', 'texto' => 'Tarefa "' . $t['titulo'] . '" — prioridade ' . $t['prioridade'], 'data' => null], $tarefasUrgentes ?? [])
          );
        ?>
        <?php if (empty($atividades)): ?>
          <p class="empty-state">Nenhuma atividade recente.</p>
        <?php else: ?>
          <ul class="activity-list">
            <?php foreach ($atividades as $a): ?>
            <li class="activity-item">
              <span class="activity-dot activity-dot--<?= $a['tipo'] ?>"></span>
              <span class="activity-text"><?= htmlspecialchars($a['texto']) ?></span>
            </li>
            <?php endforeach; ?>
          </ul>
        <?php endif; ?>
      </div>

      <div class="table-card">
        <div class="table-card-header">
          <i class="bi bi-alarm-fill" style="color:#EF4444"></i>
          <span>Tarefas Urgentes</span>
        </div>
        <ul class="urgent-list">
          <?php foreach ($tarefasUrgentes ?? [] as $t): ?>
          <li class="urgent-item">
            <span class="urgency-badge urgency-<?= $t['prioridade'] ?>"><?= $t['prioridade'] ?></span>
            <?= htmlspecialchars($t['titulo']) ?>
          </li>
          <?php endforeach; ?>
          <?php if (empty($tarefasUrgentes)): ?>
            <p class="empty-state">Nenhuma tarefa urgente.</p>
          <?php endif; ?>
        </ul>
      </div>

    </div>

  </main>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.3/dist/chart.umd.min.js"></script>
<script>
document.addEventListener("DOMContentLoaded", function () {

  /* ── Gráfico de linha ── */
  new Chart(document.getElementById('chartMensal'), {
    type: 'line',
    data: {
      labels: <?= $chartMesLabels ?? '[]' ?>,
      datasets: [{
        label: 'Projetos',
        data: <?= $chartMesData ?? '[]' ?>,
        borderColor: '#7C3AED',
        backgroundColor: 'rgba(124,58,237,.08)',
        tension: 0.4,
        fill: true,
        pointBackgroundColor: '#7C3AED',
        pointRadius: 4
      }]
    },
    options: {
      responsive: true,
      maintainAspectRatio: false,
      plugins: { legend: { labels: { color: '#94a3b8', font: { size: 12 } } } },
      scales: {
        x: { ticks: { color: '#64748b' }, grid: { color: 'rgba(255,255,255,.04)' } },
        y: { ticks: { color: '#64748b' }, grid: { color: 'rgba(255,255,255,.04)' } }
      }
    }
  });

  /* ── Gráfico donut ── */
  new Chart(document.getElementById('chartDept'), {
    type: 'doughnut',
    data: {
      labels: <?= $chartDeptLabels ?? '[]' ?>,
      datasets: [{
        data: <?= $chartDeptData ?? '[]' ?>,
        backgroundColor: ['#7C3AED','#DB2777','#9333EA','#10B981','#F59E0B'],
        borderWidth: 0,
        hoverOffset: 6
      }]
    },
    options: {
      responsive: true,
      maintainAspectRatio: false,
      cutout: '68%',
      plugins: {
        legend: {
          position: 'bottom',
          labels: { color: '#94a3b8', font: { size: 12 }, padding: 16, boxWidth: 12 }
        }
      }
    }
  });

  /* ── Toggle sidebar (mobile) ── */
  const sidebar = document.getElementById('sidebar');
  document.getElementById('sidebarToggle')?.addEventListener('click', () => {
    sidebar.classList.toggle('sidebar-open');
  });
  document.addEventListener('click', e => {
    if (sidebar.classList.contains('sidebar-open') &&
        !sidebar.contains(e.target) &&
        !e.target.closest('#sidebarToggle')) {
      sidebar.classList.remove('sidebar-open');
    }
  });

});
</script>

<?php require_once ROOT_PATH . '/views/layouts/footer.php'; ?>