<?php
include "conexao.php";



$mensagem = "";

if (isset($_POST['inserir'])) {

$nome = trim($_POST['nome']);
$senha = trim($_POST['senha']);

$erro = false;

/* VALIDAÇÃO DE SENHA */
$senhaForte = preg_match('/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[\W_]).{8,}$/', $senha);

if (!$senhaForte) {
    $mensagem .= "<p class='erro'>A senha deve ter no mínimo 8 caracteres, com letra maiúscula, minúscula, número e símbolo.</p>";
    $erro = true;
}


$senhaCriptografada = password_hash($senha, PASSWORD_DEFAULT);

$stmt = $conexao->prepare("INSERT INTO cadastro (nome, senha) VALUES (?, ?)");

$stmt->bind_param("ss", $email, $senhaCriptografada);

if ($stmt->execute()) {
    $mensagem = "<p class='sucesso'>Cadastro realizado com sucesso!</p>";
} else {
    $mensagem = "<p class='erro'>Erro ao cadastrar: ".$stmt->error."</p>";
}

$stmt->close();

}

}


?>

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

<?php echo $mensagem; ?>

<label>E-mail, Celular ou CPF</label>
<input name="email" type="text" autocomplete="off" required>

<label>Senha</label>
<input name="senha" type="password" required>

<button type="submit" name="inserir">Cadastrar</button>

<a href="login.php">
	<button type="button">fazer login?</button>
</a>


</form>

</div>

</body>
</html>
