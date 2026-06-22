<?php require_once ROOT_PATH . '/views/layouts/header.php'; ?>

<?php renderFlash(); ?>

<!-- BREADCRUMB -->
<nav aria-label="breadcrumb" class="mb-3">
  <ol class="breadcrumb">
    <li class="breadcrumb-item"><a href="<?= BASE_URL ?>/tarefas">Tarefas</a></li>
    <?php if (!empty($dados['id_projeto']) && !empty($projeto)): ?>
    <li class="breadcrumb-item"><a href="<?= BASE_URL ?>/projetos/<?= $projeto['id_projeto'] ?>"><?= htmlspecialchars($projeto['titulo']) ?></a></li>
    <?php endif; ?>
    <li class="breadcrumb-item active">Nova Tarefa</li>
  </ol>
</nav>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="h4 fw-bold mb-0 text-dark">
            <i class="bi bi-plus-square me-2" style="color: var(--brand-color);"></i>Nova Tarefa
        </h2>
        <p class="text-muted small mb-0 mt-1">Preencha os dados para criar uma nova tarefa.</p>
    </div>
    <a href="<?= BASE_URL ?>/tarefas" class="btn btn-outline-secondary btn-sm rounded-pill px-3">
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

                <form method="POST" action="<?= BASE_URL ?>/tarefas/criar" novalidate>

                    <!-- Título -->
                    <div class="mb-4">
                        <label for="titulo" class="form-label fw-semibold text-dark">
                            Título <span class="text-danger">*</span>
                        </label>
                        <input
                            type="text"
                            id="titulo"
                            name="titulo"
                            class="form-control"
                            placeholder="Título da tarefa..."
                            value="<?= htmlspecialchars($dados['titulo'] ?? '') ?>"
                            required
                            autofocus
                        >
                    </div>

                    <!-- Descrição -->
                    <div class="mb-4">
                        <label for="descricao" class="form-label fw-semibold text-dark">Descrição</label>
                        <textarea
                            id="descricao"
                            name="descricao"
                            rows="4"
                            class="form-control"
                            placeholder="Descreva a tarefa..."
                        ><?= htmlspecialchars($dados['descricao'] ?? '') ?></textarea>
                    </div>

                    <!-- Projeto -->
                    <div class="mb-4">
                        <label for="id_projeto" class="form-label fw-semibold text-dark">Projeto</label>
                        <select id="id_projeto" name="id_projeto" class="form-select">
                            <option value="">Sem projeto</option>
                            <?php foreach ($projetos as $p): ?>
                                <option value="<?= $p['id_projeto'] ?>" <?= (int)($dados['id_projeto'] ?? 0) === (int)$p['id_projeto'] ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($p['titulo']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <!-- Responsável -->
                    <div class="mb-4">
                        <label for="id_responsavel" class="form-label fw-semibold text-dark">Responsável</label>
                        <select id="id_responsavel" name="id_responsavel" class="form-select">
                            <option value="">Não atribuído</option>
                            <?php foreach ($usuarios as $u): ?>
                                <option value="<?= $u['id_usuario'] ?>" <?= (int)($dados['id_responsavel'] ?? 0) === (int)$u['id_usuario'] ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($u['nome']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <!-- Data de Vencimento -->
                    <div class="mb-4">
                        <label for="data_vencimento" class="form-label fw-semibold text-dark">Data de Vencimento</label>
                        <input
                            type="date"
                            id="data_vencimento"
                            name="data_vencimento"
                            class="form-control"
                            value="<?= htmlspecialchars($dados['data_vencimento'] ?? '') ?>"
                        >
                    </div>

                    <!-- Status e Prioridade lado a lado -->
                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <label for="status" class="form-label fw-semibold text-dark">
                                Status <span class="text-danger">*</span>
                            </label>
                            <select id="status" name="status" class="form-select" required>
                                <?php foreach ($status as $s): ?>
                                    <option value="<?= $s ?>" <?= ($dados['status'] ?? 'pendente') === $s ? 'selected' : '' ?>>
                                        <?= ucfirst(str_replace('_', ' ', $s)) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label for="prioridade" class="form-label fw-semibold text-dark">
                                Prioridade <span class="text-danger">*</span>
                            </label>
                            <select id="prioridade" name="prioridade" class="form-select" required>
                                <?php foreach ($prioridades as $p): ?>
                                    <option value="<?= $p ?>" <?= ($dados['prioridade'] ?? 'media') === $p ? 'selected' : '' ?>>
                                        <?= ucfirst($p) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>

                    <!-- Botões -->
                    <div class="d-flex justify-content-end gap-2 pt-2 border-top">
                        <a href="<?= BASE_URL ?>/tarefas" class="btn btn-outline-secondary rounded-pill px-4">
                            Cancelar
                        </a>
                        <button type="submit"
                                class="btn rounded-pill px-4 text-white fw-semibold shadow-sm"
                                style="background-color: var(--brand-color); border-color: var(--brand-color);">
                            <i class="bi bi-plus-lg me-2"></i>Criar Tarefa
                        </button>
                    </div>

                </form>

            </div>
        </div>
    </div>
</div>

<?php require_once ROOT_PATH . '/views/layouts/footer.php'; ?>