<?php
/**
 * views/agenda/criar.php
 * Salvar em: views/agenda/criar.php
 *
 * Variáveis esperadas do controller:
 * $erros — array de mensagens de validação (pode ser [])
 * $dados — array com os valores atuais dos campos (para reexibir após erro)
 * $projetos — array com a lista de projetos do banco de dados
 * $usuarios — array com a lista de usuários para convite
 */
require_once ROOT_PATH . '/views/layouts/header.php';
?>

<div class="d-flex justify-content-between align-items-center mb-4 mt-2">
    <div class="d-flex align-items-center gap-2">
        <a href="<?= BASE_URL ?>/agenda" class="btn btn-light btn-sm border rounded-pill" title="Voltar para Agenda">
            <i class="bi bi-calendar-event me-1"></i>Agenda
        </a>
        <a href="<?= BASE_URL ?>/dashboard" class="btn btn-dark btn-sm rounded-pill shadow-sm px-3" title="Voltar para o Painel Geral">
            <i class="bi bi-house-door me-1"></i>Painel Geral
        </a>
    </div>
    
    <div class="text-end">
        <h2 class="h4 fw-bold mb-0 text-dark">Nova Reunião</h2>
        <p class="text-muted small mb-0 mt-1">Sincronização automática com o Google Calendar</p>
    </div>
</div>

<?php if (!empty($erros)): ?>
    <div class="alert alert-danger border-0 shadow-sm mb-4" style="border-radius: 12px;">
        <div class="d-flex align-items-center mb-2">
            <i class="bi bi-exclamation-triangle-fill fs-5 me-2"></i> 
            <strong class="mb-0">Corrija os erros para aplicar o agendamento:</strong>
        </div>
        <ul class="mb-0 ps-4">
            <?php foreach ($erros as $e): ?>
                <li><?= htmlspecialchars($e) ?></li>
            <?php endforeach; ?>
        </ul>
    </div>
<?php endif; ?>

<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="card border-0 shadow-lg" style="border-radius: 15px;">
            <div class="card-body p-4 p-md-5">

                <form method="POST" action="<?= BASE_URL ?>/agenda/criar" novalidate>

                    <div class="mb-4">
                        <label for="titulo" class="form-label fw-bold text-muted small">TÍTULO DO COMPROMISSO <span class="text-danger">*</span></label>
                        <input
                            type="text"
                            id="titulo"
                            name="titulo"
                            class="form-control form-control-lg border-0 bg-light <?= !empty($erros) && empty($dados['titulo']) ? 'is-invalid' : '' ?>"
                            style="border-radius: 10px;"
                            placeholder="Ex.: Reunião de alinhamento de sprint"
                            value="<?= htmlspecialchars($dados['titulo'] ?? '') ?>"
                            required
                            autofocus
                        >
                    </div>

                    <div class="mb-4">
                        <label for="id_projeto" class="form-label fw-bold text-muted small">VINCULAR AO PROJETO <span class="text-danger">*</span></label>
                        <select 
                            id="id_projeto" 
                            name="id_projeto" 
                            class="form-select border-0 bg-light <?= !empty($erros) && empty($dados['id_projeto']) ? 'is-invalid' : '' ?>" 
                            style="border-radius: 10px; height: 48px;"
                            required
                        >
                            <option value="">Selecione um projeto...</option>
                            <?php if (!empty($projetos)): ?>
                                <?php foreach ($projetos as $p): ?>
                                    <option value="<?= htmlspecialchars($p['id_projeto']) ?>" 
                                        <?= (isset($dados['id_projeto']) && $dados['id_projeto'] == $p['id_projeto']) ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($p['nome']) ?>
                                    </option>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <option value="" disabled>Nenhum projeto encontrado no sistema</option>
                            <?php endif; ?>
                        </select>
                    </div>

                    <div class="mb-4">
                        <label for="participantes" class="form-label fw-bold text-muted small">CONVIDAR PESSOAS (E-MAIL)</label>
                        <select 
                            id="participantes" 
                            name="participantes[]" 
                            class="form-select border-0 bg-light" 
                            multiple
                            style="border-radius: 10px; height: 120px;"
                        >
                            <?php if (!empty($usuarios)): ?>
                                <?php foreach ($usuarios as $u): ?>
                                    <option value="<?= htmlspecialchars($u['email']) ?>">
                                        <?= htmlspecialchars($u['nome']) ?> (<?= htmlspecialchars($u['email']) ?>)
                                    </option>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <option value="" disabled>Nenhum usuário com e-mail cadastrado.</option>
                            <?php endif; ?>
                        </select>
                        <div class="form-text mt-2 text-muted" style="font-size: 11px;">
                            <i class="bi bi-info-circle me-1"></i> Segure a tecla <strong>CTRL</strong> (ou CMD no Mac) para selecionar mais de uma pessoa. O Google enviará o convite para eles.
                        </div>
                    </div>

                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <label for="data_inicio" class="form-label fw-bold text-muted small">DATA INÍCIO <span class="text-danger">*</span></label>
                            <input
                                type="datetime-local"
                                id="data_inicio"
                                name="data_inicio"
                                class="form-control border-0 bg-light <?= !empty($erros) && empty($dados['data_inicio']) ? 'is-invalid' : '' ?>"
                                style="border-radius: 10px; height: 48px;"
                                value="<?= htmlspecialchars($dados['data_inicio'] ?? '') ?>"
                                required
                            >
                        </div>
                        <div class="col-md-6">
                            <label for="data_fim" class="form-label fw-bold text-muted small">DATA TÉRMINO <span class="text-danger">*</span></label>
                            <input
                                type="datetime-local"
                                id="data_fim"
                                name="data_fim"
                                class="form-control border-0 bg-light <?= !empty($erros) && empty($dados['data_fim']) ? 'is-invalid' : '' ?>"
                                style="border-radius: 10px; height: 48px;"
                                value="<?= htmlspecialchars($dados['data_fim'] ?? '') ?>"
                                required
                            >
                        </div>
                    </div>

                    <div class="mb-5">
                        <label for="descricao" class="form-label fw-bold text-muted small">PAUTA / OBSERVAÇÕES</label>
                        <textarea
                            id="descricao"
                            name="descricao"
                            rows="3"
                            class="form-control border-0 bg-light"
                            style="border-radius: 10px;"
                            placeholder="Pauta, link de videochamada, observações…"
                        ><?= htmlspecialchars($dados['descricao'] ?? '') ?></textarea>
                    </div>

                    <div class="d-flex justify-content-between align-items-center pt-4 border-top">
                        <a href="<?= BASE_URL ?>/agenda" class="text-decoration-none text-muted fw-bold">Cancelar</a>
                        
                        <button type="submit" class="btn btn-lg px-5 text-white fw-bold shadow" 
                                style="background-color: #1a233a; border-radius: 30px; transition: 0.3s;">
                            <i class="bi bi-check-circle me-2"></i>APLICAR AGENDAMENTO
                        </button>
                    </div>

                </form>

            </div>
        </div>
    </div>
</div>

<script>
document.getElementById('data_inicio').addEventListener('change', function () {
    const fim = document.getElementById('data_fim');
    if (!fim.value || fim.value <= this.value) {
        const dt = new Date(this.value);
        dt.setHours(dt.getHours() + 1);
        const pad = n => String(n).padStart(2, '0');
        fim.value = `${dt.getFullYear()}-${pad(dt.getMonth()+1)}-${pad(dt.getDate())}T${pad(dt.getHours())}:${pad(dt.getMinutes())}`;
    }
});
</script>

<?php require_once ROOT_PATH . '/views/layouts/footer.php'; ?>