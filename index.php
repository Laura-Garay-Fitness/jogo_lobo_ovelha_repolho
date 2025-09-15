<?php
session_start();

// Inicializar estado
if (!isset($_SESSION['estado'])) {
    $_SESSION['estado'] = [
        'barco' => 'esquerda',
        'lobo' => 'esquerda',
        'cabra' => 'esquerda',
        'repolho' => 'esquerda'
    ];
    $_SESSION['mensagem'] = "";
}

// Verifica derrota
function verificarDerrota($estado) {
    if ($estado['lobo'] == $estado['cabra'] && $estado['barco'] != $estado['lobo']) {
        return "O lobo comeu a cabra! 😱 Game Over!";
    }
    if ($estado['cabra'] == $estado['repolho'] && $estado['barco'] != $estado['cabra']) {
        return "A cabra comeu o repolho! 😱 Game Over!";
    }
    return false;
}

// Verifica vitória
function verificarVitoria($estado) {
    return $estado['lobo']=='direita' && $estado['cabra']=='direita' && $estado['repolho']=='direita';
}

// Jogada
$derrota = false;
$vitoria = false;

if (isset($_POST['acao'])) {
    $acao = $_POST['acao'];
    $estado = $_SESSION['estado'];

    if ($acao == 'barco') {
        $estado['barco'] = ($estado['barco']=='esquerda') ? 'direita' : 'esquerda';
    } else {
        if ($estado[$acao] == $estado['barco']) {
            $estado['barco'] = ($estado['barco']=='esquerda') ? 'direita' : 'esquerda';
            $estado[$acao] = $estado['barco'];
        }
    }

    $_SESSION['estado'] = $estado;

    $derrota = verificarDerrota($estado);
    $vitoria = verificarVitoria($estado);

    if ($derrota) {
        $_SESSION['mensagem'] = $derrota;
    } elseif ($vitoria) {
        $_SESSION['mensagem'] = "🎉 Parabéns! Você venceu! 🎉";
    } else {
        $_SESSION['mensagem'] = "";
    }
}

// Resetar jogo
if (isset($_POST['reset'])) {
    session_destroy();
    header("Location: index.php");
    exit;
}

// Função para mostrar imagens
function mostrarMargem($lado, $estado) {
    $html = "";
    $imagens = ['lobo'=>'lobo.png','cabra'=>'cabra.png','repolho'=>'repolho.png'];
    foreach ($imagens as $nome=>$img) {
        if ($estado[$nome]==$lado) {
            $html .= "<img src='img/$img' width='60' style='margin:5px'>";
        }
    }
    if ($estado['barco']==$lado) {
        $html .= "<img src='img/barco.png' width='100' style='margin:5px'>";
    }
    return $html;
}
?>

<!DOCTYPE html>
<html lang="pt">
<head>
<meta charset="UTF-8">
<title>Lobo, Cabra e Repolho</title>
<style>
    body { font-family: Arial; text-align: center; background: #87CEEB; }
    h1 { margin-top: 20px; }
    .jogo { display:flex; justify-content:space-around; margin:30px; }
    .margem { width:40%; background:#7cfc00; padding:10px; border-radius:10px; min-height:200px; }
    form { margin:15px; }
    button { padding:10px; margin:5px; font-size:16px; cursor:pointer; }
    .mensagem { font-weight:bold; font-size:18px; margin:15px; }
</style>
</head>
<body>

<h1>🌊 Lobo, Cabra e Repolho 🌊</h1>
<p>Leve todos para a margem direita sem que se devorem!</p>

<div class="mensagem" style="color: <?= $derrota ? 'red' : ($vitoria ? 'green' : 'black') ?>">
    <?= $_SESSION['mensagem'] ?>
</div>

<div class="jogo">
    <div class="margem">
        <h2>Margem Esquerda</h2>
        <?= mostrarMargem('esquerda', $_SESSION['estado']) ?>
    </div>
    <div class="margem">
        <h2>Margem Direita</h2>
        <?= mostrarMargem('direita', $_SESSION['estado']) ?>
    </div>
</div>

<?php if (!$derrota && !$vitoria): ?>
<form method="post">
    <button type="submit" name="acao" value="barco">Mover só o Barco</button>
    <button type="submit" name="acao" value="lobo">Levar 🐺 Lobo</button>
    <button type="submit" name="acao" value="cabra">Levar 🐐 Cabra</button>
    <button type="submit" name="acao" value="repolho">Levar 🥬 Repolho</button>
</form>
<?php else: ?>
<form method="post">
    <button type="submit" name="reset">🔄 Jogar Novamente</button>
</form>
<?php endif; ?>

</body>
</html>
