<?php

include "conexao.php";

$mensagem = "";

// ========================
// FUNÇÃO VALIDAR CPF
// ========================
function validarCPF($cpf)
{
    $cpf = preg_replace('/[^0-9]/', '', $cpf);

    if (strlen($cpf) != 11 || preg_match('/(\d)\1{10}/', $cpf)) {
        return false;
    }

    for ($t = 9; $t < 11; $t++) {

        for ($d = 0, $c = 0; $c < $t; $c++) {
            $d += $cpf[$c] * (($t + 1) - $c);
        }

        $d = ((10 * $d) % 11) % 10;

        if ($cpf[$c] != $d) {
            return false;
        }
    }

    return true;
}

// ========================
// FUNÇÃO CRIPTOGRAFAR CPF
// ========================
function criptografarCPF($cpf)
{
    $cpf = preg_replace('/[^0-9]/', '', $cpf);

    $chave = "minha_chave_secreta_123";
    $metodo = "AES-256-CBC";
    $iv = substr(hash('sha256', $chave), 0, 16);

    return openssl_encrypt($cpf, $metodo, $chave, 0, $iv);
}

// ========================
// PROCESSAR FORMULÁRIO
// ========================
if (isset($_POST['inserir'])) {

    $nome = trim($_POST['nome']);
    $senha = trim($_POST['senha']);
    $telefone = trim($_POST['telefone']);
    $cpf = trim($_POST['cpf']);

    $erro = false;

    // ========================
    // VALIDAÇÃO DE SENHA
    // ========================
    $senhaForte = preg_match(
        '/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[\W_]).{8,}$/',
        $senha
    );

    if (!$senhaForte) {

        $mensagem .= "
        <p class='erro'>
            A senha deve ter no mínimo 8 caracteres,
            com letra maiúscula, minúscula, número e símbolo.
        </p>";

        $erro = true;
    }

    // ========================
    // VALIDAR CPF
    // ========================
    if (!validarCPF($cpf)) {

        $mensagem .= "
        <p class='erro'>
            CPF inválido.
        </p>";

        $erro = true;
    }

    // ========================
    // CADASTRAR
    // ========================
    if (!$erro) {

        $cpf = criptografarCPF($cpf);

        $senhaCriptografada = password_hash($senha, PASSWORD_DEFAULT);

        $stmt = $conexao->prepare("
            INSERT INTO cadastro
            (nome, senha, telefone, cpf)
            VALUES (?, ?, ?, ?)
        ");

        $stmt->bind_param(
            "ssss",
            $nome,
            $senhaCriptografada,
            $telefone,
            $cpf
        );

        if ($stmt->execute()) {

            $mensagem = "
            <p class='sucesso'>
                Cadastro realizado com sucesso!
            </p>";

        } else {

            $mensagem = "
            <p class='erro'>
                Erro ao cadastrar: {$stmt->error}
            </p>";
        }

        $stmt->close();
    }
}

?>

<!DOCTYPE html>
<html lang="pt-br">

<head>

    <meta charset="UTF-8">
    <title>Cadastro</title>

</head>

<body>

<div class="container">

    <form method="post">

        <h2>Cadastro</h2>

        <?php echo $mensagem; ?>

        <label>Nome</label>
        <input
            type="text"
            name="nome"
            autocomplete="off"
            required
        >

        <label>Telefone</label>
        <input
            type="text"
            name="telefone"
            autocomplete="off"
            required
        >

        <label>CPF</label>
        <input
            type="text"
            name="cpf"
            autocomplete="off"
            required
        >

        <label>Senha</label>
        <input
            type="password"
            name="senha"
            required
        >

        <button type="submit" name="inserir">
            Cadastrar
        </button>

        <a href="login.php">
            <button type="button">
                Já fez login?
            </button>
        </a>

    </form>

</div>

</body>
</html>
