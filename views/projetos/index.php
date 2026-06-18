<?php require_once ROOT_PATH . '/views/layouts/header.php'; ?>

<div class="d-flex justify-content-between align-items-center mb-4 mt-2">
    <div>
        <h2 class="h4 fw-bold mb-0 text-dark">
            <i class="bi bi-folder2-open me-2" style="color: var(--brand-color);"></i>Gestão de Projetos
        </h2>
        <p class="text-muted small mb-0 mt-1">Acompanhe e gerencie todos os projetos da empresa.</p>
    </div>
    
    <a href="<?= BASE_URL ?>/projetos/criar" class="btn rounded-pill px-4 shadow-sm text-white fw-semibold" style="background-color: var(--brand-color); border-color: var(--brand-color); transition: all 0.3s;">
        <i class="bi bi-plus-lg me-2"></i>Novo Projeto
    </a>
</div>

<?php renderFlash(); ?>

<div class="card border-0 shadow-sm" style="border-radius: 12px; overflow: hidden;">
    
    <div class="card-header bg-white border-bottom py-3 d-flex justify-content-between align-items-center">
        <div class="input-group" style="max-width: 300px;">
            <span class="input-group-text bg-light border-end-0 text-muted"><i class="bi bi-search"></i></span>
            <input type="text" class="form-control bg-light border-start-0" placeholder="Buscar projeto...">
        </div>
        <button class="btn btn-light btn-sm text-muted border"><i class="bi bi-filter me-1"></i>Filtros</button>
    </div>

    <div class="card-body p-0">
        <?php if (empty($projetos)): ?>
            <div class="text-center p-5 text-muted">
                <div class="bg-light rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 80px; height: 80px;">
                    <i class="bi bi-folder-x fs-1 text-secondary"></i>
                </div>
                <h5 class="fw-bold text-dark">Nenhum projeto encontrado</h5>
                <p>Você ainda não possui projetos cadastrados no sistema.</p>
                <a href="<?= BASE_URL ?>/projetos/criar" class="btn btn-outline-primary mt-2">Criar o primeiro projeto</a>
            </div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light text-muted small text-uppercase" style="letter-spacing: 0.5px;">
                        <tr>
                            <th class="ps-4 fw-semibold">Título do Projeto</th>
                            <th class="fw-semibold">Responsável</th>
                            <th class="fw-semibold">Departamento</th>
                            <th class="fw-semibold">Prazo</th>
                            <th class="fw-semibold">Status</th>
                            <th class="text-end pe-4 fw-semibold">Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($projetos as $projeto): ?>
                        <tr style="transition: background-color 0.2s;">
                            <td class="ps-4">
                                <a href="<?= BASE_URL ?>/projetos/<?= $projeto['id_projeto'] ?>" class="text-decoration-none fw-bold text-dark d-block">
                                    <?= htmlspecialchars($projeto['titulo']) ?>
                                </a>
                                <span class="text-muted" style="font-size: 0.75rem;">#<?= str_pad($projeto['id_projeto'], 4, '0', STR_PAD_LEFT) ?></span>
                            </td>
                            
                            <td>
                                <div class="d-flex align-items-center gap-2">
                                    <?php $nomeResp = htmlspecialchars($projeto['responsavel_nome'] ?? 'Sem Atribuição'); ?>
                                    <img src="https://ui-avatars.com/api/?name=<?= urlencode($nomeResp) ?>&background=random&color=fff&size=32" class="rounded-circle shadow-sm" alt="Avatar">
                                    <span class="text-dark fw-medium" style="font-size: 0.9rem;"><?= $nomeResp ?></span>
                                </div>
                            </td>
                            
                            <td>
                                <span class="text-muted" style="font-size: 0.9rem;">
                                    <i class="bi bi-building me-1"></i><?= htmlspecialchars($projeto['departamento_nome'] ?? '—') ?>
                                </span>
                            </td>
                            
                            <td>
                                <div class="text-dark fw-medium" style="font-size: 0.9rem;">
                                    <?= $projeto['data_fim'] ? date('d/m/Y', strtotime($projeto['data_fim'])) : '<span class="text-muted">Sem prazo</span>' ?>
                                </div>
                            </td>
                            
                            <td>
                                <?php
                                    $status = $projeto['status'];
                                    $badgeColor = match($status) {
                                        'concluido' => 'success',
                                        'em_andamento' => 'primary',
                                        'planejamento' => 'secondary',
                                        'cancelado' => 'danger',
                                        default => 'dark'
                                    };
                                    $statusFormatado = ucfirst(str_replace('_', ' ', $status));
                                ?>
                                <span class="badge bg-<?= $badgeColor ?> bg-opacity-10 text-<?= $badgeColor ?> border border-<?= $badgeColor ?> rounded-pill px-3 py-2 fw-semibold" style="font-size: 0.75rem;">
                                    <?= $statusFormatado ?>
                                </span>
                            </td>
                            
                            <td class="text-end pe-4">
                                <div class="btn-group shadow-sm">
                                    <a href="<?= BASE_URL ?>/projetos/<?= $projeto['id_projeto'] ?>" class="btn btn-light btn-sm border text-primary" title="Visualizar">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    <a href="<?= BASE_URL ?>/projetos/<?= $projeto['id_projeto'] ?>/editar" class="btn btn-light btn-sm border text-secondary" title="Editar">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    <form method="POST" action="<?= BASE_URL ?>/projetos/<?= $projeto['id_projeto'] ?>/excluir" class="d-inline" onsubmit="return confirm('Tem certeza que deseja excluir este projeto? Esta ação não pode ser desfeita.');">
                                        <button type="submit" class="btn btn-light btn-sm border text-danger" title="Excluir">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
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