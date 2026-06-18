<?php require_once ROOT_PATH . '/views/layouts/header.php'; ?>

<div class="row justify-content-center">
  <div class="col-md-8">
    <div class="card border-0 shadow-sm">
      <div class="card-header bg-transparent">
        <h5 class="mb-0 fw-bold">Editar Comentário</h5>
      </div>
      <div class="card-body">
        <form method="POST" action="<?= BASE_URL ?>/comentarios/<?= $comentario['id_comentario'] ?>/editar">
          <div class="mb-3">
            <label class="form-label">Comentário</label>
            <textarea name="texto" class="form-control" rows="5" required><?= htmlspecialchars($comentario['texto']) ?></textarea>
          </div>
          <div class="d-flex gap-2">
            <button class="btn btn-primary">Salvar</button>
            <a href="<?= BASE_URL ?>/tarefas/<?= $comentario['id_tarefa'] ?>" class="btn btn-outline-secondary">Cancelar</a>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>

<?php require_once ROOT_PATH . '/views/layouts/footer.php'; ?>
