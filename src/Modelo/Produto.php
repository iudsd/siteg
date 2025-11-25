<?php
class Produto
{
    private int $id;
    private string $nome;
    private string $descricao;
    private string $efeito;
    private float $preco;
    private ?string $imagem;
    private ?int $categoria_id;
    private ?string $categoria_nome;

    public function __construct(
        int $id,
        string $nome,
        string $descricao,
        string $efeito,
        float $preco,
        int $categoria_id,
        ?string $imagem = null,
        ?string $categoria_nome = null
    ) {
        $this->id = $id;
        $this->nome = $nome;
        $this->descricao = $descricao;
        $this->efeito = $efeito;
        $this->preco = $preco;
        $this->categoria_id = $categoria_id;
        $this->imagem = $imagem;
        $this->categoria_nome = $categoria_nome;
    }

    public function getId(): int { return $this->id; }
    public function getNome(): string { return $this->nome; }
    public function getDescricao(): string { return $this->descricao; }
    public function getEfeito(): string { return $this->efeito; }
    public function getPreco(): float { return $this->preco; }
    public function getCategoriaId(): int { return $this->categoria_id; }
    public function getImagem(): ?string { return $this->imagem; }
    public function setImagem(?string $imagem): void { $this->imagem = $imagem; }

    public function getCategoriaNome(): ?string { return $this->categoria_nome; }
    public function setCategoriaNome(?string $categoria_nome): void { $this->categoria_nome = $categoria_nome; }

    public function getPrecoFormatado(): string
    {
        return 'R$ ' . number_format($this->preco, 2, ',', '.');
    }
}
?>