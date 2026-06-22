<?php require_once ROOT_PATH . '/views/layouts/header.php'; ?>
<?php renderFlash(); ?>

<!-- BREADCRUMB -->
<nav aria-label="breadcrumb" class="mb-3">
  <ol class="breadcrumb">
    <li class="breadcrumb-item"><a href="<?= BASE_URL ?>/projetos">Projetos</a></li>
    <li class="breadcrumb-item active"><?= htmlspecialchars($projeto['titulo']) ?></li>
  </ol>
</nav>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="h4 fw-bold mb-0 text-dark">
            <i class="bi bi-folder2-open me-2" style="color: var(--brand-color);"></i><?= htmlspecialchars($projeto['titulo']) ?>
        </h2>
        <p class="text-muted small mb-0 mt-1">Detalhes e acompanhamento do projeto.</p>
    </div>
    <div class="d-flex gap-2">
        <?php if (Auth::can('admin', 'gestor')): ?>
        <a href="<?= BASE_URL ?>/projetos/<?= $projeto['id_projeto'] ?>/editar"
           class="btn btn-outline-secondary btn-sm rounded-pill px-3">
            <i class="bi bi-pencil me-1"></i>Editar
        </a>
        <form method="POST" action="<?= BASE_URL ?>/projetos/<?= $projeto['id_projeto'] ?>/excluir"
              onsubmit="return confirm('Tem certeza que deseja excluir este projeto?')">
            <button class="btn btn-outline-danger btn-sm rounded-pill px-3">
                <i class="bi bi-trash me-1"></i>Excluir
            </button>
        </form>
        <?php endif; ?>
        <a href="<?= BASE_URL ?>/tarefas/criar?id_projeto=<?= $projeto['id_projeto'] ?>"
           class="btn btn-sm rounded-pill px-3 text-white fw-semibold"
           style="background-color: var(--brand-color); border-color: var(--brand-color);">
            <i class="bi bi-plus-lg me-1"></i>Nova Tarefa
        </a>
    </div>
</div>

<div class="row g-4">

    <!-- COLUNA PRINCIPAL -->
    <div class="col-lg-8">

        <!-- Detalhes -->
        <div class="card border-0 shadow-sm mb-4" style="border-radius: 12px;">
            <div class="card-header bg-transparent fw-semibold border-bottom py-3">
                <i class="bi bi-info-circle me-2" style="color: var(--brand-color);"></i>Informações do Projeto
            </div>
            <div class="card-body">
                <div class="row g-3 mb-3">
                    <div class="col-6 col-md-3">
                        <small class="text-muted d-block mb-1">Status</small>
                        <?php
                        $corStatus = match($projeto['status']) {
                            'concluido'    => 'success',
                            'em_andamento' => 'primary',
                            'cancelado'    => 'secondary',
                            default        => 'warning'
                        };
                        ?>
                        <span class="badge bg-<?= $corStatus ?> bg-opacity-10 text-<?= $corStatus ?> border border-<?= $corStatus ?> rounded-pill px-2">
                            <?= ucfirst(str_replace('_', ' ', $projeto['status'])) ?>
                        </span>
                    </div>
                    <div class="col-6 col-md-3">
                        <small class="text-muted d-block mb-1">Responsável</small>
                        <span class="fw-medium small"><?= htmlspecialchars($projeto['responsavel_nome'] ?? '—') ?></span>
                    </div>
                    <div class="col-6 col-md-3">
                        <small class="text-muted d-block mb-1">Início</small>
                        <span class="small"><?= !empty($projeto['data_inicio']) ? date('d/m/Y', strtotime($projeto['data_inicio'])) : '—' ?></span>
                    </div>
                    <div class="col-6 col-md-3">
                        <small class="text-muted d-block mb-1">Previsão de Fim</small>
                        <?php
                        $atrasado = !empty($projeto['data_fim'])
                            && $projeto['data_fim'] < date('Y-m-d')
                            && !in_array($projeto['status'], ['concluido', 'cancelado']);
                        ?>
                        <span class="small <?= $atrasado ? 'text-danger fw-bold' : '' ?>">
                            <?= !empty($projeto['data_fim']) ? date('d/m/Y', strtotime($projeto['data_fim'])) : '—' ?>
                            <?= $atrasado ? '<i class="bi bi-exclamation-triangle-fill ms-1"></i>' : '' ?>
                        </span>
                    </div>
                </div>

                <?php if (!empty($projeto['descricao'])): ?>
                <hr>
                <h6 class="fw-semibold">Descrição</h6>
                <p class="mb-0 text-muted"><?= nl2br(htmlspecialchars($projeto['descricao'])) ?></p>
                <?php endif; ?>
            </div>
        </div>

        <!-- Alterar status rápido -->
        <?php if (Auth::can('admin', 'gestor')): ?>
        <div class="card border-0 shadow-sm mb-4" style="border-radius: 12px;">
            <div class="card-body">
                <form method="POST" action="<?= BASE_URL ?>/projetos/<?= $projeto['id_projeto'] ?>/status"
                      class="d-flex gap-2 align-items-center flex-wrap">
                    <label class="form-label mb-0 fw-semibold">Alterar status:</label>
                    <select name="status" class="form-select form-select-sm w-auto">
                        <?php foreach (['planejamento', 'em_andamento', 'concluido', 'cancelado'] as $s): ?>
                        <option value="<?= $s ?>" <?= $s === $projeto['status'] ? 'selected' : '' ?>>
                            <?= ucfirst(str_replace('_', ' ', $s)) ?>
                        </option>
                        <?php endforeach; ?>
                    </select>
                    <button class="btn btn-primary btn-sm">Salvar</button>
                </form>
            </div>
        </div>
        <?php endif; ?>

        <!-- Tarefas do projeto -->
        <div class="card border-0 shadow-sm" style="border-radius: 12px; overflow: hidden;">
            <div class="card-header bg-transparent fw-semibold border-bottom py-3 d-flex justify-content-between align-items-center">
                <span><i class="bi bi-check2-square me-2" style="color: var(--brand-color);"></i>Tarefas</span>
                <a href="<?= BASE_URL ?>/tarefas/criar?id_projeto=<?= $projeto['id_projeto'] ?>"
                   class="btn btn-sm rounded-pill px-3 text-white"
                   style="background-color: var(--brand-color); font-size: 0.75rem;">
                    <i class="bi bi-plus-lg me-1"></i>Nova
                </a>
            </div>
            <?php if (empty($projeto['tarefas'])): ?>
                <div class="text-center p-4 text-muted">
                    <i class="bi bi-card-checklist fs-2 d-block mb-2 text-secondary"></i>
                    <p class="mb-0 small">Nenhuma tarefa vinculada a este projeto.</p>
                </div>
            <?php else: ?>
                <ul class="list-group list-group-flush">
                    <?php foreach ($projeto['tarefas'] as $t):
                        $corT = match($t['status']) {
                            'concluida'    => 'success',
                            'em_andamento' => 'primary',
                            'cancelada'    => 'secondary',
                            default        => 'warning'
                        };
                        $corP = match($t['prioridade'] ?? 'baixa') {
                            'critica' => 'danger',
                            'alta'    => 'warning',
                            'media'   => 'info',
                            default   => 'secondary'
                        };
                    ?>
                    <li class="list-group-item border-0 border-bottom px-4 py-3">
                        <div class="d-flex align-items-center gap-3">
                            <div class="flex-grow-1">
                                <a href="<?= BASE_URL ?>/tarefas/<?= $t['id_tarefa'] ?>"
                                   class="text-decoration-none fw-semibold text-dark d-block">
                                    <?= htmlspecialchars($t['titulo']) ?>
                                </a>
                                <?php if (!empty($t['responsavel_nome'])): ?>
                                <small class="text-muted">
                                    <i class="bi bi-person me-1"></i><?= htmlspecialchars($t['responsavel_nome']) ?>
                                </small>
                                <?php endif; ?>
                            </div>
                            <span class="badge bg-<?= $corP ?> rounded-pill" style="font-size: 0.65rem;">
                                <?= ucfirst($t['prioridade'] ?? '—') ?>
                            </span>
                            <span class="badge bg-<?= $corT ?> bg-opacity-10 text-<?= $corT ?> border border-<?= $corT ?> rounded-pill px-2" style="font-size: 0.65rem;">
                                <?= ucfirst(str_replace('_', ' ', $t['status'])) ?>
                            </span>
                            <a href="<?= BASE_URL ?>/tarefas/<?= $t['id_tarefa'] ?>"
                               class="btn btn-light btn-sm border text-primary" title="Ver tarefa">
                                <i class="bi bi-eye"></i>
                            </a>
                        </div>
                    </li>
                    <?php endforeach; ?>
                </ul>
            <?php endif; ?>
        </div>

    </div>

    <!-- SIDEBAR INFO -->
    <div class="col-lg-4">
        <div class="card border-0 shadow-sm" style="border-radius: 12px;">
            <div class="card-header bg-transparent fw-semibold border-bottom py-3">Resumo</div>
            <ul class="list-group list-group-flush small">
                <li class="list-group-item d-flex justify-content-between px-4">
                    <span class="text-muted">Departamento</span>
                    <span><?= htmlspecialchars($projeto['departamento_nome'] ?? '—') ?></span>
                </li>
                <li class="list-group-item d-flex justify-content-between px-4">
                    <span class="text-muted">Criado em</span>
                    <span><?= !empty($projeto['created_at']) ? date('d/m/Y', strtotime($projeto['created_at'])) : '—' ?></span>
                </li>
                <li class="list-group-item d-flex justify-content-between px-4">
                    <span class="text-muted">Atualizado em</span>
                    <span><?= !empty($projeto['updated_at']) ? date('d/m/Y H:i', strtotime($projeto['updated_at'])) : '—' ?></span>
                </li>
                <li class="list-group-item d-flex justify-content-between px-4">
                    <span class="text-muted">Total de Tarefas</span>
                    <span class="fw-bold"><?= count($projeto['tarefas'] ?? []) ?></span>
                </li>
            </ul>
        </div>
    </div>

</div>

<?php require_once ROOT_PATH . '/views/layouts/footer.php'; ?>