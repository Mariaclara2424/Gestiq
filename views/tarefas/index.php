<?php require_once ROOT_PATH . '/views/layouts/header.php'; ?>

<div class="d-flex justify-content-between align-items-center mb-4 mt-2">
    <div>
        <h2 class="h4 fw-bold mb-0 text-dark">
            <i class="bi bi-check2-square me-2" style="color: var(--brand-color);"></i>Gestão de Tarefas
        </h2>
        <p class="text-muted small mb-0 mt-1">Gerencie as atividades, prazos e prioridades da equipe.</p>
    </div>
    
    <a href="<?= BASE_URL ?>/tarefas/criar" class="btn rounded-pill px-4 shadow-sm text-white fw-semibold" style="background-color: var(--brand-color); border-color: var(--brand-color); transition: all 0.3s;">
        <i class="bi bi-plus-lg me-2"></i>Nova Tarefa
    </a>
</div>

<?php renderFlash(); ?>

<div class="card border-0 shadow-sm mb-4" style="border-radius: 12px;">
    <div class="card-body bg-light rounded" style="border-radius: 12px;">
        <form method="GET" action="<?= BASE_URL ?>/tarefas" class="row g-3 align-items-end">
            <div class="col-md-3">
                <label class="form-label small text-muted fw-semibold">Busca</label>
                <input type="text" name="busca" class="form-control form-control-sm" placeholder="Título ou descrição..." value="<?= htmlspecialchars($_GET['busca'] ?? '') ?>">
            </div>
            <div class="col-md-2">
                <label class="form-label small text-muted fw-semibold">Status</label>
                <select name="status" class="form-select form-select-sm">
                    <option value="">Todos</option>
                    <?php foreach ($status as $s): ?>
                        <option value="<?= $s ?>" <?= ($_GET['status'] ?? '') === $s ? 'selected' : '' ?>><?= ucfirst(str_replace('_', ' ', $s)) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label small text-muted fw-semibold">Prioridade</label>
                <select name="prioridade" class="form-select form-select-sm">
                    <option value="">Todas</option>
                    <?php foreach ($prioridades as $p): ?>
                        <option value="<?= $p ?>" <?= ($_GET['prioridade'] ?? '') === $p ? 'selected' : '' ?>><?= ucfirst($p) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label small text-muted fw-semibold">Usuário Responsável</label>
                <select name="id_responsavel" class="form-select form-select-sm">
                    <option value="">Qualquer um</option>
                    <?php foreach ($usuarios as $u): ?>
                        <option value="<?= $u['id_usuario'] ?>" <?= (int)($_GET['id_responsavel'] ?? 0) === (int)$u['id_usuario'] ? 'selected' : '' ?>><?= htmlspecialchars($u['nome']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-sm btn-secondary w-100 fw-semibold"><i class="bi bi-filter me-1"></i>Filtrar</button>
            </div>
        </form>
    </div>
</div>

<div class="card border-0 shadow-sm" style="border-radius: 12px; overflow: hidden;">
    <div class="card-body p-0">
        <?php if (empty($tarefas)): ?>
            <div class="text-center p-5 text-muted">
                <div class="bg-light rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 80px; height: 80px;">
                    <i class="bi bi-card-checklist fs-1 text-secondary"></i>
                </div>
                <h5 class="fw-bold text-dark">Nenhuma tarefa encontrada</h5>
                <p>Altere os filtros de busca ou crie uma nova tarefa.</p>
            </div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light text-muted small text-uppercase" style="letter-spacing: 0.5px;">
                        <tr>
                            <th class="ps-4 fw-semibold">Tarefa / Projeto</th>
                            <th class="fw-semibold">Responsável</th>
                            <th class="fw-semibold">Vencimento</th>
                            <th class="fw-semibold">Status</th>
                            <th class="fw-semibold">Prioridade</th>
                            <th class="text-end pe-4 fw-semibold">Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($tarefas as $t): ?>
                        <tr>
                            <td class="ps-4">
                                <a href="<?= BASE_URL ?>/tarefas/<?= $t['id_tarefa'] ?>" class="text-decoration-none fw-bold text-dark d-block">
                                    <?= htmlspecialchars($t['titulo']) ?>
                                </a>
                                <span class="text-muted" style="font-size: 0.75rem;">
                                    <i class="bi bi-folder2 me-1"></i><?= htmlspecialchars($t['projeto_titulo'] ?? 'Sem Projeto') ?>
                                </span>
                            </td>
                            
                            <td>
                                <?php if ($t['responsavel_nome']): ?>
                                    <div class="d-flex align-items-center gap-2">
                                        <img src="https://ui-avatars.com/api/?name=<?= urlencode($t['responsavel_nome']) ?>&background=random&color=fff&size=28" class="rounded-circle shadow-sm" alt="Avatar">
                                        <span class="text-dark fw-medium small"><?= htmlspecialchars($t['responsavel_nome']) ?></span>
                                    </div>
                                <?php else: ?>
                                    <span class="text-muted small fst-italic">Não atribuído</span>
                                <?php endif; ?>
                            </td>
                            
                            <td>
                                <?php if ($t['data_vencimento']): ?>
                                    <?php $atrasada = $t['data_vencimento'] < date('Y-m-d') && !in_array($t['status'], ['concluida', 'cancelada']); ?>
                                    <div class="fw-medium small <?= $atrasada ? 'text-danger fw-bold' : 'text-dark' ?>">
                                        <?= date('d/m/Y', strtotime($t['data_vencimento'])) ?>
                                        <?= $atrasada ? '<i class="bi bi-exclamation-triangle-fill ms-1"></i>' : '' ?>
                                    </div>
                                <?php else: ?>
                                    <span class="text-muted small">—</span>
                                <?php endif; ?>
                            </td>
                            
                            <td>
                                <?php
                                    $corStatus = match($t['status']) {
                                        'concluida' => 'success',
                                        'em_andamento' => 'primary',
                                        'cancelada' => 'secondary',
                                        default => 'warning'
                                    };
                                ?>
                                <span class="badge bg-<?= $corStatus ?> bg-opacity-10 text-<?= $corStatus ?> border border-<?= $corStatus ?> rounded-pill px-2 py-1" style="font-size: 0.7rem;">
                                    <?= ucfirst(str_replace('_', ' ', $t['status'])) ?>
                                </span>
                            </td>

                            <td>
                                <?php
                                    $corPrior = match($t['prioridade']) {
                                        'critica' => 'danger',
                                        'alta' => 'warning',
                                        'media' => 'info',
                                        default => 'secondary'
                                    };
                                ?>
                                <span class="badge bg-<?= $corPrior ?> rounded-pill" style="font-size: 0.7rem;">
                                    <?= ucfirst($t['prioridade']) ?>
                                </span>
                            </td>
                            
                            <td class="text-end pe-4">
                                <div class="btn-group shadow-sm">
                                    <a href="<?= BASE_URL ?>/tarefas/<?= $t['id_tarefa'] ?>" class="btn btn-light btn-sm border text-primary" title="Visualizar">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    <a href="<?= BASE_URL ?>/tarefas/<?= $t['id_tarefa'] ?>/editar" class="btn btn-light btn-sm border text-secondary" title="Editar">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    <?php if (Auth::can('admin', 'gestor')): ?>
                                    <form method="POST" action="<?= BASE_URL ?>/tarefas/<?= $t['id_tarefa'] ?>/excluir" class="d-inline" onsubmit="return confirm('Tem certeza que deseja excluir esta tarefa?');">
                                        <button type="submit" class="btn btn-light btn-sm border text-danger" title="Excluir">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                    <?php endif; ?>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php require_once ROOT_PATH . '/views/layouts/footer.php'; ?>