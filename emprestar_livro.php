<?php
include "conexao.php";

$mensagem = "";
$prazoDias = 7; // prazo padrão de devolução em dias

if (isset($_POST['inserir'])) {

    $id_livro  = (int) trim($_POST['id_livro']);
    $id_leitor = (int) trim($_POST['id_leitor']);

    $erro = false;

    // ========================
    // VERIFICAR SE HÁ EXEMPLAR DISPONÍVEL
    // ========================
    $stmt = $conexao->prepare("SELECT quantidade FROM livros WHERE id = ?");
    $stmt->bind_param("i", $id_livro);
    $stmt->execute();
    $resultado = $stmt->get_result();
    $livro = $resultado->fetch_assoc();
    $stmt->close();

    if (!$livro) {
        $mensagem .= "<p class='erro'>Livro não encontrado.</p>";
        $erro = true;
    } elseif ($livro['quantidade'] <= 0) {
        $mensagem .= "<p class='erro'>Não há exemplares disponíveis para empréstimo.</p>";
        $erro = true;
    }

    // ========================
    // VERIFICAR SE O LEITOR EXISTE
    // ========================
    if (!$erro) {
        $stmt = $conexao->prepare("SELECT id FROM leitores WHERE id = ?");
        $stmt->bind_param("i", $id_leitor);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows === 0) {
            $mensagem .= "<p class='erro'>Leitor não encontrado.</p>";
            $erro = true;
        }
        $stmt->close();
    }

    // ========================
    // REGISTRAR EMPRÉSTIMO
    // ========================
    if (!$erro) {

        $dataEmprestimo = date("Y-m-d H:i:s");
        $dataPrevista   = date("Y-m-d", strtotime("+$prazoDias days"));

        $stmt = $conexao->prepare("
            INSERT INTO emprestimos
            (id_livro, id_leitor, data_emprestimo, data_prevista, status)
            VALUES (?, ?, ?, ?, 'emprestado')
        ");
        $stmt->bind_param("iiss", $id_livro, $id_leitor, $dataEmprestimo, $dataPrevista);

        if ($stmt->execute()) {

            // baixa 1 exemplar do estoque
            $stmtUpdate = $conexao->prepare("UPDATE livros SET quantidade = quantidade - 1 WHERE id = ?");
            $stmtUpdate->bind_param("i", $id_livro);
            $stmtUpdate->execute();
            $stmtUpdate->close();

            $mensagem = "<p class='sucesso'>Empréstimo registrado com sucesso! Devolução prevista para " . date("d/m/Y", strtotime($dataPrevista)) . ".</p>";

        } else {
            $mensagem = "<p class='erro'>Erro ao registrar empréstimo: " . $stmt->error . "</p>";
        }

        $stmt->close();
    }
}

// ========================
// LISTAR LIVROS DISPONÍVEIS E LEITORES (para os selects)
// ========================
$livros = $conexao->query("SELECT id, titulo FROM livros WHERE quantidade > 0 ORDER BY titulo");
$leitores = $conexao->query("SELECT id, nome FROM leitores ORDER BY nome");
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
<meta charset="UTF-8">
<title>Emprestar Livro</title>
</head>

<body>

<div class="container">

    <h2>Emprestar Livro</h2>

    <?php echo $mensagem; ?>

    <form method="post">

        <label>Livro</label>
        <select name="id_livro" required>
            <option value="">Selecione o livro</option>
            <?php while ($l = $livros->fetch_assoc()): ?>
                <option value="<?php echo $l['id']; ?>"><?php echo htmlspecialchars($l['titulo']); ?></option>
            <?php endwhile; ?>
        </select>

        <label>Leitor</label>
        <select name="id_leitor" required>
            <option value="">Selecione o leitor</option>
            <?php while ($le = $leitores->fetch_assoc()): ?>
                <option value="<?php echo $le['id']; ?>"><?php echo htmlspecialchars($le['nome']); ?></option>
            <?php endwhile; ?>
        </select>

        <button type="submit" name="inserir">Registrar Empréstimo</button>

    </form>

</div>

</body>
</html>
