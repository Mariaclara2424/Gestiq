<?php require_once ROOT_PATH . '/views/layouts/header.php'; ?>
<?php renderFlash(); ?>

<!-- BREADCRUMB -->
<nav aria-label="breadcrumb" class="mb-3">
  <ol class="breadcrumb">
    <li class="breadcrumb-item"><a href="<?= BASE_URL ?>/projetos">Projetos</a></li>
    <li class="breadcrumb-item"><a href="<?= BASE_URL ?>/projetos/<?= $dados['id_projeto'] ?>"><?= htmlspecialchars($dados['titulo']) ?></a></li>
    <li class="breadcrumb-item active">Editar</li>
  </ol>
</nav>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="h4 fw-bold mb-0 text-dark">
            <i class="bi bi-pencil-square me-2" style="color: var(--brand-color);"></i>Editar Projeto
        </h2>
        <p class="text-muted small mb-0 mt-1">Altere os dados do projeto abaixo.</p>
    </div>
    <a href="<?= BASE_URL ?>/projetos/<?= $dados['id_projeto'] ?>" class="btn btn-outline-secondary btn-sm rounded-pill px-3">
        <i class="bi bi-arrow-left me-1"></i>Voltar
    </a>
</div>

<?php if (!empty($erros)): ?>
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <i class="bi bi-exclamation-circle-fill me-2"></i>
        <strong>Corrija os seguintes erros:</strong>
        <ul class="mb-0 mt-1 ps-3">
            <?php foreach ($erros as $e): ?>
                <li><?= htmlspecialchars($e) ?></li>
            <?php endforeach; ?>
        </ul>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>

<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm" style="border-radius: 12px;">
            <div class="card-body p-4 p-md-5">

                <form method="POST" action="<?= BASE_URL ?>/projetos/<?= $dados['id_projeto'] ?>/editar" novalidate>

                    <!-- Título -->
                    <div class="mb-4">
                        <label for="titulo" class="form-label fw-semibold text-dark">
                            Título <span class="text-danger">*</span>
                        </label>
                        <input type="text" id="titulo" name="titulo" class="form-control"
                               placeholder="Título do projeto..."
                               value="<?= htmlspecialchars($dados['titulo'] ?? '') ?>"
                               required autofocus>
                    </div>

                    <!-- Descrição -->
                    <div class="mb-4">
                        <label for="descricao" class="form-label fw-semibold text-dark">Descrição</label>
                        <textarea id="descricao" name="descricao" rows="4" class="form-control"
                                  placeholder="Descreva o projeto..."><?= htmlspecialchars($dados['descricao'] ?? '') ?></textarea>
                    </div>

                    <!-- Departamento -->
                    <div class="mb-4">
                        <label for="id_departamento" class="form-label fw-semibold text-dark">Departamento</label>
                        <select id="id_departamento" name="id_departamento" class="form-select">
                            <option value="">Sem departamento</option>
                            <?php foreach ($departamentos as $d): ?>
                                <option value="<?= $d['id_departamento'] ?>"
                                    <?= (int)($dados['id_departamento'] ?? 0) === (int)$d['id_departamento'] ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($d['nome']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <!-- Responsável -->
                    <div class="mb-4">
                        <label for="id_responsavel" class="form-label fw-semibold text-dark">
                            Responsável <span class="text-danger">*</span>
                        </label>
                        <select id="id_responsavel" name="id_responsavel" class="form-select" required>
                            <option value="">Selecione...</option>
                            <?php foreach ($usuarios as $u): ?>
                                <option value="<?= $u['id_usuario'] ?>"
                                    <?= (int)($dados['id_responsavel'] ?? 0) === (int)$u['id_usuario'] ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($u['nome']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <!-- Datas -->
                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <label for="data_inicio" class="form-label fw-semibold text-dark">Data de Início</label>
                            <input type="date" id="data_inicio" name="data_inicio" class="form-control"
                                   value="<?= htmlspecialchars($dados['data_inicio'] ?? '') ?>">
                        </div>
                        <div class="col-md-6">
                            <label for="data_fim" class="form-label fw-semibold text-dark">Previsão de Conclusão</label>
                            <input type="date" id="data_fim" name="data_fim" class="form-control"
                                   value="<?= htmlspecialchars($dados['data_fim'] ?? '') ?>">
                        </div>
                    </div>

                    <!-- Status -->
                    <div class="mb-4">
                        <label for="status" class="form-label fw-semibold text-dark">
                            Status <span class="text-danger">*</span>
                        </label>
                        <select id="status" name="status" class="form-select" required>
                            <?php foreach ($status_list as $s): ?>
                                <option value="<?= $s ?>" <?= ($dados['status'] ?? '') === $s ? 'selected' : '' ?>>
                                    <?= ucfirst(str_replace('_', ' ', $s)) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <!-- Botões -->
                    <div class="d-flex justify-content-end gap-2 pt-2 border-top">
                        <a href="<?= BASE_URL ?>/projetos/<?= $dados['id_projeto'] ?>"
                           class="btn btn-outline-secondary rounded-pill px-4">Cancelar</a>
                        <button type="submit"
                                class="btn rounded-pill px-4 text-white fw-semibold shadow-sm"
                                style="background-color: var(--brand-color); border-color: var(--brand-color);">
                            <i class="bi bi-floppy me-2"></i>Salvar Alterações
                        </button>
                    </div>

                </form>

            </div>
        </div>
    </div>
</div>

<?php require_once ROOT_PATH . '/views/layouts/footer.php'; ?>