<?php
require_once ROOT_PATH . '/config/database.php';

class Comentario
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::getConnection();
    }

    /* ------------------------------------------------------------------ */
    /* LISTAGEM E BUSCA                                                     */
    /* ------------------------------------------------------------------ */

    public function listarPorTarefa(int $idTarefa): array
    {
        $stmt = $this->db->prepare(
            "SELECT c.*, u.nome AS autor_nome, u.perfil AS autor_perfil
             FROM comentarios c
             INNER JOIN usuarios u ON c.id_usuario = u.id_usuario
             WHERE c.id_tarefa = :id_tarefa
             ORDER BY c.created_at ASC"
        );
        $stmt->execute([':id_tarefa' => $idTarefa]);
        return $stmt->fetchAll();
    }

    public function buscarPorId(int $id): array|false
    {
        $stmt = $this->db->prepare(
            "SELECT c.*, u.nome AS autor_nome, u.perfil AS autor_perfil
             FROM comentarios c
             INNER JOIN usuarios u ON c.id_usuario = u.id_usuario
             WHERE c.id_comentario = :id LIMIT 1"
        );
        $stmt->execute([':id' => $id]);
        return $stmt->fetch();
    }

    public function ultimosPorUsuario(int $idUsuario, int $limite = 10): array
    {
        $stmt = $this->db->prepare(
            "SELECT c.*, t.titulo AS tarefa_titulo, p.titulo AS projeto_titulo
             FROM comentarios c
             INNER JOIN tarefas  t ON c.id_tarefa  = t.id_tarefa
             INNER JOIN projetos p ON t.id_projeto = p.id_projeto
             WHERE c.id_usuario = :id
             ORDER BY c.created_at DESC
             LIMIT :limite"
        );
        $stmt->bindValue(':id',     $idUsuario, PDO::PARAM_INT);
        $stmt->bindValue(':limite', $limite,    PDO::PARAM_INT);
        $stmt->execute();
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
            "INSERT INTO comentarios (id_tarefa, id_usuario, texto)
             VALUES (:id_tarefa, :id_usuario, :texto)"
        );
        $stmt->execute([
            ':id_tarefa' => (int) $dados['id_tarefa'],
            ':id_usuario' => (int) $dados['id_usuario'],
            ':texto'     => trim($dados['texto']),
        ]);
        return (int) $this->db->lastInsertId();
    }

    public function atualizar(int $id, string $texto, int $idUsuario): bool
    {
        // Garante que o usuário só edita seu próprio comentário
        $stmt = $this->db->prepare(
            "UPDATE comentarios SET texto = :texto
             WHERE id_comentario = :id AND id_usuario = :id_usuario"
        );
        return $stmt->execute([
            ':texto'      => trim($texto),
            ':id'         => $id,
            ':id_usuario' => $idUsuario,
        ]);
    }

    public function excluir(int $id, int $idUsuario, bool $isAdmin = false): bool
    {
        if ($isAdmin) {
            $stmt = $this->db->prepare("DELETE FROM comentarios WHERE id_comentario = :id");
            return $stmt->execute([':id' => $id]);
        }

        // Usuário comum só pode excluir o próprio comentário
        $stmt = $this->db->prepare(
            "DELETE FROM comentarios WHERE id_comentario = :id AND id_usuario = :id_usuario"
        );
        return $stmt->execute([':id' => $id, ':id_usuario' => $idUsuario]);
    }

    /* ------------------------------------------------------------------ */
    /* VALIDAÇÃO                                                             */
    /* ------------------------------------------------------------------ */

    public function validar(array $dados): array
    {
        $erros = [];

        if (empty(trim($dados['texto'] ?? ''))) {
            $erros[] = 'O comentário não pode estar vazio.';
        } elseif (mb_strlen(trim($dados['texto'])) > 5000) {
            $erros[] = 'O comentário deve ter no máximo 5000 caracteres.';
        }

        if (empty($dados['id_tarefa'])) {
            $erros[] = 'Tarefa inválida.';
        }

        if (empty($dados['id_usuario'])) {
            $erros[] = 'Usuário inválido.';
        }

        return $erros;
    }
}
