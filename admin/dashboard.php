<?php
require_once '../includes/auth.php';
require_once '../includes/functions.php';
verificarAuth();

// Buscar estatísticas REAIS do banco de dados
$estatisticas = obterEstatisticasGerais();

// Buscar notícias recentes para atividade
$stmt_recentes = $pdo->prepare("
    SELECT n.*, u.nome as autor 
    FROM noticias n 
    LEFT JOIN usuarios u ON n.autor_id = u.id 
    ORDER BY n.created_at DESC 
    LIMIT 5
");
$stmt_recentes->execute();
$noticias_recentes = $stmt_recentes->fetchAll();

// Buscar estatísticas de banners
$estatisticas_banners = obterEstatisticasBanners();

// Buscar notícias mais visualizadas
$stmt_mais_vistas = $pdo->prepare("
    SELECT * FROM noticias 
    WHERE status = 'publicado' 
    ORDER BY visualizacoes DESC 
    LIMIT 5
");
$stmt_mais_vistas->execute();
$mais_vistas = $stmt_mais_vistas->fetchAll();
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Black Angel</title>
    <link rel="stylesheet" href="../css/admin.css">
    <style>
        .charts-container {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 2rem;
            margin-bottom: 2rem;
        }
        
        .chart-card {
            background: var(--branco);
            padding: 2rem;
            border-radius: 12px;
            box-shadow: var(--sombra);
        }
        
        .chart-card h3 {
            color: var(--preto);
            margin-bottom: 1.5rem;
            font-size: 1.2rem;
        }
        
        .simple-chart {
            display: flex;
            flex-direction: column;
            gap: 1rem;
        }
        
        .chart-bar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 0.5rem 0;
            border-bottom: 1px solid var(--cinza);
        }
        
        .chart-bar:last-child {
            border-bottom: none;
        }
        
        .chart-label {
            font-weight: 500;
            color: var(--preto);
        }
        
        .chart-value {
            font-weight: 600;
            color: var(--vermelho);
        }
        
        .progress-bar {
            width: 100%;
            height: 8px;
            background: var(--cinza-claro);
            border-radius: 4px;
            overflow: hidden;
            margin-top: 0.25rem;
        }
        
        .progress-fill {
            height: 100%;
            background: var(--vermelho);
            border-radius: 4px;
        }
        
        .top-content {
            background: var(--branco);
            padding: 2rem;
            border-radius: 12px;
            box-shadow: var(--sombra);
            margin-bottom: 2rem;
        }
        
        .top-content h3 {
            color: var(--preto);
            margin-bottom: 1.5rem;
            font-size: 1.2rem;
        }
        
        .content-list {
            display: flex;
            flex-direction: column;
            gap: 1rem;
        }
        
        .content-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 1rem;
            background: var(--cinza-claro);
            border-radius: 8px;
            transition: all 0.3s;
        }
        
        .content-item:hover {
            background: var(--cinza);
        }
        
        .content-title {
            font-weight: 500;
            color: var(--preto);
            flex: 1;
        }
        
        .content-views {
            background: var(--vermelho);
            color: var(--branco);
            padding: 0.25rem 0.75rem;
            border-radius: 12px;
            font-size: 0.8rem;
            font-weight: 600;
        }
        
        @media (max-width: 768px) {
            .charts-container {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <div class="admin-layout">
        <!-- Sidebar -->
        <aside class="admin-sidebar">
            <div class="sidebar-header">
                 <h1>⚡ SOTCONTROL</h1 >  
            </div>
            
            <nav class="sidebar-nav">
                <a href="dashboard.php" class="nav-item active">
                    <span>📊</span>
                    <span>Dashboard</span>
                </a>
                <a href="cadastrar_noticia.php" class="nav-item">
                    <span>📝</span>
                    <span>Nova Notícia</span>
                </a>
                <a href="gerenciar_noticias.php" class="nav-item">
                    <span>📰</span>
                    <span>Gerenciar Notícias</span>
                </a>
                <a href="anuncios.php" class="nav-item">
                    <span>💰</span>
                    <span>Anúncios</span>
                </a>
                <a href="configuracoes.php" class="nav-item">
                    <span>⚙️</span>
                    <span>Configurações</span>
                </a>
                <a href="../index.php" class="nav-item">
                    <span>👁️</span>
                    <span>Ver Site</span>
                </a>
                <a href="logout.php" class="nav-item">
                    <span>🚪</span>
                    <span>Sair</span>
                </a>
            </nav>
        </aside>

        <!-- Conteúdo Principal -->
        <main class="admin-main">
            <div class="admin-header">
                <div class="welcome">
                    <h1>👋 Bem-vindo, <?php echo $_SESSION['usuario_nome']; ?>!</h1>
                    <p>Painel de Controle - Black Angel</p>
                </div>
                
                <div class="user-menu">
                    <div class="user-info">
                        <div class="user-name"><?php echo $_SESSION['usuario_nome']; ?></div>
                        <div class="user-role">Administrador</div>
                    </div>
                    <a href="logout.php" class="btn-logout">
                        <span>🚪</span>
                        <span>Sair</span>
                    </a>
                </div>
            </div>

            <!-- Estatísticas PRINCIPAIS -->
            <div class="stats-grid">
                <div class="stat-card vermelho">
                    <div class="stat-header">
                        <div class="stat-icon">📰</div>
                    </div>
                    <div class="stat-number"><?php echo $estatisticas['total_noticias']; ?></div>
                    <div class="stat-label">Total de Notícias</div>
                </div>
                
                <div class="stat-card verde">
                    <div class="stat-header">
                        <div class="stat-icon">🚀</div>
                    </div>
                    <div class="stat-number"><?php echo $estatisticas['noticias_publicadas']; ?></div>
                    <div class="stat-label">Notícias Publicadas</div>
                </div>
                
                <div class="stat-card azul">
                    <div class="stat-header">
                        <div class="stat-icon">💰</div>
                    </div>
                    <div class="stat-number"><?php echo $estatisticas['total_anunciantes']; ?></div>
                    <div class="stat-label">Anunciantes</div>
                </div>
                
                <div class="stat-card amarelo">
                    <div class="stat-header">
                        <div class="stat-icon">👁️</div>
                    </div>
                    <div class="stat-number"><?php echo number_format($estatisticas['total_visualizacoes']); ?></div>
                    <div class="stat-label">Visualizações</div>
                </div>
            </div>

            <!-- Gráficos e Estatísticas Detalhadas -->
            <div class="charts-container">
                <!-- Estatísticas de Notícias -->
                <div class="chart-card">
                    <h3>📊 Estatísticas de Notícias</h3>
                    <div class="simple-chart">
                        <div class="chart-bar">
                            <span class="chart-label">Publicadas</span>
                            <span class="chart-value"><?php echo $estatisticas['noticias_publicadas']; ?></span>
                        </div>
                        <div class="progress-bar">
                            <div class="progress-fill" style="width: <?php echo $estatisticas['total_noticias'] > 0 ? ($estatisticas['noticias_publicadas'] / $estatisticas['total_noticias'] * 100) : 0; ?>%"></div>
                        </div>
                        
                        <div class="chart-bar">
                            <span class="chart-label">Em Destaque</span>
                            <span class="chart-value"><?php echo $estatisticas['noticias_destaque']; ?></span>
                        </div>
                        <div class="progress-bar">
                            <div class="progress-fill" style="width: <?php echo $estatisticas['total_noticias'] > 0 ? ($estatisticas['noticias_destaque'] / $estatisticas['total_noticias'] * 100) : 0; ?>%"></div>
                        </div>
                        
                        <div class="chart-bar">
                            <span class="chart-label">Rascunhos</span>
                            <span class="chart-value"><?php echo $estatisticas['total_noticias'] - $estatisticas['noticias_publicadas']; ?></span>
                        </div>
                        <div class="progress-bar">
                            <div class="progress-fill" style="width: <?php echo $estatisticas['total_noticias'] > 0 ? (($estatisticas['total_noticias'] - $estatisticas['noticias_publicadas']) / $estatisticas['total_noticias'] * 100) : 0; ?>%"></div>
                        </div>
                    </div>
                </div>

                <!-- Estatísticas de Anúncios -->
                <div class="chart-card">
                    <h3>💰 Estatísticas de Anúncios</h3>
                    <div class="simple-chart">
                        <div class="chart-bar">
                            <span class="chart-label">Total de Banners</span>
                            <span class="chart-value"><?php echo $estatisticas_banners['total_banners']; ?></span>
                        </div>
                        
                        <div class="chart-bar">
                            <span class="chart-label">Banners Ativos</span>
                            <span class="chart-value"><?php echo $estatisticas_banners['banners_ativos']; ?></span>
                        </div>
                        <div class="progress-bar">
                            <div class="progress-fill" style="width: <?php echo $estatisticas_banners['total_banners'] > 0 ? ($estatisticas_banners['banners_ativos'] / $estatisticas_banners['total_banners'] * 100) : 0; ?>%"></div>
                        </div>
                        
                        <div class="chart-bar">
                            <span class="chart-label">Total de Cliques</span>
                            <span class="chart-value"><?php echo number_format($estatisticas_banners['total_cliques']); ?></span>
                        </div>
                        
                        <div class="chart-bar">
                            <span class="chart-label">Banners Inativos</span>
                            <span class="chart-value"><?php echo $estatisticas_banners['banners_inativos']; ?></span>
                        </div>
                        <div class="progress-bar">
                            <div class="progress-fill" style="width: <?php echo $estatisticas_banners['total_banners'] > 0 ? ($estatisticas_banners['banners_inativos'] / $estatisticas_banners['total_banners'] * 100) : 0; ?>%"></div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Conteúdo Mais Visualizado -->
            <div class="top-content">
                <h3>🔥 Conteúdo Mais Visualizado</h3>
                <div class="content-list">
                    <?php if ($mais_vistas): ?>
                        <?php foreach ($mais_vistas as $noticia): ?>
                        <div class="content-item">
                            <span class="content-title"><?php echo limitarTexto($noticia['titulo'], 60); ?></span>
                            <span class="content-views"><?php echo $noticia['visualizacoes']; ?> visualizações</span>
                        </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div class="content-item">
                            <span class="content-title">Nenhuma notícia publicada ainda</span>
                            <span class="content-views">0 visualizações</span>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Ações Rápidas -->
            <div class="quick-actions">
                <h2>🚀 Ações Rápidas</h2>
                <div class="action-buttons">
                    <a href="cadastrar_noticia.php" class="btn btn-primary">
                        <span>📝</span>
                        <span>Nova Notícia</span>
                    </a>
                    <a href="gerenciar_noticias.php" class="btn btn-secondary">
                        <span>📰</span>
                        <span>Gerenciar Notícias</span>
                    </a>
                    <a href="anuncios.php" class="btn btn-secondary">
                        <span>💰</span>
                        <span>Gerenciar Anúncios</span>
                    </a>
                    <a href="configuracoes.php" class="btn btn-secondary">
                        <span>⚙️</span>
                        <span>Configurações</span>
                    </a>
                </div>
            </div>

            <!-- Atividade Recente -->
            <div class="recent-activity">
                <h2>📈 Atividade Recente</h2>
                <div class="activity-list">
                    <?php if ($noticias_recentes): ?>
                        <?php foreach ($noticias_recentes as $noticia): ?>
                        <div class="activity-item">
                            <div class="activity-icon" style="background: <?php echo $noticia['status'] === 'publicado' ? 'var(--verde)' : 'var(--amarelo)'; ?>;">
                                <?php echo $noticia['status'] === 'publicado' ? '🚀' : '📝'; ?>
                            </div>
                            <div class="activity-content">
                                <div class="activity-title">
                                    <?php echo $noticia['titulo']; ?>
                                    <?php if ($noticia['destaque'] === 'sim'): ?>
                                        <span style="color: var(--vermelho); font-size: 0.8rem; margin-left: 0.5rem;">⭐ Destaque</span>
                                    <?php endif; ?>
                                </div>
                                <div class="activity-time">
                                    Por <?php echo $noticia['autor']; ?> • 
                                    <?php echo formatarData($noticia['created_at']); ?> • 
                                    <?php echo $noticia['visualizacoes']; ?> visualizações
                                </div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div class="activity-item">
                            <div class="activity-icon" style="background: var(--cinza-escuro);">
                                📝
                            </div>
                            <div class="activity-content">
                                <div class="activity-title">Nenhuma notícia cadastrada ainda</div>
                                <div class="activity-time">Comece criando sua primeira notícia!</div>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </main>
    </div>

    <script>
        // Atualizar dados em tempo real (opcional)
        function atualizarEstatisticas() {
            // Aqui você pode adicionar atualização em tempo real se quiser
            console.log('Estatísticas atualizadas:', new Date().toLocaleTimeString());
        }
        
        // Atualizar a cada 30 segundos (opcional)
        // setInterval(atualizarEstatisticas, 30000);
    </script>
</body>
</html>