<?php
/**
 * views/agenda/dashboard.php
 * Salvar em: views/agenda/dashboard.php
 *
 * Variáveis esperadas do controller:
 *   $tarefas        — array de tarefas locais
 *   $eventos        — array de eventos do Google Calendar (pode ser [])
 *   $erroCalendario — string|null, mensagem de erro ao buscar eventos
 */
require_once ROOT_PATH . '/views/layouts/header.php';
?>

<div class="d-flex justify-content-between align-items-center mb-4 mt-2">
    <div>
        <h2 class="h4 fw-bold mb-0 text-dark">
            <i class="bi bi-calendar2-week me-2" style="color: var(--brand-color);"></i>Agenda
        </h2>
        <p class="text-muted small mb-0 mt-1">Suas tarefas locais e reuniões do Google Calendar em um só lugar.</p>
    </div>

    <a href="<?= BASE_URL ?>/agenda/criar"
       class="btn rounded-pill px-4 shadow-sm text-white fw-semibold"
       style="background-color: var(--brand-color); border-color: var(--brand-color);">
        <i class="bi bi-plus-lg me-2"></i>Nova Reunião / Tarefa
    </a>
</div>

<?php renderFlash(); ?>

<?php if ($erroCalendario): ?>
    <div class="alert alert-warning alert-dismissible fade show" role="alert">
        <i class="bi bi-exclamation-triangle-fill me-2"></i><?= htmlspecialchars($erroCalendario) ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>

<div class="row g-4">

    <!-- =====================================================================
         COLUNA ESQUERDA — Tarefas locais
    ====================================================================== -->
    <div class="col-lg-6">
        <div class="card border-0 shadow-sm h-100" style="border-radius: 12px; overflow: hidden;">
            <div class="card-header bg-white border-bottom d-flex align-items-center gap-2 py-3 px-4">
                <i class="bi bi-check2-square fs-5" style="color: var(--brand-color);"></i>
                <span class="fw-bold text-dark">Tarefas Locais</span>
                <span class="badge rounded-pill ms-auto bg-secondary bg-opacity-10 text-secondary">
                    <?= count($tarefas) ?>
                </span>
            </div>

            <div class="card-body p-0">
                <?php if (empty($tarefas)): ?>
                    <div class="text-center p-5 text-muted">
                        <i class="bi bi-card-checklist fs-1 text-secondary d-block mb-2"></i>
                        <p class="mb-0">Nenhuma tarefa encontrada.</p>
                    </div>
                <?php else: ?>
                    <ul class="list-group list-group-flush">
                        <?php foreach ($tarefas as $t):
                            $atrasada = !empty($t['data_vencimento'])
                                && $t['data_vencimento'] < date('Y-m-d')
                                && !in_array($t['status'], ['concluida', 'cancelada']);

                            $corStatus = match($t['status']) {
                                'concluida'    => 'success',
                                'em_andamento' => 'primary',
                                'cancelada'    => 'secondary',
                                default        => 'warning'
                            };
                        ?>
                        <li class="list-group-item border-0 border-bottom px-4 py-3">
                            <div class="d-flex align-items-start gap-3">
                                <div class="flex-grow-1 overflow-hidden">
                                    <a href="<?= BASE_URL ?>/tarefas/<?= $t['id_tarefa'] ?>"
                                       class="text-decoration-none fw-semibold text-dark d-block text-truncate">
                                        <?= htmlspecialchars($t['titulo']) ?>
                                    </a>
                                    <?php if (!empty($t['data_vencimento'])): ?>
                                        <small class="<?= $atrasada ? 'text-danger fw-bold' : 'text-muted' ?>">
                                            <i class="bi bi-calendar3 me-1"></i>
                                            <?= date('d/m/Y', strtotime($t['data_vencimento'])) ?>
                                            <?= $atrasada ? '<i class="bi bi-exclamation-triangle-fill ms-1"></i>' : '' ?>
                                        </small>
                                    <?php endif; ?>
                                </div>
                                <span class="badge bg-<?= $corStatus ?> bg-opacity-10 text-<?= $corStatus ?> border border-<?= $corStatus ?> rounded-pill px-2 flex-shrink-0" style="font-size: 0.68rem;">
                                    <?= ucfirst(str_replace('_', ' ', $t['status'])) ?>
                                </span>
                            </div>
                        </li>
                        <?php endforeach; ?>
                    </ul>
                <?php endif; ?>
            </div>

            <?php if (!empty($tarefas)): ?>
            <div class="card-footer bg-white border-top text-center py-2">
                <a href="<?= BASE_URL ?>/tarefas" class="small text-muted text-decoration-none">
                    Ver todas as tarefas <i class="bi bi-arrow-right ms-1"></i>
                </a>
            </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- =====================================================================
         COLUNA DIREITA — Eventos do Google Calendar
    ====================================================================== -->
    <div class="col-lg-6">
        <div class="card border-0 shadow-sm h-100" style="border-radius: 12px; overflow: hidden;">
            <div class="card-header bg-white border-bottom d-flex align-items-center gap-2 py-3 px-4">
                <!-- Ícone do Google Calendar em SVG inline para não depender de rede -->
                <svg width="20" height="20" viewBox="0 0 48 48" xmlns="http://www.w3.org/2000/svg">
                    <path fill="#fff" d="M35 9H13a4 4 0 0 0-4 4v22a4 4 0 0 0 4 4h22a4 4 0 0 0 4-4V13a4 4 0 0 0-4-4z"/>
                    <path fill="#1976D2" d="M35 40H13a5 5 0 0 1-5-5V13a5 5 0 0 1 5-5h22a5 5 0 0 1 5 5v22a5 5 0 0 1-5 5zm0-30H13a3 3 0 0 0-3 3v22a3 3 0 0 0 3 3h22a3 3 0 0 0 3-3V13a3 3 0 0 0-3-3z"/>
                    <path fill="#1976D2" d="M32 8h2v8h-2zm-18 0h2v8h-2z"/>
                    <path fill="#E53935" d="M9 18h30v4H9z"/>
                    <path fill="#1565C0" d="M22 28h4v4h-4z"/>
                </svg>
                <span class="fw-bold text-dark">Google Calendar</span>
                <span class="badge rounded-pill ms-auto bg-secondary bg-opacity-10 text-secondary">
                    próximos 30 dias
                </span>
            </div>

            <div class="card-body p-0">
                <?php if (empty($eventos)): ?>
                    <div class="text-center p-5 text-muted">
                        <i class="bi bi-calendar-x fs-1 text-secondary d-block mb-2"></i>
                        <p class="mb-0">Nenhum evento nos próximos 30 dias.</p>
                    </div>
                <?php else: ?>
                    <ul class="list-group list-group-flush">
                        <?php foreach ($eventos as $ev):
                            // Suporta tanto dateTime quanto date (eventos de dia inteiro)
                            $rawInicio = $ev['start']['dateTime'] ?? $ev['start']['date'] ?? null;
                            $rawFim    = $ev['end']['dateTime']   ?? $ev['end']['date']   ?? null;

                            $dtInicio = $rawInicio ? new DateTime($rawInicio) : null;
                            $dtFim    = $rawFim    ? new DateTime($rawFim)    : null;
                        ?>
                        <li class="list-group-item border-0 border-bottom px-4 py-3">
                            <div class="d-flex align-items-start gap-3">

                                <!-- Mini-calendário com dia/mês -->
                                <?php if ($dtInicio): ?>
                                <div class="flex-shrink-0 text-center bg-light rounded-2 px-2 py-1" style="min-width: 44px;">
                                    <div class="fw-bold text-dark lh-1" style="font-size: 1.1rem;">
                                        <?= $dtInicio->format('d') ?>
                                    </div>
                                    <div class="text-muted text-uppercase" style="font-size: 0.6rem; letter-spacing: 0.5px;">
                                        <?= $dtInicio->format('M') ?>
                                    </div>
                                </div>
                                <?php endif; ?>

                                <div class="flex-grow-1 overflow-hidden">
                                    <div class="fw-semibold text-dark text-truncate">
                                        <?= htmlspecialchars($ev['summary'] ?? '(Sem título)') ?>
                                    </div>
                                    <?php if ($dtInicio && $dtFim): ?>
                                        <small class="text-muted">
                                            <i class="bi bi-clock me-1"></i>
                                            <?= $dtInicio->format('H:i') ?> – <?= $dtFim->format('H:i') ?>
                                        </small>
                                    <?php endif; ?>
                                    <?php if (!empty($ev['description'])): ?>
                                        <p class="mb-0 text-muted text-truncate" style="font-size: 0.75rem;">
                                            <?= htmlspecialchars($ev['description']) ?>
                                        </p>
                                    <?php endif; ?>
                                </div>

                                <?php if (!empty($ev['htmlLink'])): ?>
                                <a href="<?= htmlspecialchars($ev['htmlLink']) ?>" target="_blank" rel="noopener"
                                   class="btn btn-light btn-sm border flex-shrink-0" title="Abrir no Google Calendar">
                                    <i class="bi bi-box-arrow-up-right"></i>
                                </a>
                                <?php endif; ?>
                            </div>
                        </li>
                        <?php endforeach; ?>
                    </ul>
                <?php endif; ?>
            </div>
        </div>
    </div><!-- /col -->

</div><!-- /row -->

<?php require_once ROOT_PATH . '/views/layouts/footer.php'; ?>