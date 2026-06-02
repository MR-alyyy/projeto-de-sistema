
<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>Cadastro</title>


</head>

<body>

<div class="container">

<form method="post">

<h2>cadastro</h2>


<label>nome</label>
<input name="nomeG" type="text" autocomplete="off">

<label>Senha</label>
<input name="senha" type="password">

<button type="submit" name="inserir">Cadastrar</button>

<a href="login.php">
	<button type="button">fazer login?</button>
</a>


</form>

</div>

</body>
</html>

<?php
include "conexao.php";




if (isset($_POST['inserir'])) {

$nomeG = trim($_POST['nomeG']);
$senha = trim($_POST['senha']);

$erro = false;

/* VALIDAÇÃO DE SENHA */
$senhaForte = preg_match('/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[\W_]).{8,}$/', $senha);

if (!$senhaForte) {
    $mensagem .= "<p class='erro'>A senha deve ter no mínimo 8 caracteres, com letra maiúscula, minúscula, número e símbolo.</p>";
    $erro = true;
}




$senhaCriptografada = password_hash($senha, PASSWORD_DEFAULT);



$stmt = $conexao->prepare("INSERT INTO gerente (nomeG, senha) VALUES (?, ?)");

$stmt->bind_param("ss", $nomeG, $senhaForte);

if ($stmt->execute()) {
    $mensagem = "<p class='sucesso'>Cadastro realizado com sucesso!</p>";
} else {
    $mensagem = "<p class='erro'>Erro ao cadastrar: ".$stmt->error."</p>";
}

$stmt->close();

}




?>
