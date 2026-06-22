<?php require_once ROOT_PATH . '/views/layouts/header.php'; ?>

<div class="d-flex justify-content-between align-items-center mb-4 mt-2">
    <div>
        <h2 class="h4 fw-bold mb-0 text-dark">
            <i class="bi bi-bar-chart-line me-2" style="color: var(--brand-color);"></i>Relatórios
        </h2>
        <p class="text-muted small mb-0 mt-1">Análise de dados e exportação de relatórios gerenciais.</p>
    </div>

    <div class="d-flex gap-2">

        <?php
        // Monta a URL base de exportação conforme o tipo ativo.
        // CSV usa as rotas /relatorios/projetos e /relatorios/tarefas (que já tinham suporte).
        // Usuários e departamentos não têm rota própria de CSV, então ocultamos o botão.
        $rotaCsv = match ($tipo) {
            'projetos' => BASE_URL . '/relatorios/projetos?exportar=csv',
            'tarefas'  => BASE_URL . '/relatorios/tarefas?exportar=csv',
            default    => null, // usuários e departamentos não exportam CSV
        };
        ?>

        <?php if ($rotaCsv): ?>
        <a href="<?= $rotaCsv ?>"
           class="btn rounded-pill px-4 shadow-sm text-white fw-semibold"
           style="background-color: var(--brand-color); border-color: var(--brand-color); transition: all 0.3s;">
            <i class="bi bi-file-earmark-arrow-down me-2"></i>Exportar CSV
        </a>
        <?php endif; ?>

        <!-- Botão PDF — funciona para todos os tipos via ?exportar=pdf -->
        <a href="<?= BASE_URL ?>/relatorios?tipo=<?= htmlspecialchars($tipo) ?>&exportar=pdf<?= !empty($_GET['status'])           ? '&status='           . htmlspecialchars($_GET['status'])           : '' ?><?= !empty($_GET['id_departamento']) ? '&id_departamento='  . htmlspecialchars($_GET['id_departamento']) : '' ?><?= !empty($_GET['id_projeto'])      ? '&id_projeto='       . htmlspecialchars($_GET['id_projeto'])      : '' ?><?= !empty($_GET['prioridade'])      ? '&prioridade='       . htmlspecialchars($_GET['prioridade'])      : '' ?>"
           class="btn rounded-pill px-4 shadow-sm text-white fw-semibold"
           style="background-color: #dc3545; border-color: #dc3545; transition: all 0.3s;">
            <i class="bi bi-file-earmark-pdf me-2"></i>Exportar PDF
        </a>

    </div>
</div>

<ul class="nav nav-pills mb-4 pb-2 border-bottom">
    <li class="nav-item">
        <a class="nav-link <?= $tipo === 'projetos' ? 'active' : 'text-muted' ?>"
           <?= $tipo === 'projetos' ? 'style="background-color: var(--brand-color);"' : '' ?>
           href="<?= BASE_URL ?>/relatorios?tipo=projetos">Projetos</a>
    </li>
    <li class="nav-item">
        <a class="nav-link <?= $tipo === 'tarefas' ? 'active' : 'text-muted' ?>"
           <?= $tipo === 'tarefas' ? 'style="background-color: var(--brand-color);"' : '' ?>
           href="<?= BASE_URL ?>/relatorios?tipo=tarefas">Tarefas</a>
    </li>
    <li class="nav-item">
        <a class="nav-link <?= $tipo === 'usuarios' ? 'active' : 'text-muted' ?>"
           <?= $tipo === 'usuarios' ? 'style="background-color: var(--brand-color);"' : '' ?>
           href="<?= BASE_URL ?>/relatorios?tipo=usuarios">Usuários</a>
    </li>
    <li class="nav-item">
        <a class="nav-link <?= $tipo === 'departamentos' ? 'active' : 'text-muted' ?>"
           <?= $tipo === 'departamentos' ? 'style="background-color: var(--brand-color);"' : '' ?>
           href="<?= BASE_URL ?>/relatorios?tipo=departamentos">Departamentos</a>
    </li>
</ul>

<div class="card border-0 shadow-sm" style="border-radius: 12px; overflow: hidden;">
    <div class="card-body p-0">
        <?php if (empty($dados['linhas'])): ?>
            <div class="text-center p-5 text-muted">
                <div class="bg-light rounded-circle d-inline-flex align-items-center justify-content-center mb-3"
                     style="width: 80px; height: 80px;">
                    <i class="bi bi-inbox fs-1 text-secondary"></i>
                </div>
                <h5 class="fw-bold text-dark">Nenhum dado encontrado</h5>
                <p>Não há registros para exibir neste relatório.</p>
            </div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light text-muted small text-uppercase" style="letter-spacing: 0.5px;">
                        <tr>
                            <?php foreach ($dados['cabecalho'] as $th): ?>
                                <th class="fw-semibold px-3 py-3"><?= htmlspecialchars($th) ?></th>
                            <?php endforeach; ?>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($dados['linhas'] as $linha): ?>
                            <tr style="transition: background-color 0.2s;">
                                <?php foreach ($linha as $celula): ?>
                                    <td class="px-3 text-dark small"><?= htmlspecialchars((string)$celula) ?></td>
                                <?php endforeach; ?>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php require_once ROOT_PATH . '/views/layouts/footer.php'; ?>