<?php require_once ROOT_PATH . '/config/config.php'; ?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Criar Conta — GestIQ</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
  
  <style>
    body {
        background-color: #f4f6f9;
    }
    /* Padronizando com o tema Roxo Rosado do Login */
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
    /* Estilizando os ícones dentro do campo com a nova cor */
    .input-group-text {
        background-color: #f8f9fa;
        border-right: none;
        border-radius: 8px 0 0 8px;
        color: #c026d3; 
    }
    .form-control {
        border-left: none;
        border-radius: 0 8px 8px 0;
        background-color: #f8f9fa;
        padding: 10px 15px;
    }
    .form-control:focus {
        box-shadow: none;
        background-color: #fff;
        border-color: #dee2e6;
    }
    /* Efeito de brilho roxo rosado ao clicar no campo */
    .input-group:focus-within .input-group-text, 
    .input-group:focus-within .form-control {
        background-color: #fff;
        border-color: #c026d3;
    }
  </style>
</head>

<body class="d-flex align-items-center justify-content-center" style="min-height: 100vh;">

<div class="container d-flex justify-content-center">
  <div class="card border-0 shadow-lg" style="border-radius: 1.5rem; overflow: hidden; max-width: 950px; width: 100%;">
    <div class="row g-0">
      
      <div class="col-md-6 d-none d-md-flex align-items-center justify-content-center p-5" style="background-color: #fdfdfd;">
        <img src="<?= BASE_URL ?>/public/img/logistica.png" alt="Ilustração Logística" class="img-fluid" style="max-width: 90%;">
      </div>

      <div class="col-md-6 p-5">
        <h3 class="fw-bold mb-4 text-center text-dark" style="font-size: 1.5rem;">Criar Conta</h3>
        
        <?php if (!empty($erro)): ?>
        <div class="alert alert-danger alert-dismissible py-2" style="font-size: 0.9rem;">
          <i class="bi bi-exclamation-circle me-2"></i><?= htmlspecialchars($erro) ?>
          <button type="button" class="btn-close mt-1" data-bs-dismiss="alert"></button>
        </div>
        <?php endif; ?>
        
        <form method="POST" action="<?= BASE_URL ?>/cadastro">
          
          <div class="input-group mb-3 shadow-sm rounded-3">
            <span class="input-group-text"><i class="bi bi-person"></i></span>
            <input type="text" name="nome" class="form-control" placeholder="Usuário" required autofocus>
          </div>
          
          <div class="input-group mb-3 shadow-sm rounded-3">
            <span class="input-group-text"><i class="bi bi-at"></i></span>
            <input type="email" name="email" class="form-control" placeholder="Email" required>
          </div>
          
          <div class="input-group mb-3 shadow-sm rounded-3">
            <span class="input-group-text"><i class="bi bi-lock"></i></span>
            <input type="password" name="senha" class="form-control" placeholder="Senha" required>
          </div>

          <div class="input-group mb-4 shadow-sm rounded-3">
            <span class="input-group-text"><i class="bi bi-shield-lock"></i></span>
            <input type="password" name="confirmar_senha" class="form-control" placeholder="Confirmar Senha" required>
          </div>
          
          <button type="submit" class="btn btn-custom w-100 fw-semibold mb-4 shadow-sm">Cadastrar</button>
          
          <div class="text-center">
            <span class="text-muted" style="font-size: 0.9rem;">Já tem uma conta?</span>
            <a href="<?= BASE_URL ?>/login" class="text-decoration-none text-brand fw-semibold ms-1" style="font-size: 0.9rem;">Entrar</a>
          </div>
          
        </form>
      </div>
      
    </div>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>