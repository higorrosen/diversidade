<?php
// salvar.php
header('Content-Type: application/json');

// Configurações do Banco de Dados
$host = 'localhost';
$dbname = 'aula_roteiro';
$username = 'root'; // Altere para seu usuário do banco
$password = '';     // Altere para sua senha do banco

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Recebe os dados do JavaScript (JSON)
    $dados = json_decode(file_get_contents('php://input'), true);

    if ($dados) {
        $stmt = $pdo->prepare("INSERT INTO respostas_mckinsey (genero, ancestralidade, fenotipo, trajetoria, motivo) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([
            $dados['q1'], 
            $dados['q2'], 
            $dados['q3'], 
            $dados['q4'], 
            $dados['q5']
        ]);
        
        echo json_encode(['status' => 'sucesso']);
    } else {
        echo json_encode(['status' => 'erro', 'mensagem' => 'Nenhum dado recebido.']);
    }

} catch(PDOException $e) {
    echo json_encode(['status' => 'erro', 'mensagem' => $e->getMessage()]);
}
?>
