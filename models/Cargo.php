<?php
require_once ROOT_PATH . '/config/database.php';

class Cargo
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
        $sql = "SELECT c.*,
                       d.nome AS departamento_nome,
                       COUNT(u.id_usuario) AS total_usuarios
                FROM cargos c
                LEFT JOIN departamentos d ON c.id_departamento = d.id_departamento
                LEFT JOIN usuarios      u ON u.id_cargo = c.id_cargo AND u.ativo = 1
                WHERE 1=1";
        $params = [];

        if (!empty($filtros['busca'])) {
            $sql .= " AND (c.nome LIKE :busca OR c.descricao LIKE :busca)";
            $params[':busca'] = '%' . $filtros['busca'] . '%';
        }

        if (!empty($filtros['id_departamento'])) {
            $sql .= " AND c.id_departamento = :id_departamento";
            $params[':id_departamento'] = (int) $filtros['id_departamento'];
        }

        $sql .= " GROUP BY c.id_cargo ORDER BY d.nome ASC, c.nome ASC";

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public function buscarPorId(int $id): array|false
    {
        $stmt = $this->db->prepare(
            "SELECT c.*, d.nome AS departamento_nome,
                    COUNT(u.id_usuario) AS total_usuarios
             FROM cargos c
             LEFT JOIN departamentos d ON c.id_departamento = d.id_departamento
             LEFT JOIN usuarios      u ON u.id_cargo = c.id_cargo AND u.ativo = 1
             WHERE c.id_cargo = :id
             GROUP BY c.id_cargo"
        );
        $stmt->execute([':id' => $id]);
        return $stmt->fetch();
    }

    public function listarSimples(?int $idDepartamento = null): array
    {
        if ($idDepartamento) {
            $stmt = $this->db->prepare(
                "SELECT id_cargo, nome FROM cargos WHERE id_departamento = :id ORDER BY nome ASC"
            );
            $stmt->execute([':id' => $idDepartamento]);
        } else {
            $stmt = $this->db->query(
                "SELECT id_cargo, nome FROM cargos ORDER BY nome ASC"
            );
        }
        return $stmt->fetchAll();
    }

    public function listarUsuarios(int $idCargo): array
    {
        $stmt = $this->db->prepare(
            "SELECT u.id_usuario, u.nome, u.email, u.perfil, d.nome AS departamento_nome
             FROM usuarios u
             LEFT JOIN departamentos d ON u.id_departamento = d.id_departamento
             WHERE u.id_cargo = :id AND u.ativo = 1
             ORDER BY u.nome ASC"
        );
        $stmt->execute([':id' => $idCargo]);
        return $stmt->fetchAll();
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
            "INSERT INTO cargos (nome, descricao, id_departamento)
             VALUES (:nome, :descricao, :id_departamento)"
        );
        $stmt->execute([
            ':nome'            => trim($dados['nome']),
            ':descricao'       => trim($dados['descricao'] ?? ''),
            ':id_departamento' => !empty($dados['id_departamento']) ? (int) $dados['id_departamento'] : null,
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
            "UPDATE cargos
             SET nome = :nome, descricao = :descricao, id_departamento = :id_departamento
             WHERE id_cargo = :id"
        );
        return $stmt->execute([
            ':nome'            => trim($dados['nome']),
            ':descricao'       => trim($dados['descricao'] ?? ''),
            ':id_departamento' => !empty($dados['id_departamento']) ? (int) $dados['id_departamento'] : null,
            ':id'              => $id,
        ]);
    }

    public function excluir(int $id): bool|string
    {
        $stmt = $this->db->prepare(
            "SELECT COUNT(*) FROM usuarios WHERE id_cargo = :id AND ativo = 1"
        );
        $stmt->execute([':id' => $id]);
        if ($stmt->fetchColumn() > 0) {
            return 'Existem usuários ativos com este cargo. Transfira-os antes de excluir.';
        }

        $stmt = $this->db->prepare("DELETE FROM cargos WHERE id_cargo = :id");
        return $stmt->execute([':id' => $id]);
    }

    /* ------------------------------------------------------------------ */
    /* VALIDAÇÃO                                                             */
    /* ------------------------------------------------------------------ */

    public function validar(array $dados, int $excluirId = 0): array
    {
        $erros = [];

        if (empty(trim($dados['nome'] ?? ''))) {
            $erros[] = 'O nome do cargo é obrigatório.';
        } elseif (mb_strlen(trim($dados['nome'])) > 100) {
            $erros[] = 'O nome deve ter no máximo 100 caracteres.';
        }

        return $erros;
    }
}
