<?php require_once ROOT_PATH . '/views/layouts/header.php'; ?>

<div class="d-flex justify-content-between align-items-center mb-4 mt-2">
    <div>
        <h2 class="h4 fw-bold mb-0 text-dark">
            <i class="bi bi-building me-2" style="color: var(--brand-color);"></i>Módulo de Departamentos
        </h2>
    </div>
    <a href="<?= BASE_URL ?>/departamentos/criar" class="btn rounded-pill px-4 shadow-sm text-white fw-semibold" style="background-color: var(--brand-color);">
        <i class="bi bi-plus-lg me-2"></i>Novo Departamento
    </a>
</div>

<div class="card border-0 shadow-sm" style="border-radius: 12px;">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light text-muted small text-uppercase">
                    <tr>
                        <th class="ps-4 py-3">Nome do Setor</th>
                        <th class="py-3">Descrição</th>
                        <th class="py-3">Estatísticas</th>
                        <th class="text-end pe-4 py-3">Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($departamentos as $depto): ?>
                    <tr>
                        <td class="ps-4">
                            <div class="fw-bold text-dark"><?= htmlspecialchars($depto['nome']); ?></div>
                        </td>
                        <td><small class="text-muted"><?= htmlspecialchars($depto['descricao'] ?? '---'); ?></small></td>
                        <td>
                            <span class="badge bg-light text-dark border">
                                <i class="bi bi-person me-1"></i><?= $depto['total_usuarios'] ?? 0 ?> Usuários
                                <i class="bi bi-briefcase ms-2 me-1"></i><?= $depto['total_cargos'] ?? 0 ?> Cargos
                            </span>
                        </td>
                        <td class="text-end pe-4">
                            <div class="btn-group">
                                <a href="<?= BASE_URL ?>/departamentos/editar/<?= $depto['id_departamento'] ?>" class="btn btn-sm btn-light border"><i class="bi bi-pencil"></i></a>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php require_once ROOT_PATH . '/views/layouts/footer.php'; ?>