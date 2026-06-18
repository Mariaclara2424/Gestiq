<?php require_once ROOT_PATH . '/config/config.php'; ?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Recuperar Senha — GestIQ</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
  
  <style>
    body {
        background-color: #f4f6f9;
    }
    /* Cores do tema Roxo Rosado (Fuchsia) */
    .text-main {
        color: #2d1633;
    }
    .text-brand {
        color: #c026d3 !important; 
    }
    .btn-custom {
        background-color: #c026d3; 
        border-color: #c026d3;
        color: white;
        border-radius: 8px;
        padding: 10px;
        transition: all 0.3s ease;
    }
    .btn-custom:hover {
        background-color: #a21caf; 
        border-color: #a21caf;
        color: white;
    }
    .form-control {
        background-color: #f8f9fa;
        border: 1px solid #dee2e6;
        border-radius: 8px;
        padding: 10px 15px;
        font-size: 0.95rem;
    }
    .form-control:focus {
        box-shadow: none;
        background-color: #fff;
        border-color: #c026d3;
    }
    .form-label {
        font-size: 0.85rem;
        color: #6c757d;
        margin-bottom: 0.3rem;
    }
  </style>
</head>

<body class="d-flex align-items-center justify-content-center" style="min-height: 100vh;">

<div class="container d-flex justify-content-center">
  <div class="card border-0 shadow-lg" style="border-radius: 1.5rem; max-width: 450px; width: 100%;">
    <div class="card-body p-5">
      
      <div class="text-center mb-4">
        <i class="bi bi-key text-brand" style="font-size: 3rem;"></i>
        <h2 class="h4 fw-bold mt-3 text-main">Esqueceu a senha?</h2>
        <p class="text-muted small px-2">Digite o e-mail associado ao seu usuário para receber as instruções de recuperação.</p>
      </div>
      
      <?php if (!empty($erro)): ?>
      <div class="alert alert-danger alert-dismissible py-2" style="font-size: 0.9rem;">
        <i class="bi bi-exclamation-circle me-2"></i><?= htmlspecialchars($erro) ?>
        <button type="button" class="btn-close mt-1" data-bs-dismiss="alert"></button>
      </div>
      <?php endif; ?>
      
      <?php if (!empty($sucesso)): ?>
      <div class="alert alert-success alert-dismissible py-2" style="font-size: 0.9rem;">
        <i class="bi bi-check-circle me-2"></i><?= htmlspecialchars($sucesso) ?>
        <button type="button" class="btn-close mt-1" data-bs-dismiss="alert"></button>
      </div>
      <?php endif; ?>
      
      <form method="POST" action="<?= BASE_URL ?>/recuperar-senha">
        <div class="mb-4">
          <label class="form-label fw-medium">E-mail</label>
          <input type="email" name="email" class="form-control" placeholder="Seu e-mail cadastrado" required autofocus>
        </div>
        
        <button type="submit" class="btn btn-custom w-100 fw-semibold mb-4 shadow-sm">Enviar link de recuperação</button>
        
        <div class="text-center border-top pt-4">
          <a href="<?= BASE_URL ?>/login" class="text-decoration-none text-brand fw-medium" style="font-size: 0.9rem;">
            <i class="bi bi-arrow-left me-1"></i>Voltar para o login
          </a>
        </div>
        
      </form>
    </div>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>