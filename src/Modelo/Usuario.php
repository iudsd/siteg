<?php
class Usuario
{
    private int $id;
    private string $nome;
    private string $email;
    private string $senha;
    private string $perfil;

    public function __construct(int $id, string $nome, string $email, string $senha, string $perfil = 'User'){
        $this->id = $id;
        $this->nome = $nome;
        $this->email = $email;
        $this->senha = $senha;
        $this->perfil = $perfil;
    }

    public function getId(): int { return $this->id; }
    public function getNome(): string { return $this->nome; }
    public function getEmail(): string { return $this->email; }
    public function getSenha(): string { return $this->senha; }
    public function getPerfil(): string { return $this->perfil; }

    public function setPerfil(string $perfil): void { $this->perfil = $perfil; }
}
?>
