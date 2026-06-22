<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>GESTIQ</title>
    
    <link rel="icon" href="<?= BASE_URL ?>/public/img/favicon.png" type="image/png">

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    
    <link rel="stylesheet" href="<?= BASE_URL ?>/public/css/gestiq-dashboard.css">

    <style>
        :root {
            /* Mantém a cor original do sistema intacta */
            --brand-color: #1a233a; 
        }

        /* ====================================================================
           MÁGICA GLOBAL: ARRUMA O CONTRASTE DE TODAS AS TELAS AUTOMATICAMENTE
           ==================================================================== */
        
        /* 1. Busca qualquer barra que use a cor principal de fundo e deixa o texto branco */
        [style*="background-color: var(--brand-color)"],
        [style*="background: var(--brand-color)"] {
            color: #ffffff !important;
        }

        /* 2. Força que todos os títulos (h1, h2, etc) e textos lá dentro fiquem brancos */
        [style*="background-color: var(--brand-color)"] h1,
        [style*="background-color: var(--brand-color)"] h2,
        [style*="background-color: var(--brand-color)"] h3,
        [style*="background-color: var(--brand-color)"] h4,
        [style*="background-color: var(--brand-color)"] h5,
        [style*="background-color: var(--brand-color)"] h6,
        [style*="background-color: var(--brand-color)"] p,
        [style*="background-color: var(--brand-color)"] span,
        [style*="background: var(--brand-color)"] h1,
        [style*="background: var(--brand-color)"] h2,
        [style*="background: var(--brand-color)"] h3,
        [style*="background: var(--brand-color)"] h4,
        [style*="background: var(--brand-color)"] p,
        [style*="background: var(--brand-color)"] span {
            color: #ffffff !important;
        }

        /* 3. Conserta os botões "+ Novo..." para não sumirem no fundo escuro */
        [style*="background-color: var(--brand-color)"] .btn,
        [style*="background: var(--brand-color)"] .btn {
            color: #ffffff !important;
            border: 1px solid rgba(255, 255, 255, 0.4) !important;
            background-color: transparent;
        }
        
        /* 4. Efeito elegante ao passar o mouse por cima do botão */
        [style*="background-color: var(--brand-color)"] .btn:hover,
        [style*="background: var(--brand-color)"] .btn:hover {
            background-color: rgba(255, 255, 255, 0.15) !important;
            color: #ffffff !important;
        }
    </style>
</head>
<body>
<?php if (function_exists('renderFlash')) renderFlash(); ?>