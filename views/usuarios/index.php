<?php require_once ROOT_PATH . '/views/layouts/header.php'; ?>
<?php renderFlash(); ?>

<div class="d-flex justify-content-between align-items-center mb-3 mt-2">
    <h6 class="m-0 text-muted fw-bold text-uppercase" style="font-size: 12px;">Controle de Acesso e Permissões dos Usuários</h6>
    
    <a href="<?= BASE_URL ?>/usuarios/criar" class="btn btn-sm text-white fw-semibold shadow-sm" style="background-color: var(--brand-color); border-radius: 20px; padding: 6px 16px;">
        <i class="fa-solid fa-plus me-1"></i> Novo Usuário
    </a>
</div>

<div class="card-premium shadow-sm border-0" style="border-radius: 12px; overflow: hidden; background: #fff;">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table align-middle m-0 table-hover" style="font-size: 14px;">
                <thead class="table-light text-secondary" style="border-bottom: 2px solid #f0f0f0;">
                    <tr>
                        <th class="ps-4 py-3 fw-semibold">Nome do Profissional</th>
                        <th class="py-3 fw-semibold">E-mail Corporativo</th>
                        <th class="py-3 fw-semibold">Perfil Administrativo</th>
                        <th class="py-3 fw-semibold">Situação</th>
                        <th class="pe-4 py-3 fw-semibold text-end">Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($usuarios)): ?>
                        <tr>
                            <td colspan="5" class="text-center py-5 text-muted">
                                <i class="fa-solid fa-users-slash fs-2 mb-2 text-light"></i>
                                <p class="mb-0">Nenhum usuário encontrado no sistema.</p>
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($usuarios as $u): ?>
                        <tr style="transition: all 0.2s;">
                            <td class="ps-4 py-3">
                                <div class="fw-bold" style="color: #2b284c; font-size: 15px;">
                                    <?= htmlspecialchars($u['nome']) ?>
                                </div>
                                <div class="text-muted mt-1" style="font-size: 11px;">
                                    <i class="fa-solid fa-briefcase me-1"></i><?= htmlspecialchars($u['cargo_nome'] ?? 'Sem cargo') ?> &nbsp;|&nbsp; 
                                    <i class="fa-solid fa-building me-1"></i><?= htmlspecialchars($u['departamento_nome'] ?? 'Sem departamento') ?>
                                </div>
                            </td>
                            
                            <td class="text-muted py-3"><?= htmlspecialchars($u['email']) ?></td>
                            
                            <td class="py-3">
                                <?php
                                    $icon = match($u['perfil']) {
                                        'admin'  => '<i class="fa-solid fa-shield-halved text-primary me-1"></i>',
                                        'gestor' => '<i class="fa-solid fa-user-tie text-secondary me-1"></i>',
                                        default  => '<i class="fa-solid fa-user text-muted me-1"></i>'
                                    };
                                    $perfilNome = match($u['perfil']) {
                                        'admin'  => 'Administrador',
                                        'gestor' => 'Gerente',
                                        default  => 'Colaborador'
                                    };
                                ?>
                                <span class="badge bg-light text-dark border px-2 py-1"><?= $icon . $perfilNome ?></span>
                            </td>
                            
                            <td class="py-3">
                                <?php if ($u['ativo']): ?>
                                    <span class="badge bg-success-subtle text-success px-3 py-1" style="font-size: 11px; border-radius: 20px; font-weight: bold;">Ativo</span>
                                <?php else: ?>
                                    <span class="badge bg-danger-subtle text-danger px-3 py-1" style="font-size: 11px; border-radius: 20px; font-weight: bold;">Inativo</span>
                                <?php endif; ?>
                            </td>

                            <td class="pe-4 py-3 text-end">
                                <div class="btn-group shadow-sm">
                                    <a href="<?= BASE_URL ?>/usuarios/<?= $u['id_usuario'] ?>/editar" class="btn btn-light btn-sm border text-secondary" title="Editar">
                                        <i class="fa-solid fa-pen"></i>
                                    </a>
                                    
                                    <?php if ((int)$u['id_usuario'] !== (int)Auth::user()['id']): ?>
                                    <form method="POST" action="<?= BASE_URL ?>/usuarios/<?= $u['id_usuario'] ?>/toggle" class="d-inline" onsubmit="return confirm('Alterar o status de acesso deste usuário?');">
                                        <button type="submit" class="btn btn-light btn-sm border text-<?= $u['ativo'] ? 'warning' : 'success' ?>" title="<?= $u['ativo'] ? 'Desativar Acesso' : 'Ativar Acesso' ?>">
                                            <i class="fa-solid fa-power-off"></i>
                                        </button>
                                    </form>
                                    <?php endif; ?>
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