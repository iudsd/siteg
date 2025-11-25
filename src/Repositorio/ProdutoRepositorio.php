<?php
require_once __DIR__ . '/../Modelo/Produto.php';

class ProdutoRepositorio
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    private function formarObjeto(array $dados): Produto
    {
        // Garantir que categoria_id possa ser null
        $categoria_id = isset($dados['categoria_id']) ? (int)$dados['categoria_id'] : null;

        $produto = new Produto(
            (int)$dados['id'],
            $dados['nome'],
            $dados['descricao'],
            $dados['efeito'],
            (float)$dados['preco'],
            $categoria_id,                // categoria_id antes da imagem
            $dados['imagem'] ?? null
        );

        // Sempre define nome da categoria, mesmo que seja 'Sem categoria'
        $produto->setCategoriaNome($dados['categoria_nome'] ?? 'Sem categoria');

        return $produto;
    }

    public function buscarPorId(int $id): ?Produto
    {
        $sql = "SELECT p.*, c.categoria AS categoria_nome
                FROM produtos p
                LEFT JOIN categorias c ON c.id = p.categoria_id
                WHERE p.id = ?";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$id]);

        $dados = $stmt->fetch(PDO::FETCH_ASSOC);
        return $dados ? $this->formarObjeto($dados) : null;
    }

    public function buscarTodos(): array
    {
        $sql = "SELECT p.*, c.categoria AS categoria_nome
                FROM produtos p
                LEFT JOIN categorias c ON c.id = p.categoria_id";

        $stmt = $this->pdo->query($sql);

        $produtos = [];
        foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $linha) {
            $produtos[] = $this->formarObjeto($linha);
        }
        return $produtos;
    }

    public function salvar(Produto $produto): void
    {
        $sql = "INSERT INTO produtos (nome, descricao, efeito, preco, categoria_id, imagem)
                VALUES (?, ?, ?, ?, ?, ?)";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            $produto->getNome(),
            $produto->getDescricao(),
            $produto->getEfeito(),
            $produto->getPreco(),
            $produto->getCategoriaId(), // pode ser null
            $produto->getImagem()
        ]);
    }

    public function atualizar(Produto $produto): void
    {
        $sql = "UPDATE produtos SET
                nome=?, descricao=?, efeito=?, preco=?, categoria_id=?, imagem=?
                WHERE id=?";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            $produto->getNome(),
            $produto->getDescricao(),
            $produto->getEfeito(),
            $produto->getPreco(),
            $produto->getCategoriaId(), // pode ser null
            $produto->getImagem(),
            $produto->getId()
        ]);
    }

    public function remover(int $id): void
    {
        $stmt = $this->pdo->prepare("DELETE FROM produtos WHERE id=?");
        $stmt->execute([$id]);
    }

    public function contarTotal(): int
    {
        $stmt = $this->pdo->query("SELECT COUNT(*) AS total FROM produtos");
        return (int)$stmt->fetch(PDO::FETCH_ASSOC)['total'];
    }

    public function buscarPaginado(int $limite, int $offset, ?string $ordem = null, ?string $direcao = 'ASC'): array
    {
        $colunasPermitidas = ['nome', 'descricao', 'efeito', 'preco', 'categoria_id'];

        $sql = "SELECT p.*, c.categoria AS categoria_nome
                FROM produtos p
                LEFT JOIN categorias c ON c.id = p.categoria_id";

        if ($ordem && in_array($ordem, $colunasPermitidas)) {
            $direcao = ($direcao === 'DESC') ? 'DESC' : 'ASC';
            $sql .= " ORDER BY $ordem $direcao";
        }

        $sql .= " LIMIT ? OFFSET ?";

        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(1, $limite, PDO::PARAM_INT);
        $stmt->bindValue(2, $offset, PDO::PARAM_INT);
        $stmt->execute();

        $produtos = [];
        foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $linha) {
            $produtos[] = $this->formarObjeto($linha);
        }

        return $produtos;
    }
}
?>
