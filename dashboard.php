<?php
// dashboard.php

$host = 'localhost';
$dbname = 'aula_roteiro';
$username = 'root'; // Altere para seu usuário do banco
$password = '';     // Altere para sua senha do banco

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    
    // Função para buscar a contagem de respostas de uma coluna específica
    function buscarDados($pdo, $coluna) {
        $stmt = $pdo->query("SELECT $coluna, COUNT(*) as qtd FROM respostas_mckinsey GROUP BY $coluna");
        $resultados = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        $labels = [];
        $data = [];
        foreach ($resultados as $row) {
            $labels[] = $row[$coluna];
            $data[] = $row['qtd'];
        }
        return ['labels' => $labels, 'data' => $data];
    }

    // Coletando dados para os gráficos
    $dados_motivo = buscarDados($pdo, 'motivo');
    $dados_trajetoria = buscarDados($pdo, 'trajetoria');

} catch(PDOException $e) {
    die("Erro de conexão: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Painel de Resultados - O Último Axioma</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        body { font-family: 'Arial', sans-serif; background-color: #f4f4f9; color: #333; text-align: center; padding: 20px;}
        h1 { color: #2c3e50; }
        .charts-wrapper {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            gap: 20px;
        }
        .chart-container { 
            width: 45%; 
            min-width: 400px;
            background: white; 
            padding: 20px; 
            border-radius: 8px; 
            box-shadow: 0 4px 6px rgba(0,0,0,0.1); 
        }
    </style>
</head>
<body>

    <h1>Análise dos Vieses da Turma</h1>
    <p>Como a turma interpretou as características e os riscos da genialidade?</p>

    <div class="charts-wrapper">
        <div class="chart-container">
            <h3>Motivo do Risco à Segurança</h3>
            <canvas id="graficoMotivo"></canvas>
        </div>

        <div class="chart-container">
            <h3>Trajetória e Acesso</h3>
            <canvas id="graficoTrajetoria"></canvas>
        </div>
    </div>

    <script>
        // Cores padrão para os gráficos
        const cores = ['#3498db', '#e74c3c', '#f1c40f', '#2ecc71', '#9b59b6'];

        // Gráfico de Motivo (Barra)
        const ctxMotivo = document.getElementById('graficoMotivo').getContext('2d');
        new Chart(ctxMotivo, {
            type: 'bar',
            data: {
                labels: <?php echo json_encode($dados_motivo['labels']); ?>,
                datasets: [{
                    label: 'Votos da Turma',
                    data: <?php echo json_encode($dados_motivo['data']); ?>,
                    backgroundColor: cores,
                    borderWidth: 1
                }]
            },
            options: { responsive: true, scales: { y: { beginAtZero: true, ticks: { stepSize: 1 } } } }
        });

        // Gráfico de Trajetória (Pizza)
        const ctxTrajetoria = document.getElementById('graficoTrajetoria').getContext('2d');
        new Chart(ctxTrajetoria, {
            type: 'pie',
            data: {
                labels: <?php echo json_encode($dados_trajetoria['labels']); ?>,
                datasets: [{
                    data: <?php echo json_encode($dados_trajetoria['data']); ?>,
                    backgroundColor: cores,
                    borderWidth: 1
                }]
            },
            options: { responsive: true }
        });
    </script>
</body>
</html>
