<?php

class produtoRepositorio{
    private PDO $pdo;

    /**
     * @param PDO $pdo
     */
    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    private function formarObjeto($dados){
        return new Produto($dados['id'],
            $dados['tipo'],
            $dados['nome'],
            $dados['descricao'],
            $dados['preco'],
            $dados['imagem']
            );
    }

    public function opcoesCafe(): array 
    { 
        $sql1 = "select * from produtos where tipo = 'Café' ORDER BY preco";
        $statement = $this->pdo->query($sql1);
        $produtosCafe = $statement->fetchAll(mode: PDO::FETCH_ASSOC);

        //transformando os arrays em objetos Produto.
        $dadosCafe = array_map(function ($cafe){
            // Para cada $cafe (que ainda é um array)...
            return new Produto($cafe['id'], // ...cria um objeto Produto
                $cafe['tipo'],
                $cafe['nome'],
                $cafe['descricao'],
                (float) $cafe['preco'],
                $cafe['imagem']);
        }, $produtosCafe); // ← percorre esse array aqui
        
        return $dadosCafe;
    }


    public function opcoesAlmoco(): array {
        $sql2 = "select * from produtos where tipo = 'Almoço' ORDER BY preco";
        $statement = $this->pdo->query($sql2);
        $produtosAlmoco = $statement->fetchAll(mode: PDO::FETCH_ASSOC);

        //transformando os arrays em objetos Produto.
        $dadosAlmoco = array_map(function ($almoco){
            // Para cada $almoco (que ainda é um array)...
            return new Produto($almoco['id'], // ...cria um objeto Produto
            $almoco['tipo'],
            $almoco['nome'],
            $almoco['descricao'],
            (float) $almoco['preco'],
            $almoco['imagem']);
        }, $produtosAlmoco); // ← percorre esse array aqui
        return $dadosAlmoco;
    }

    public function buscarTodos(){
        $sql = "select * from produtos ORDER BY preco";
        $statement = $this->pdo->query($sql);
        $dados = $statement->fetchAll(mode:PDO::FETCH_ASSOC);

        //transformando os arrays em objetos Produto.
        $todoOsDados = array_map(function ($produto){
            // Para cada $produto (que ainda é um array)...
            return $this->formarObjeto($produto);
        }, $dados); 
        
        return $todoOsDados;
    }

    public function deletar(int $id){
            $sql = "delete from produtos where id = ?";
            $statement = $this->pdo->prepare($sql);
            $statement->bindValue(1, $id);
            $statement->execute();
    }

    public function salvar(Produto $produto){
        $sql =  "insert into produtos (tipo, nome, descricao, preco, imagem) values (?,?,?,?,?)";
        $statement= $this->pdo->prepare($sql);
        $statement->bindValue(1, $produto->getTipo());
        $statement->bindValue(2, $produto->getNome());
        $statement->bindValue(3, $produto->getDescricao());
        $statement->bindValue(4, $produto->getPreco());
        $statement->bindValue(5, $produto->getImagem());
        $statement->execute();
        }

        public function buscar(int $id){
            $sql = "select * from produtos where id = ?";
            $statement = $this->pdo->prepare($sql);
            $statement->bindValue(1, $id);
            $statement->execute();

           $dados =  $statement->fetch(PDO::FETCH_ASSOC);

           return $this->formarObjeto($dados);

        }

        public function atualizar(Produto $produto)
        {
            $sql = "UPDATE produtos SET tipo = ?, nome = ?, descricao = ?, preco = ?, imagem = ? WHERE id = ?";
            $statement = $this->pdo->prepare($sql);
            $statement->bindValue(1, $produto->getTipo());
            $statement->bindValue(2, $produto->getNome());
            $statement->bindValue(3, $produto->getDescricao());
            $statement->bindValue(4, $produto->getPreco());
            $statement->bindValue(5, $produto->getImagem());
            $statement->bindValue(6, $produto->getId());
            $statement->execute();
}
    
}
