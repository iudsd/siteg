<?php
class UsuarioRepositorio
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    private function formarObjeto(array $dados): Usuario
    {
        return new Usuario(
            (int)$dados['id'],
            $dados['nome'] ?? '',
            $dados['email'],
            $dados['senha'],   // senha já hash do banco
            $dados['perfil'] ?? 'User'
        );
    }

    public function buscarPorEmail(string $email): ?Usuario
    {
        $sql = "SELECT id, nome, email, senha, perfil FROM usuarios WHERE email = ?";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(1, $email);
        $stmt->execute();
        $dados = $stmt->fetch(PDO::FETCH_ASSOC);

        return $dados ? $this->formarObjeto($dados) : null;
    }

    public function autenticar(string $email, string $senha): ?Usuario
    {
        $usuario = $this->buscarPorEmail($email);
        if (!$usuario) return null;

        if (password_verify($senha, $usuario->getSenha())) {
            return $usuario;
        }

        return null;
    }

    public function salvar(Usuario $usuario): void
    {
        $sql = "INSERT INTO usuarios (nome, email, senha, perfil) VALUES (?, ?, ?, ?)";
        $stmt = $this->pdo->prepare($sql);

        $stmt->bindValue(1, $usuario->getNome());
        $stmt->bindValue(2, $usuario->getEmail());
        $stmt->bindValue(3, $usuario->getSenha()); // senha já vem hash do cadastro
        $stmt->bindValue(4, $usuario->getPerfil());

        $stmt->execute();
    }

    public function listar(): array
    {
        $sql = "SELECT id, nome, email, senha, perfil FROM usuarios ORDER BY id ASC";
        $stmt = $this->pdo->query($sql);

        $usuarios = [];
        while ($dados = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $usuarios[] = $this->formarObjeto($dados);
        }

        return $usuarios;
    }

    public function excluir(int $id): void
    {
        $sql = "DELETE FROM usuarios WHERE id = ?";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(1, $id);
        $stmt->execute();
    }

    public function tornarAdmin(int $id): void
    {
        $sql = "UPDATE usuarios SET perfil = 'Admin' WHERE id = ?";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(1, $id);
        $stmt->execute();
    }
}
?>
