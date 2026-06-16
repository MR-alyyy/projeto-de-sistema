<?php

include "conexao.php";

$mensagem = "";
if (isset($_POST["inserir"])) {

    $nome     = trim($_POST["nome"]);
    $senha    = trim($_POST["senha"]);
    $telefone = trim($_POST["telefone"]);
    $cpf      = trim($_POST["cpf"]);

    $mensagem = "";
    $erro     = false;

    // ── 1. VALIDAÇÃO DE SENHA ────────────────────────────────────────────────
    $senhaForte = preg_match('/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[\W_]).{8,}$/', $senha);

    if (!$senhaForte) {
        $mensagem .= "<p class='erro'>A senha deve ter no mínimo 8 caracteres, com letra maiúscula, minúscula, número e símbolo.</p>";
        $erro = true;
    }

    // ── 2. VALIDAÇÃO DE TELEFONE ─────────────────────────────────────────────
    $telefoneLimpo  = preg_replace('/\D/', '', $telefone);
    $telefoneValido = preg_match('/^(\d{2})(9\d{8}|\d{8})$/', $telefoneLimpo);

    if (!$telefoneValido) {
        $mensagem .= "<p class='erro'>Telefone inválido. Use o formato (XX) XXXXX-XXXX para celular ou (XX) XXXX-XXXX para fixo.</p>";
        $erro = true;
    }

    // ── 3. VALIDAÇÃO DE CPF ──────────────────────────────────────────────────
    function validarCPF(string $cpf): bool {
        $cpf = preg_replace('/\D/', '', $cpf);

        if (strlen($cpf) !== 11) return false;
        if (preg_match('/^(\d)\1{10}$/', $cpf)) return false;

        $soma = 0;
        for ($i = 0; $i < 9; $i++) {
            $soma += (int)$cpf[$i] * (10 - $i);
        }
        $resto   = $soma % 11;
        $digito1 = $resto < 2 ? 0 : 11 - $resto;
        if ((int)$cpf[9] !== $digito1) return false;

        $soma = 0;
        for ($i = 0; $i < 10; $i++) {
            $soma += (int)$cpf[$i] * (11 - $i);
        }
        $resto   = $soma % 11;
        $digito2 = $resto < 2 ? 0 : 11 - $resto;

        return (int)$cpf[10] === $digito2;
    }

    if (!validarCPF($cpf)) {
        $mensagem .= "<p class='erro'>CPF inválido. Verifique os números digitados.</p>";
        $erro = true;
    }

    // ── 4. CRIPTOGRAFIA E INSERÇÃO ───────────────────────────────────────────
    if (!$erro) {
        $senhaCriptografada = password_hash($senha, PASSWORD_BCRYPT);

        $stmt = $conexao->prepare("INSERT INTO funcionarios (nome, senha, telefone, cpf) VALUES (?,?,?,?)");
if ($stmt === false) {
    $mensagem = "<p class='erro'>Erro na query: " . $conexao->error . "</p>";
} else {
    $stmt->bind_param("ssss", $nome, $senhaCriptografada, $telefoneLimpo, $cpf);

    if ($stmt->execute()) {
        $mensagem = "<p class='sucesso'>Cadastro realizado com sucesso!</p>";
    } else {
        $mensagem = "<p class='erro'>Erro ao cadastrar o funcionário: " . $stmt->error . "</p>";
    }

    $stmt->close();
}
}
?>


<!DOCTYPE html>
<html lang="pt-br">

<head>

    <meta charset="UTF-8">
    <title>Cadastro funcionario</title>

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
