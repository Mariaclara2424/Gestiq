<?php require_once ROOT_PATH . '/views/layouts/header.php'; ?>
<?php renderFlash(); ?>

<div class="d-flex justify-content-between align-items-center mb-3 mt-2">
    <h6 class="m-0 text-muted fw-bold text-uppercase" style="font-size: 12px;">Editar Cargo</h6>
    
    <a href="<?= BASE_URL ?>/cargos" class="btn btn-sm btn-light border text-secondary shadow-sm" style="border-radius: 20px; padding: 6px 16px;">
        <i class="fa-solid fa-arrow-left me-1"></i> Voltar
    </a>
</div>

<div class="card shadow-sm border-0" style="border-radius: 12px; overflow: hidden; background: #fff;">
    <div class="card-body p-4">
        
        <form method="POST" action="<?= BASE_URL ?>/cargos/<?= $cargo['id_cargo'] ?>/editar">
            
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="nome" class="form-label fw-semibold text-dark">Nome do Cargo <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" id="nome" name="nome" value="<?= htmlspecialchars($cargo['nome'] ?? '') ?>" required placeholder="Ex: Desenvolvedor Senior">
                </div>

                <div class="col-md-6 mb-3">
                    <label for="id_departamento" class="form-label fw-semibold text-dark">Departamento <span class="text-danger">*</span></label>
                    <select class="form-select" id="id_departamento" name="id_departamento" required>
                        <option value="">Selecione o departamento...</option>
                        <?php if (!empty($departamentos)): ?>
                            <?php foreach ($departamentos as $dept): ?>
                                <option value="<?= $dept['id_departamento'] ?>" <?= (isset($cargo['id_departamento']) && $cargo['id_departamento'] == $dept['id_departamento']) ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($dept['nome']) ?>
                                </option>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </select>
                </div>
            </div>

            <div class="mb-4">
                <label for="descricao" class="form-label fw-semibold text-dark">Descrição e Responsabilidades</label>
                <textarea class="form-control" id="descricao" name="descricao" rows="4" placeholder="Descreva as atividades deste cargo..."><?= htmlspecialchars($cargo['descricao'] ?? '') ?></textarea>
            </div>

            <div class="d-flex justify-content-end gap-2 mt-2 pt-3 border-top">
                <a href="<?= BASE_URL ?>/cargos" class="btn btn-light border text-secondary px-4 fw-semibold shadow-sm">Cancelar</a>
                
                <button type="submit" class="btn text-white px-4 fw-semibold shadow-sm" style="background-color: var(--brand-color);">
                    <i class="fa-solid fa-save me-1"></i> Atualizar Cargo
                </button>
            </div>
            
        </form>
    </div>
</div>

<?php require_once ROOT_PATH . '/views/layouts/footer.php'; ?>