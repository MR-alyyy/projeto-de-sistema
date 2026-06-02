
<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>Cadastro livro</title>


</head>

<body>

<div class="container">

<form method="post">

<h2>cadastro</h2>


<label>titulo</label>
<input name="titulo" type="text" >

<label>autor</label>
<input name="autor" type="text" >

<label>genero</label>
<input name="genero" type="text" >

<label>quantidade</label>
<input name="quantidade" type="text" >

<label>descricao</label>
<input name="descricao" type="text" >


<button type="submit" name="inserir">Cadastrar</button>


</form>

</div>

</body>
</html>
<?php
include "conexao.php";



$mensagem = "";

if (isset($_POST['inserir'])) {

$titulo = trim($_POST['titulo']);
$autor = trim($_POST['autor']);
$genero = trim($_POST['genero']);
$quantidade = trim($_POST['quantidade']);
$descricao = trim($_POST['descricao']);

$erro = false;




$stmt = $conexao->prepare("INSERT INTO livros (titulo, autor, genero, quantidade, descricao) VALUES (?, ?, ?, ?,?)");

$stmt->bind_param("sssss", $titulo, $autor, $genero, $quantidade, $descricao);

if ($stmt->execute()) {
    $mensagem = "<p class='sucesso'>Cadastro realizado com sucesso!</p>";
} else {
    $mensagem = "<p class='erro'>Erro ao cadastrar: ".$stmt->error."</p>";
}

$stmt->close();

}



?>
