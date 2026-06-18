<?php
/**
 * Departamento.php
 * Model responsável pelos dados de departamentos no GESTIQ.
 */

require_once ROOT_PATH . '/config/database.php';

class Departamento
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::getConnection();
    }

    /* ------------------------------------------------------------------ */
    /* LISTAGEM E BUSCA                                                     */
    /* ------------------------------------------------------------------ */

    public function listar(array $filtros = []): array
    {
        $sql = "SELECT d.*,
                       COUNT(DISTINCT u.id_usuario) AS total_usuarios,
                       COUNT(DISTINCT p.id_projeto) AS total_projetos
                FROM departamentos d
                LEFT JOIN usuarios u ON u.id_departamento = d.id_departamento AND u.ativo = 1
                LEFT JOIN projetos p ON p.id_departamento = d.id_departamento
                WHERE 1=1";
        $params = [];

        if (!empty($filtros['busca'])) {
            $sql .= " AND (d.nome LIKE :busca OR d.descricao LIKE :busca)";
            $params[':busca'] = '%' . $filtros['busca'] . '%';
        }

        $sql .= " GROUP BY d.id_departamento ORDER BY d.nome ASC";

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public function buscarPorId(int $id): array|false
    {
        $stmt = $this->db->prepare(
            "SELECT d.*,
                    COUNT(DISTINCT u.id_usuario) AS total_usuarios,
                    COUNT(DISTINCT p.id_projeto) AS total_projetos
             FROM departamentos d
             LEFT JOIN usuarios u ON u.id_departamento = d.id_departamento AND u.ativo = 1
             LEFT JOIN projetos p ON p.id_departamento = d.id_departamento
             WHERE d.id_departamento = :id
             GROUP BY d.id_departamento LIMIT 1"
        );
        $stmt->execute([':id' => $id]);
        return $stmt->fetch();
    }

    public function listarSimples(): array
    {
        $stmt = $this->db->query(
            "SELECT id_departamento, nome FROM departamentos ORDER BY nome ASC"
        );
        return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
    }

    /* ------------------------------------------------------------------ */
    /* CRUD                                                                  */
    /* ------------------------------------------------------------------ */

    public function criar(array $dados): int|false
    {
        $erros = $this->validar($dados);
        if (!empty($erros)) {
            return false;
        }

        $stmt = $this->db->prepare(
            "INSERT INTO departamentos (nome, descricao) VALUES (:nome, :descricao)"
        );
        $stmt->execute([
            ':nome'      => trim($dados['nome']),
            ':descricao' => trim($dados['descricao'] ?? ''),
        ]);
        return (int) $this->db->lastInsertId();
    }

    public function atualizar(int $id, array $dados): bool
    {
        $erros = $this->validar($dados, $id);
        if (!empty($erros)) {
            return false;
        }

        $stmt = $this->db->prepare(
            "UPDATE departamentos SET nome = :nome, descricao = :descricao WHERE id_departamento = :id"
        );
        return $stmt->execute([
            ':nome'      => trim($dados['nome']),
            ':descricao' => trim($dados['descricao'] ?? ''),
            ':id'        => $id,
        ]);
    }

    public function excluir(int $id): bool|string
    {
        $stmt = $this->db->prepare("SELECT COUNT(*) FROM usuarios WHERE id_departamento = :id AND ativo = 1");
        $stmt->execute([':id' => $id]);
        if ($stmt->fetchColumn() > 0) {
            return 'Existem usuários ativos vinculados a este departamento. Transfira-os antes de excluir.';
        }

        $stmt = $this->db->prepare("DELETE FROM departamentos WHERE id_departamento = :id");
        return $stmt->execute([':id' => $id]);
    }

    /* ------------------------------------------------------------------ */
    /* VALIDAÇÃO                                                             */
    /* ------------------------------------------------------------------ */

    public function validar(array $dados, int $excluirId = 0): array
    {
        $erros = [];

        if (empty(trim($dados['nome'] ?? ''))) {
            $erros[] = 'O nome do departamento é obrigatório.';
        } elseif (mb_strlen(trim($dados['nome'])) > 100) {
            $erros[] = 'O nome deve ter no máximo 100 caracteres.';
        }

        return $erros;
    }
}