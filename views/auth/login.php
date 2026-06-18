<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Login — GestIQ</title>
  
  <link rel="icon" href="/gestiq/public/img/favicon.png" type="image/png">
  <link rel="shortcut icon" href="/gestiq/public/img/favicon.png" type="image/png">

  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
  
  <style>
    body {
        background-color: #f4f6f9;
    }
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
        border-radius: 6px;
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
        border-radius: 6px;
        padding: 10px 15px;
        font-size: 0.95rem;
    }
    .form-control:focus {
        box-shadow: none;
        background-color: #fff;
        border-color: #c026d3;
    }
    .form-label {
        font-size: 0.8rem;
        color: #6c757d;
        margin-bottom: 0.2rem;
    }
  </style>
</head>
<body class="d-flex align-items-center justify-content-center" style="min-height: 100vh;">

<div class="container d-flex justify-content-center">
  <div class="card border-0 shadow-lg" style="border-radius: 1.5rem; overflow: hidden; max-width: 950px; width: 100%;">
    <div class="row g-0">
      <div class="col-md-6 p-5 d-flex flex-column justify-content-center">
        <div class="mb-4">
          <h1 class="h5 fw-bold text-brand mb-3">
            <i class="bi bi-kanban me-2"></i>GESTIQ
          </h1>
          <h2 class="h4 fw-bold text-main" style="line-height: 1.3;">
            Olá,<br>Bom ter você de volta
          </h2>
        </div>

        <?php /* ✅ BUG 3 CORRIGIDO: bloco de alertas adicionado — a view nunca exibia $erro ou $sucesso */ ?>

        <?php if (!empty($erro)): ?>
          <div class="alert alert-danger d-flex align-items-center gap-2 py-2 mb-3" role="alert">
            <i class="bi bi-exclamation-triangle-fill flex-shrink-0"></i>
            <span><?= htmlspecialchars($erro) ?></span>
          </div>
        <?php endif; ?>

        <?php if (!empty($sucesso)): ?>
          <div class="alert alert-success d-flex align-items-center gap-2 py-2 mb-3" role="alert">
            <i class="bi bi-check-circle-fill flex-shrink-0"></i>
            <span><?= htmlspecialchars($sucesso) ?></span>
          </div>
        <?php endif; ?>
        
        <form method="POST" action="<?= BASE_URL ?>/login">
          <div class="mb-3">
            <label class="form-label">Usuário (E-mail):</label>
            <input type="email" name="email" class="form-control" placeholder="admin@gestiq.com" required autofocus>
          </div>
          
          <div class="mb-4">
            <label class="form-label">Senha:</label>
            <input type="password" name="senha" class="form-control" placeholder="••••••••••••" required>
          </div>
          
          <button type="submit" class="btn btn-custom w-100 fw-semibold mb-3 shadow-sm">Login</button>
          
          <div class="text-center mb-4">
            <a href="<?= BASE_URL ?>/recuperar-senha" class="text-decoration-none text-muted" style="font-size: 0.85rem;" tabindex="-1">Esqueci a senha</a>
          </div>

          <div class="text-center pt-3 border-top">
            <span class="text-muted" style="font-size: 0.85rem;">Não possui um usuário?</span>
            <a href="<?= BASE_URL ?>/cadastro" class="text-decoration-none fw-semibold ms-1 text-brand" style="font-size: 0.85rem;">Cadastre-se</a>
          </div>
        </form>
      </div>

      <div class="col-md-6 d-none d-md-flex align-items-center justify-content-center p-5" style="background-color: #fdfdfd;">
        <img src="https://illustrations.popsy.co/pink/work-from-home.svg" alt="Ilustração de Login" class="img-fluid" style="max-width: 90%;">
      </div>
    </div>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>