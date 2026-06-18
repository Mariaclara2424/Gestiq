<?php require_once ROOT_PATH . '/views/layouts/header.php'; ?>

<div class="d-flex justify-content-between align-items-center mb-4 mt-2">
    <div>
        <h2 class="h4 fw-bold mb-0 text-dark">
            <i class="fa-solid fa-user-plus me-2" style="color: var(--brand-color);"></i>Novo Usuário
        </h2>
        <p class="text-muted small mb-0 mt-1">Cadastre um novo profissional e defina suas permissões de acesso.</p>
    </div>
    <a href="<?= BASE_URL ?>/usuarios" class="btn btn-light border shadow-sm text-secondary rounded-pill px-3 fw-semibold">
        <i class="fa-solid fa-arrow-left me-1"></i> Voltar
    </a>
</div>

<?php if (!empty($erros)): ?>
    <div class="alert alert-danger shadow-sm border-0" style="border-radius: 10px;">
        <ul class="mb-0 ps-3">
            <?php foreach ($erros as $erro): ?>
                <li><?= htmlspecialchars($erro) ?></li>
            <?php endforeach; ?>
        </ul>
    </div>
<?php endif; ?>

<div class="card-premium shadow-sm border-0" style="border-radius: 12px; background: #fff;">
    <div class="card-body p-4">
        <form method="POST" action="<?= BASE_URL ?>/usuarios/criar">
            
            <h6 class="fw-bold text-secondary mb-3 border-bottom pb-2">Informações Pessoais e Acesso</h6>
            <div class="row g-4 mb-4">
                
                <div class="col-md-6">
                    <label class="form-label fw-semibold text-secondary small">Nome Completo <span class="text-danger">*</span></label>
                    <input type="text" name="nome" class="form-control" placeholder="Ex: João da Silva" value="<?= htmlspecialchars($dados['nome'] ?? '') ?>" required>
                </div>

                <div class="col-md-6">
                    <label class="form-label fw-semibold text-secondary small">E-mail Corporativo <span class="text-danger">*</span></label>
                    <input type="email" name="email" class="form-control" placeholder="Ex: joao.silva@gestiq.com" value="<?= htmlspecialchars($dados['email'] ?? '') ?>" required>
                </div>

                <div class="col-md-6">
                    <label class="form-label fw-semibold text-secondary small">Senha de Acesso <span class="text-danger">*</span></label>
                    <input type="password" name="senha" class="form-control" placeholder="Mínimo 6 caracteres" required>
                </div>

                <div class="col-md-6">
                    <label class="form-label fw-semibold text-secondary small">Confirmar Senha <span class="text-danger">*</span></label>
                    <input type="password" name="confirmar_senha" class="form-control" placeholder="Repita a senha" required>
                </div>

            </div>

            <h6 class="fw-bold text-secondary mb-3 border-bottom pb-2">Vínculos Institucionais</h6>
            <div class="row g-4">
                
                <div class="col-md-4">
                    <label class="form-label fw-semibold text-secondary small">Perfil do Usuário <span class="text-danger">*</span></label>
                    <select name="perfil" class="form-select" required>
                        <option value="">Selecione...</option>
                        <?php foreach ($perfis as $p): ?>
                            <option value="<?= $p ?>" <?= (($dados['perfil'] ?? '') === $p) ? 'selected' : '' ?>>
                                <?= match($p) { 'admin' => 'Administrador', 'gestor' => 'Gerente', default => 'Colaborador' } ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="col-md-4">
                    <label class="form-label fw-semibold text-secondary small">Departamento</label>
                    <select name="id_departamento" class="form-select">
                        <option value="">Nenhum...</option>
                        <?php foreach ($departamentos as $d): ?>
                            <option value="<?= $d['id_departamento'] ?>" <?= (($dados['id_departamento'] ?? '') == $d['id_departamento']) ? 'selected' : '' ?>>
                                <?= htmlspecialchars($d['nome']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="col-md-4">
                    <label class="form-label fw-semibold text-secondary small">Cargo</label>
                    <select name="id_cargo" class="form-select">
                        <option value="">Nenhum...</option>
                        <?php foreach ($cargos as $c): ?>
                            <option value="<?= $c['id_cargo'] ?>" <?= (($dados['id_cargo'] ?? '') == $c['id_cargo']) ? 'selected' : '' ?>>
                                <?= htmlspecialchars($c['nome']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="col-md-12 mt-4">
                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" name="ativo" value="1" id="ativoSwitch" <?= (!isset($dados['ativo']) || $dados['ativo'] == 1) ? 'checked' : '' ?>>
                        <label class="form-check-label fw-semibold text-secondary small ms-1" for="ativoSwitch">Usuário Ativo (pode acessar o sistema)</label>
                    </div>
                </div>

            </div>

            <hr class="my-4 text-muted">

            <div class="d-flex justify-content-end gap-2">
                <a href="<?= BASE_URL ?>/usuarios" class="btn btn-light border text-secondary px-4 fw-semibold rounded-pill">Cancelar</a>
                <button type="submit" class="btn text-white px-4 fw-semibold rounded-pill shadow-sm" style="background-color: var(--brand-color);">
                    <i class="fa-solid fa-check me-1"></i> Salvar Usuário
                </button>
            </div>
        </form>
    </div>
</div>

<?php require_once ROOT_PATH . '/views/layouts/footer.php'; ?>