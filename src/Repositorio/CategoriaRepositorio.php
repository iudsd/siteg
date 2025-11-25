<?php

class CategoriaRepositorio
{
    private PDO $pdo;

    /**
     * @param PDO $pdo
     */
    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    private function formarObjeto($dados)
    {
        return new Categoria(
            $dados['id'],
            $dados['categoria'],
        );
    }


       

    public function buscarTodos()
    {
        $sql = "SELECT * FROM categorias";
        $statement = $this->pdo->query($sql);
        $dados = $statement->fetchAll(PDO::FETCH_ASSOC);

        $todosOsDados = array_map(function ($categoria) {
            return $this->formarObjeto($categoria);
        }, $dados);

        return $todosOsDados;
    }

    public function deletar(int $id)
    {

        // deleta o registro do banco
        $sqlDel = "DELETE FROM categorias WHERE id = ?";
        $stmtDel = $this->pdo->prepare($sqlDel);
        $stmtDel->bindValue(1, $id, PDO::PARAM_INT);
        $stmtDel->execute();

    }

    public function salvar(Categoria $categoria)
    {
        $sql = "INSERT INTO categorias (categoria) VALUES (:categoria)";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':categoria', $categoria->getCategoria(), PDO::PARAM_STR);
        $stmt->execute();
    }

    public function buscar(int $id)
    {
        $sql = "SELECT * FROM categorias WHERE id = ?";
        $statement = $this->pdo->prepare($sql);
        $statement->bindValue(1, $id);
        $statement->execute();

        $dados = $statement->fetch(PDO::FETCH_ASSOC);

        return $this->formarObjeto($dados);
    }

    public function atualizar(Categoria $categoria)
    {
        $sql = "UPDATE categorias SET categoria = :categoria WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':categoria', $categoria->getCategoria(), PDO::PARAM_STR);
        $stmt->bindValue(':id', $categoria->getId(), PDO::PARAM_INT);

        $stmt->execute();
    }
}
