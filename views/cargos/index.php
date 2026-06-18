<?php require_once ROOT_PATH . '/views/layouts/header.php'; ?>
<?php renderFlash(); ?>

<div class="d-flex justify-content-between align-items-center mb-3 mt-2">
    <h6 class="m-0 text-muted fw-bold text-uppercase" style="font-size: 12px;">Gerenciamento de Cargos</h6>
    
    <a href="<?= BASE_URL ?>/cargos/criar" class="btn btn-sm text-white fw-semibold shadow-sm" style="background-color: var(--brand-color); border-radius: 20px; padding: 6px 16px;">
        <i class="fa-solid fa-plus me-1"></i> Novo Cargo
    </a>
</div>

<div class="card-premium shadow-sm border-0" style="border-radius: 12px; overflow: hidden; background: #fff;">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table align-middle m-0 table-hover" style="font-size: 14px;">
                <thead class="table-light text-secondary" style="border-bottom: 2px solid #f0f0f0;">
                    <tr>
                        <th class="ps-4 py-3 fw-semibold">Cargo</th>
                        <th class="py-3 fw-semibold">Departamento</th>
                        <th class="py-3 fw-semibold">Descrição</th>
                        <th class="py-3 fw-semibold text-center">Usuários Vinculados</th>
                        <th class="pe-4 py-3 fw-semibold text-end">Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($cargos)): ?>
                        <tr>
                            <td colspan="5" class="text-center py-5 text-muted">
                                <i class="fa-solid fa-briefcase fs-2 mb-2 text-light"></i>
                                <p class="mb-0">Nenhum cargo cadastrado no sistema.</p>
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($cargos as $c): ?>
                        <tr style="transition: all 0.2s;">
                            <td class="ps-4 py-3 fw-bold" style="color: #2b284c;">
                                <?= htmlspecialchars($c['nome']) ?>
                            </td>
                            
                            <td class="py-3">
                                <?php if ($c['departamento_nome']): ?>
                                    <span class="badge bg-light text-dark border px-2 py-1">
                                        <i class="fa-solid fa-building text-secondary me-1"></i> <?= htmlspecialchars($c['departamento_nome']) ?>
                                    </span>
                                <?php else: ?>
                                    <span class="text-muted fst-italic" style="font-size: 12px;">Sem departamento</span>
                                <?php endif; ?>
                            </td>
                            
                            <td class="py-3 text-muted" style="max-width: 250px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                                <?= htmlspecialchars($c['descricao'] ?? '—') ?>
                            </td>
                            
                            <td class="py-3 text-center">
                                <?php $temUsuarios = $c['total_usuarios'] > 0; ?>
                                <span class="badge rounded-pill <?= $temUsuarios ? 'bg-primary-subtle text-primary' : 'bg-secondary-subtle text-secondary' ?> px-2 py-1" style="font-size: 12px; font-weight: bold;">
                                    <?= (int)$c['total_usuarios'] ?>
                                </span>
                            </td>

                            <td class="pe-4 py-3 text-end">
                                <div class="btn-group shadow-sm">
                                    <a href="<?= BASE_URL ?>/cargos/<?= $c['id_cargo'] ?>/editar" class="btn btn-light btn-sm border text-secondary" title="Editar">
                                        <i class="fa-solid fa-pen"></i>
                                    </a>
                                    <form method="POST" action="<?= BASE_URL ?>/cargos/<?= $c['id_cargo'] ?>/excluir" class="d-inline" onsubmit="return confirm('Tem certeza que deseja excluir este cargo?');">
                                        <button type="submit" class="btn btn-light btn-sm border text-danger" title="Excluir">
                                            <i class="fa-solid fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php require_once ROOT_PATH . '/views/layouts/footer.php'; ?>