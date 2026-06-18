<?php require_once ROOT_PATH . '/views/layouts/header.php'; ?>
<?php renderFlash(); ?>

<!-- BREADCRUMB -->
<nav aria-label="breadcrumb" class="mb-3">
  <ol class="breadcrumb">
    <li class="breadcrumb-item"><a href="<?= BASE_URL ?>/projetos">Projetos</a></li>
    <li class="breadcrumb-item"><a href="<?= BASE_URL ?>/projetos/<?= $tarefa['id_projeto'] ?>"><?= htmlspecialchars($tarefa['projeto_titulo']) ?></a></li>
    <li class="breadcrumb-item active"><?= htmlspecialchars($tarefa['titulo']) ?></li>
  </ol>
</nav>

<div class="row g-4">
  <!-- DETALHE DA TAREFA -->
  <div class="col-lg-8">
    <div class="card border-0 shadow-sm mb-4">
      <div class="card-header bg-transparent d-flex justify-content-between align-items-center">
        <h5 class="mb-0 fw-bold"><?= htmlspecialchars($tarefa['titulo']) ?></h5>
        <?php if (Auth::can('admin', 'gestor') || (int)Auth::user()['id'] === (int)$tarefa['id_responsavel']): ?>
        <div class="d-flex gap-2">
          <a href="<?= BASE_URL ?>/tarefas/<?= $tarefa['id_tarefa'] ?>/editar" class="btn btn-outline-secondary btn-sm"><i class="bi bi-pencil"></i></a>
          <?php if (Auth::can('admin', 'gestor')): ?>
          <form method="POST" action="<?= BASE_URL ?>/tarefas/<?= $tarefa['id_tarefa'] ?>/excluir" onsubmit="return confirm('Excluir esta tarefa?')">
            <button class="btn btn-outline-danger btn-sm"><i class="bi bi-trash"></i></button>
          </form>
          <?php endif; ?>
        </div>
        <?php endif; ?>
      </div>
      <div class="card-body">
        <div class="row g-3 mb-3">
          <div class="col-6 col-md-3">
            <small class="text-muted d-block">Status</small>
            <?php
              $corStatus = match($tarefa['status']) {
                'concluida'    => 'success',
                'em_andamento' => 'primary',
                'cancelada'    => 'secondary',
                default        => 'warning',
              };
            ?>
            <span class="badge bg-<?= $corStatus ?>"><?= ucfirst(str_replace('_',' ',$tarefa['status'])) ?></span>
          </div>
          <div class="col-6 col-md-3">
            <small class="text-muted d-block">Prioridade</small>
            <?php $corPrior = match($tarefa['prioridade']) { 'critica'=>'danger','alta'=>'warning','media'=>'info',default=>'secondary' }; ?>
            <span class="badge bg-<?= $corPrior ?>"><?= ucfirst($tarefa['prioridade']) ?></span>
          </div>
          <div class="col-6 col-md-3">
            <small class="text-muted d-block">Responsável</small>
            <span><?= htmlspecialchars($tarefa['responsavel_nome'] ?? '—') ?></span>
          </div>
          <div class="col-6 col-md-3">
            <small class="text-muted d-block">Vencimento</small>
            <?php if ($tarefa['data_vencimento']): ?>
              <?php $atrasada = $tarefa['data_vencimento'] < date('Y-m-d') && !in_array($tarefa['status'],['concluida','cancelada']); ?>
              <span class="<?= $atrasada ? 'text-danger fw-semibold' : '' ?>">
                <?= date('d/m/Y', strtotime($tarefa['data_vencimento'])) ?>
                <?= $atrasada ? '<i class="bi bi-exclamation-circle"></i>' : '' ?>
              </span>
            <?php else: ?>—<?php endif; ?>
          </div>
        </div>

        <?php if ($tarefa['descricao']): ?>
        <hr>
        <h6 class="fw-semibold">Descrição</h6>
        <p class="mb-0"><?= nl2br(htmlspecialchars($tarefa['descricao'])) ?></p>
        <?php endif; ?>
      </div>
    </div>

    <!-- ALTERAR STATUS RÁPIDO -->
    <div class="card border-0 shadow-sm mb-4">
      <div class="card-body">
        <form method="POST" action="<?= BASE_URL ?>/tarefas/<?= $tarefa['id_tarefa'] ?>/status" class="d-flex gap-2 align-items-center flex-wrap">
          <label class="form-label mb-0 fw-semibold">Alterar status:</label>
          <select name="status" class="form-select form-select-sm w-auto">
            <?php foreach ($status as $s): ?>
            <option value="<?= $s ?>" <?= $s === $tarefa['status'] ? 'selected' : '' ?>><?= ucfirst(str_replace('_',' ',$s)) ?></option>
            <?php endforeach; ?>
          </select>
          <button class="btn btn-primary btn-sm">Salvar</button>
        </form>
      </div>
    </div>

    <!-- COMENTÁRIOS -->
    <div class="card border-0 shadow-sm">
      <div class="card-header bg-transparent fw-semibold">
        <i class="bi bi-chat-left-text me-2"></i>Comentários (<?= count($comentarios) ?>)
      </div>
      <div class="card-body">
        <?php if (empty($comentarios)): ?>
          <p class="text-muted mb-3">Sem comentários ainda. Seja o primeiro!</p>
        <?php else: ?>
        <?php foreach ($comentarios as $c): ?>
        <div class="d-flex mb-3">
          <div class="flex-shrink-0 bg-primary bg-opacity-10 rounded-circle d-flex align-items-center justify-content-center me-3" style="width:40px;height:40px">
            <span class="text-primary fw-bold"><?= mb_strtoupper(mb_substr($c['autor_nome'], 0, 1)) ?></span>
          </div>
          <div class="flex-grow-1">
            <div class="d-flex justify-content-between align-items-start">
              <div>
                <span class="fw-semibold small"><?= htmlspecialchars($c['autor_nome']) ?></span>
                <span class="text-muted ms-2" style="font-size:.75rem"><?= date('d/m/Y H:i', strtotime($c['created_at'])) ?></span>
                <?php if ($c['updated_at'] !== $c['created_at']): ?>
                  <span class="text-muted ms-1" style="font-size:.7rem">(editado)</span>
                <?php endif; ?>
              </div>
              <?php $uid = (int)Auth::user()['id']; $isOwner = $uid === (int)$c['id_usuario']; ?>
              <?php if ($isOwner || Auth::can('admin')): ?>
              <div class="d-flex gap-1">
                <a href="<?= BASE_URL ?>/comentarios/<?= $c['id_comentario'] ?>/editar" class="btn btn-link btn-sm p-0 text-muted"><i class="bi bi-pencil"></i></a>
                <form method="POST" action="<?= BASE_URL ?>/comentarios/<?= $c['id_comentario'] ?>/excluir" onsubmit="return confirm('Excluir comentário?')">
                  <button class="btn btn-link btn-sm p-0 text-danger"><i class="bi bi-trash"></i></button>
                </form>
              </div>
              <?php endif; ?>
            </div>
            <p class="mb-0 mt-1 small"><?= nl2br(htmlspecialchars($c['texto'])) ?></p>
          </div>
        </div>
        <?php endforeach; ?>
        <?php endif; ?>

        <!-- NOVO COMENTÁRIO -->
        <hr>
        <form method="POST" action="<?= BASE_URL ?>/comentarios/<?= $tarefa['id_tarefa'] ?>/novo">
          <div class="mb-2">
            <textarea name="texto" class="form-control form-control-sm" rows="3" placeholder="Escreva um comentário..." required></textarea>
          </div>
          <button class="btn btn-primary btn-sm"><i class="bi bi-send me-1"></i>Comentar</button>
        </form>
      </div>
    </div>
  </div>

  <!-- SIDEBAR -->
  <div class="col-lg-4">
    <div class="card border-0 shadow-sm">
      <div class="card-header bg-transparent fw-semibold">Informações</div>
      <ul class="list-group list-group-flush small">
        <li class="list-group-item d-flex justify-content-between">
          <span class="text-muted">Projeto</span>
          <a href="<?= BASE_URL ?>/projetos/<?= $tarefa['id_projeto'] ?>"><?= htmlspecialchars($tarefa['projeto_titulo']) ?></a>
        </li>
        <li class="list-group-item d-flex justify-content-between">
          <span class="text-muted">Criado em</span>
          <span><?= date('d/m/Y', strtotime($tarefa['created_at'])) ?></span>
        </li>
        <li class="list-group-item d-flex justify-content-between">
          <span class="text-muted">Atualizado em</span>
          <span><?= date('d/m/Y H:i', strtotime($tarefa['updated_at'])) ?></span>
        </li>
      </ul>
    </div>
  </div>
</div>

<?php require_once ROOT_PATH . '/views/layouts/footer.php'; ?>
