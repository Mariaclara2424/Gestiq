<?php
/**
 * DashboardController.php
 * Controller do painel principal do GESTIQ.
 */

require_once ROOT_PATH . '/helpers/Auth.php';
require_once ROOT_PATH . '/models/Projeto.php';

class DashboardController
{
    private $model;

    public function __construct()
    {
        Auth::check();
        $this->model = new ProjetoModel();
    }

    public function index(): void
    {
        $usuario = Auth::user();
        $uid     = (int) $usuario['id'];
        $perfil  = $usuario['perfil'];

        // Chamada segura dos dados
        try {
            $kpiProjetos      = $this->model->contarPorStatus();
            $kpiTarefas       = $this->model->contarTarefasPorStatus();
            $taxaConclusao    = $this->model->taxaConclusao();
            $projetosRecentes = $this->model->listarRecentesEmAndamento(5);
            $tarefasUrgentes  = $this->model->listarTarefasUrgentes($perfil === 'colaborador' ? $uid : 0, 6);
            $graficoDept      = $this->model->distribuicaoPorDepartamento();
            $graficoMensal    = $this->model->evolucaoMensal();
        } catch (Exception $e) {
            // Se der erro no banco, definimos arrays vazios para não quebrar a tela
            $kpiProjetos = ['total' => 0];
            $kpiTarefas = ['total' => 0, 'atrasadas' => 0];
            $taxaConclusao = 0;
            $projetosRecentes = [];
            $tarefasUrgentes = [];
            $graficoDept = [];
            $graficoMensal = [];
        }
        
        // Serialização
        $chartDeptLabels   = json_encode(array_column($graficoDept, 'label') ?: []);
        $chartDeptData     = json_encode(array_column($graficoDept, 'total') ?: []);
        $chartMesLabels    = json_encode(array_column($graficoMensal, 'mes') ?: []);
        $chartMesData      = json_encode(array_column($graficoMensal, 'total') ?: []);

        require ROOT_PATH . '/views/dashboard/index.php';
    }
}