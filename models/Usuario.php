<?php
require_once ROOT_PATH . '/config/database.php';

class Usuario
{
    private PDO $db;

    public const PERFIS = ['admin', 'gestor', 'colaborador'];

    public function __construct()
    {
        $this->db = Database::getConnection();
    }

    /* ------------------------------------------------------------------ */
    /* LISTAGEM E BUSCA                                                     */
    /* ------------------------------------------------------------------ */

    public function listar(array $filtros = []): array
    {
        $sql = "SELECT u.*,
                       d.nome AS departamento_nome,
                       c.nome AS cargo_nome
                FROM usuarios u
                LEFT JOIN departamentos d ON u.id_departamento = d.id_departamento
                LEFT JOIN cargos        c ON u.id_cargo = c.id_cargo
                WHERE 1=1";
        $params = [];

        if (!empty($filtros['busca'])) {
            $sql .= " AND (u.nome LIKE :busca OR u.email LIKE :busca)";
            $params[':busca'] = '%' . $filtros['busca'] . '%';
        }

        if (isset($filtros['ativo']) && $filtros['ativo'] !== '') {
            $sql .= " AND u.ativo = :ativo";
            $params[':ativo'] = (int) $filtros['ativo'];
        }

        if (!empty($filtros['perfil'])) {
            $sql .= " AND u.perfil = :perfil";
            $params[':perfil'] = $filtros['perfil'];
        }

        if (!empty($filtros['id_departamento'])) {
            $sql .= " AND u.id_departamento = :id_departamento";
            $params[':id_departamento'] = (int) $filtros['id_departamento'];
        }

        if (!empty($filtros['id_cargo'])) {
            $sql .= " AND u.id_cargo = :id_cargo";
            $params[':id_cargo'] = (int) $filtros['id_cargo'];
        }

        $sql .= " ORDER BY u.nome ASC";

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public function buscarPorId(int $id): array|false
    {
        $stmt = $this->db->prepare(
            "SELECT u.*, d.nome AS departamento_nome, c.nome AS cargo_nome
             FROM usuarios u
             LEFT JOIN departamentos d ON u.id_departamento = d.id_departamento
             LEFT JOIN cargos        c ON u.id_cargo = c.id_cargo
             WHERE u.id_usuario = :id LIMIT 1"
        );
        $stmt->execute([':id' => $id]);
        return $stmt->fetch();
    }

    public function buscarPorEmail(string $email, int $excluirId = 0): array|false
    {
        $stmt = $this->db->prepare(
            "SELECT * FROM usuarios WHERE email = :email AND id_usuario <> :excluir LIMIT 1"
        );
        $stmt->execute([':email' => $email, ':excluir' => $excluirId]);
        return $stmt->fetch();
    }

    public function listarAtivos(): array
    {
        $stmt = $this->db->query(
            "SELECT id_usuario, nome, email, perfil FROM usuarios WHERE ativo = 1 ORDER BY nome ASC"
        );
        return $stmt->fetchAll();
    }

    public function contarPorPerfil(): array
    {
        $stmt = $this->db->query(
            "SELECT perfil, COUNT(*) AS total FROM usuarios WHERE ativo = 1 GROUP BY perfil"
        );
        return $stmt->fetchAll();
    }

    /* ------------------------------------------------------------------ */
    /* CRUD                                                                  */
    /* ------------------------------------------------------------------ */

    public function criar(array $dados): int|false
    {
        $erros = $this->validar($dados, 0, true);
        if (!empty($erros)) {
            return false;
        }

        $stmt = $this->db->prepare(
            "INSERT INTO usuarios (nome, email, senha, perfil, id_departamento, id_cargo, ativo)
             VALUES (:nome, :email, :senha, :perfil, :id_departamento, :id_cargo, :ativo)"
        );
        $stmt->execute([
            ':nome'            => trim($dados['nome']),
            ':email'           => mb_strtolower(trim($dados['email'])),
            ':senha'           => password_hash($dados['senha'], PASSWORD_BCRYPT, ['cost' => 12]),
            ':perfil'          => $dados['perfil'],
            ':id_departamento' => !empty($dados['id_departamento']) ? (int) $dados['id_departamento'] : null,
            ':id_cargo'        => !empty($dados['id_cargo']) ? (int) $dados['id_cargo'] : null,
            ':ativo'           => isset($dados['ativo']) ? (int) $dados['ativo'] : 1,
        ]);
        return (int) $this->db->lastInsertId();
    }

    public function atualizar(int $id, array $dados): bool
    {
        $alterandoSenha = !empty($dados['senha']);
        $erros = $this->validar($dados, $id, $alterandoSenha);
        if (!empty($erros)) {
            return false;
        }

        $sql = "UPDATE usuarios
                SET nome = :nome, email = :email, perfil = :perfil,
                    id_departamento = :id_departamento, id_cargo = :id_cargo, ativo = :ativo";
        $params = [
            ':nome'            => trim($dados['nome']),
            ':email'           => mb_strtolower(trim($dados['email'])),
            ':perfil'          => $dados['perfil'],
            ':id_departamento' => !empty($dados['id_departamento']) ? (int) $dados['id_departamento'] : null,
            ':id_cargo'        => !empty($dados['id_cargo']) ? (int) $dados['id_cargo'] : null,
            ':ativo'           => isset($dados['ativo']) ? (int) $dados['ativo'] : 1,
            ':id'              => $id,
        ];

        if ($alterandoSenha) {
            $sql .= ", senha = :senha";
            $params[':senha'] = password_hash($dados['senha'], PASSWORD_BCRYPT, ['cost' => 12]);
        }

        $sql .= " WHERE id_usuario = :id";

        $stmt = $this->db->prepare($sql);
        return $stmt->execute($params);
    }

    public function alterarStatus(int $id, int $ativo): bool
    {
        $stmt = $this->db->prepare(
            "UPDATE usuarios SET ativo = :ativo WHERE id_usuario = :id"
        );
        return $stmt->execute([':ativo' => $ativo, ':id' => $id]);
    }

    public function excluir(int $id): bool|string
    {
        // Impede exclusão de responsável por projetos
        $stmt = $this->db->prepare(
            "SELECT COUNT(*) FROM projetos WHERE id_responsavel = :id"
        );
        $stmt->execute([':id' => $id]);
        if ($stmt->fetchColumn() > 0) {
            return 'Este usuário é responsável por projetos. Reatribua-os antes de excluir.';
        }

        $stmt = $this->db->prepare("DELETE FROM usuarios WHERE id_usuario = :id");
        return $stmt->execute([':id' => $id]);
    }

    /* ------------------------------------------------------------------ */
    /* VALIDAÇÃO                                                             */
    /* ------------------------------------------------------------------ */

    public function validar(array $dados, int $excluirId = 0, bool $senhaObrigatoria = false): array
    {
        $erros = [];

        if (empty(trim($dados['nome'] ?? ''))) {
            $erros[] = 'O nome é obrigatório.';
        } elseif (mb_strlen(trim($dados['nome'])) > 150) {
            $erros[] = 'O nome deve ter no máximo 150 caracteres.';
        }

        if (empty(trim($dados['email'] ?? ''))) {
            $erros[] = 'O e-mail é obrigatório.';
        } elseif (!filter_var(trim($dados['email']), FILTER_VALIDATE_EMAIL)) {
            $erros[] = 'E-mail inválido.';
        } elseif ($this->buscarPorEmail(trim($dados['email']), $excluirId)) {
            $erros[] = 'Este e-mail já está em uso.';
        }

        if ($senhaObrigatoria && empty($dados['senha'])) {
            $erros[] = 'A senha é obrigatória.';
        }

        if (!empty($dados['senha'])) {
            if (mb_strlen($dados['senha']) < 6) {
                $erros[] = 'A senha deve ter no mínimo 6 caracteres.';
            }
            if (!empty($dados['confirmar_senha']) && $dados['senha'] !== $dados['confirmar_senha']) {
                $erros[] = 'As senhas não conferem.';
            }
        }

        if (empty($dados['perfil']) || !in_array($dados['perfil'], self::PERFIS, true)) {
            $erros[] = 'Perfil inválido.';
        }

        return $erros;
    }
}
