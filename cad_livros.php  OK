<?php
include "conexao.php";

$mensagem = "";

if (isset($_POST['inserir'])) {

    $titulo = trim($_POST['titulo']);
    $autor = trim($_POST['autor']);
    $genero = trim($_POST['genero']);
    $quantidade = (int) trim($_POST['quantidade']);
    $descricao = trim($_POST['descricao']);

    $stmt = $conexao->prepare("INSERT INTO livros (titulo, autor, genero, quantidade, descricao) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("sssis", $titulo, $autor, $genero, $quantidade, $descricao);

    if ($stmt->execute()) {
        $mensagem = "<p class='sucesso'>Cadastro realizado com sucesso!</p>";
    } else {
        $mensagem = "<p class='erro'>Erro ao cadastrar: " . $stmt->error . "</p>";
    }

    $stmt->close();
}
?>
<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>Cadastro livro</title>
</head>

<body>

<div class="container">

    <h2>Cadastro</h2>

    <?php echo $mensagem; ?>

    <form method="post">

        <label>Título</label>
        <input name="titulo" type="text">

        <label>Autor</label>
        <input name="autor" type="text">

        <label>Gênero</label>
        <input name="genero" type="text">

        <label>Quantidade</label>
        <input name="quantidade" type="text">

        <label>Descrição</label>
        <input name="descricao" type="text">

        <button type="submit" name="inserir">Cadastrar</button>

    </form>

</div>

</body>
</html>
