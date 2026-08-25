<?php

// ==========================================
// CONEXÃO COM O BANCO
// ==========================================

require_once "conexao.php";


// ==========================================
// BUSCAR LEITORES
// ==========================================

$sql = "SELECT id, nome FROM leitores ORDER BY nome ASC";

$resultado = mysqli_query($conexao, $sql);

if (!$resultado) {
    die("Erro ao buscar leitores: " . mysqli_error($conexao));
}


// ==========================================
// CONTAR LEITORES
// ==========================================

$sqlTotal = "SELECT COUNT(*) AS total FROM leitores";

$resultadoTotal = mysqli_query($conexao, $sqlTotal);

$totalLeitores = 0;

if ($resultadoTotal) {
    $dadosTotal = mysqli_fetch_assoc($resultadoTotal);
    $totalLeitores = $dadosTotal["total"];
}


// ==========================================
// PESQUISA DE LEITORES
// ==========================================

$pesquisa = $_GET["pesquisa"] ?? "";

if ($pesquisa != "") {

    $pesquisaSegura = mysqli_real_escape_string(
        $conexao,
        $pesquisa
    );

    $sql = "
        SELECT id, nome
        FROM leitores
        WHERE nome LIKE '%$pesquisaSegura%'
        ORDER BY nome ASC
    ";

    $resultado = mysqli_query($conexao, $sql);

    if (!$resultado) {
        die("Erro na pesquisa: " . mysqli_error($conexao));
    }
}

?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>Painel do Funcionário</title>


    <style>

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: Arial, sans-serif;
        }


        body {
            background: #f1f5f9;
            color: #1e293b;
        }


        .layout {
            display: flex;
            min-height: 100vh;
        }


        /* ===============================
           MENU
        =============================== */

        .sidebar {
            width: 250px;
            height: 100vh;

            position: fixed;
            left: 0;
            top: 0;

            background: #172554;
            color: white;

            padding: 25px 18px;
        }


        .logo {
            font-size: 21px;
            font-weight: bold;

            margin-bottom: 40px;
            padding-left: 10px;
        }


        .logo span {
            color: #60a5fa;
        }


        .menu {
            list-style: none;
        }


        .menu li {
            margin-bottom: 8px;
        }


        .menu a {
            display: block;

            padding: 13px 15px;

            color: #cbd5e1;

            text-decoration: none;

            border-radius: 8px;

            transition: .2s;
        }


        .menu a:hover,
        .menu a.active {
            background: #2563eb;
            color: white;
        }


        /* ===============================
           CONTEÚDO
        =============================== */

        .main {
            margin-left: 250px;

            width: calc(100% - 250px);

            padding: 35px 40px;
        }


        .header {
            display: flex;

            justify-content: space-between;

            align-items: center;

            margin-bottom: 30px;
        }


        .header h1 {
            font-size: 28px;

            margin-bottom: 7px;
        }


        .header p {
            color: #64748b;
        }


        /* ===============================
           CARDS
        =============================== */

        .cards {
            display: grid;

            grid-template-columns:
                repeat(3, 1fr);

            gap: 20px;

            margin-bottom: 30px;
        }


        .card {
            background: white;

            padding: 23px;

            border-radius: 12px;

            box-shadow:
                0 4px 15px rgba(0,0,0,.05);
        }


        .card-icon {
            font-size: 25px;

            margin-bottom: 10px;
        }


        .card p {
            color: #64748b;

            font-size: 14px;
        }


        .card h2 {
            font-size: 28px;

            margin-top: 8px;

            color: #1e3a8a;
        }


        /* ===============================
           PAINEL
        =============================== */

        .panel {
            background: white;

            border-radius: 12px;

            padding: 25px;

            box-shadow:
                0 4px 15px rgba(0,0,0,.05);
        }


        .panel-header {
            display: flex;

            justify-content: space-between;

            align-items: center;

            margin-bottom: 20px;
        }


        .panel-header h2 {
            font-size: 20px;
        }


        /* ===============================
           PESQUISA
        =============================== */

        .pesquisa {
            display: flex;

            gap: 10px;

            margin-bottom: 20px;
        }


        .pesquisa input {
            flex: 1;

            padding: 13px 15px;

            border: 1px solid #cbd5e1;

            border-radius: 8px;

            outline: none;

            font-size: 14px;
        }


        .pesquisa input:focus {
            border-color: #2563eb;
        }


        .pesquisa button {
            border: none;

            background: #2563eb;

            color: white;

            padding: 0 22px;

            border-radius: 8px;

            cursor: pointer;

            font-weight: bold;
        }


        .pesquisa button:hover {
            background: #1d4ed8;
        }


        /* ===============================
           TABELA
        =============================== */

        .table-container {
            overflow-x: auto;
        }


        table {
            width: 100%;

            border-collapse: collapse;
        }


        th {
            background: #f8fafc;

            color: #64748b;

            text-align: left;

            padding: 14px;

            font-size: 13px;
        }


        td {
            padding: 15px 14px;

            border-bottom:
                1px solid #e2e8f0;

            font-size: 14px;
        }


        tr:hover td {
            background: #f8fafc;
        }


        .id {
            color: #64748b;
        }


        /* ===============================
           BOTÃO
        =============================== */

        .btn {
            display: inline-block;

            padding: 7px 12px;

            background: #eff6ff;

            color: #2563eb;

            border-radius: 6px;

            text-decoration: none;

            font-size: 13px;

            font-weight: bold;
        }


        .btn:hover {
            background: #dbeafe;
        }


        .nenhum {
            text-align: center;

            color: #64748b;

            padding: 25px;
        }


        /* ===============================
           RESPONSIVO
        =============================== */

        @media (max-width: 900px) {

            .cards {
                grid-template-columns: 1fr;
            }

        }


        @media (max-width: 700px) {

            .sidebar {
                width: 70px;

                padding: 20px 10px;
            }


            .logo {
                font-size: 0;

                text-align: center;
            }


            .logo span {
                font-size: 22px;
            }


            .menu a {
                font-size: 0;

                text-align: center;
            }


            .main {
                margin-left: 70px;

                width: calc(100% - 70px);

                padding: 20px;
            }


            .pesquisa {
                flex-direction: column;
            }


            .pesquisa button {
                height: 45px;
            }

        }

    </style>

</head>


<body>


<div class="layout">


    <!-- ======================================
         MENU LATERAL
    ======================================= -->

    <aside class="sidebar">

        <div class="logo">

            📚 <span>Biblioteca</span>

        </div>


        <ul class="menu">

            <li>
                <a href="#" class="active">
                    📊 Dashboard
                </a>
            </li>

            <li>
                <a href="#">
                    📚 Livros
                </a>
            </li>

            <li>
                <a href="#">
                    👥 Leitores
                </a>
            </li>

            <li>
                <a href="#">
                    📤 Empréstimos
                </a>
            </li>

            <li>
                <a href="#">
                    📥 Devoluções
                </a>
            </li>

            <li>
                <a href="#">
                    📋 Relatórios
                </a>
            </li>

            <li>
                <a href="#">
                    ⚙️ Configurações
                </a>
            </li>

        </ul>

    </aside>



    <!-- ======================================
         CONTEÚDO
    ======================================= -->

    <main class="main">


        <header class="header">

            <div>

                <h1>
                    Painel do Funcionário
                </h1>

                <p>
                    Gerencie os leitores e o acervo da biblioteca.
                </p>

            </div>

        </header>



        <!-- ======================================
             CARDS
        ======================================= -->

        <section class="cards">


            <div class="card">

                <div class="card-icon">
                    👥
                </div>

                <p>
                    Leitores cadastrados
                </p>

                <h2>
                    <?= $totalLeitores ?>
                </h2>

            </div>


            <div class="card">

                <div class="card-icon">
                    📚
                </div>

                <p>
                    Livros cadastrados
                </p>

                <h2>
                    0
                </h2>

            </div>
            <div class="card">
                <div class="card-icon">
                    📤
                </div>
                <p>
                    Empréstimos ativos
                </p>
                <h2>
                    0
                </h2>
            </div>
        </section>
        <!-- ======================================
             LEITORES
        ======================================= -->
        <section class="panel">
            <div class="panel-header">
                <h2>
                    👥 Leitores cadastrados
                </h2>
            </div>
            <!-- PESQUISA -->
            <form
                method="GET"
                class="pesquisa"
            >
                <input
                    type="text"
                    name="pesquisa"
                    value="<?= htmlspecialchars($pesquisa) ?>"
                    placeholder="Digite o nome do leitor..."
                >
                <button type="submit">
                    Pesquisar
                </button>
            </form>
            <!-- TABELA -->
            <div class="table-container">
                <table>
                    <thead>
                        <tr>
                            <th>
                                ID
                            </th>
                            <th>
                                Nome do leitor
                            </th>
                            <th>
                                Ação
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php if (mysqli_num_rows($resultado) > 0): ?>
                        <?php while (
                            $leitor = mysqli_fetch_assoc($resultado)
                        ): ?>
                            <tr>
                                <td class="id">
                                    #<?= $leitor["id"] ?>
                                </td>
                                <td>
                                    <?= htmlspecialchars(
                                        $leitor["nome"]
                                    ) ?>
                                </td>
                                <td>
                                    <a
                                        href="leitor.php?id=<?= $leitor["id"] ?>"
                                        class="btn"
                                    >
                                        Ver cadastro
                                    </a>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td
                                colspan="3"
                                class="nenhum"
                            >
                                Nenhum leitor encontrado.
                            </td>
                        </tr>
                    <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </section>
    </main>
</div>
</body>
</html>
