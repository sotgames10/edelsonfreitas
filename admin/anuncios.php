<?php
require_once '../includes/auth.php';
require_once '../includes/functions.php';
verificarAuth();

// Buscar todos os anunciantes e anúncios
$stmt_anunciantes = $pdo->prepare("
    SELECT a.*, COUNT(b.id) as total_banners 
    FROM anunciantes a 
    LEFT JOIN banners b ON a.id = b.anunciante_id 
    GROUP BY a.id 
    ORDER BY a.created_at DESC
");
$stmt_anunciantes->execute();
$anunciantes = $stmt_anunciantes->fetchAll();

// Buscar banners ativos
$stmt_banners = $pdo->prepare("
    SELECT b.*, a.nome as anunciante 
    FROM banners b 
    LEFT JOIN anunciantes a ON b.anunciante_id = a.id 
    ORDER BY b.created_at DESC
");
$stmt_banners->execute();
$banners = $stmt_banners->fetchAll();

// Excluir anunciante
if (isset($_GET['excluir_anunciante'])) {
    $id_excluir = $_GET['excluir_anunciante'];
    
    // Buscar banners do anunciante para excluir imagens
    $stmt_banners_anunciante = $pdo->prepare("SELECT imagem FROM banners WHERE anunciante_id = ?");
    $stmt_banners_anunciante->execute([$id_excluir]);
    $banners_anunciante = $stmt_banners_anunciante->fetchAll();
    
    // Excluir imagens dos banners
    foreach ($banners_anunciante as $banner) {
        if ($banner['imagem']) {
            $caminho_imagem = '../uploads/anuncios/' . $banner['imagem'];
            if (file_exists($caminho_imagem)) {
                unlink($caminho_imagem);
            }
        }
    }
    
    // Excluir banners do anunciante
    $stmt_del_banners = $pdo->prepare("DELETE FROM banners WHERE anunciante_id = ?");
    $stmt_del_banners->execute([$id_excluir]);
    
    // Excluir anunciante
    $stmt_del_anunciante = $pdo->prepare("DELETE FROM anunciantes WHERE id = ?");
    if ($stmt_del_anunciante->execute([$id_excluir])) {
        $sucesso = "Anunciante e todos os seus banners foram excluídos com sucesso!";
    } else {
        $erro = "Erro ao excluir anunciante.";
    }
    
    header("Location: anuncios.php");
    exit;
}

// Excluir banner
if (isset($_GET['excluir_banner'])) {
    $id_excluir = $_GET['excluir_banner'];
    
    // Buscar banner para excluir imagem
    $stmt_banner = $pdo->prepare("SELECT imagem FROM banners WHERE id = ?");
    $stmt_banner->execute([$id_excluir]);
    $banner = $stmt_banner->fetch();
    
    // Excluir imagem do banner
    if ($banner && $banner['imagem']) {
        $caminho_imagem = '../uploads/anuncios/' . $banner['imagem'];
        if (file_exists($caminho_imagem)) {
            unlink($caminho_imagem);
        }
    }
    
    // Excluir banner
    $stmt_del_banner = $pdo->prepare("DELETE FROM banners WHERE id = ?");
    if ($stmt_del_banner->execute([$id_excluir])) {
        $sucesso = "Banner excluído com sucesso!";
    } else {
        $erro = "Erro ao excluir banner.";
    }
    
    header("Location: anuncios.php");
    exit;
}

// Alterar status do banner
if (isset($_GET['alterar_status'])) {
    $banner_id = $_GET['alterar_status'];
    
    $stmt_status = $pdo->prepare("SELECT status FROM banners WHERE id = ?");
    $stmt_status->execute([$banner_id]);
    $banner_status = $stmt_status->fetch();
    
    $novo_status = $banner_status['status'] === 'ativo' ? 'inativo' : 'ativo';
    
    $stmt_update = $pdo->prepare("UPDATE banners SET status = ? WHERE id = ?");
    if ($stmt_update->execute([$novo_status, $banner_id])) {
        $sucesso = "Status do banner alterado para " . $novo_status . "!";
    }
    
    header("Location: anuncios.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gerenciar Anúncios - Black Angel</title>
    <link rel="stylesheet" href="../css/admin.css">
    <style>
        .tabs {
            display: flex;
            border-bottom: 2px solid var(--cinza);
            margin-bottom: 2rem;
        }
        
        .tab {
            padding: 1rem 2rem;
            background: var(--cinza-claro);
            border: none;
            cursor: pointer;
            font-weight: 600;
            transition: all 0.3s;
            border-bottom: 3px solid transparent;
        }
        
        .tab.active {
            background: var(--branco);
            color: var(--vermelho);
            border-bottom-color: var(--vermelho);
        }
        
        .tab-content {
            display: none;
        }
        
        .tab-content.active {
            display: block;
        }
        
        .table-container {
            background: var(--branco);
            padding: 2rem;
            border-radius: 12px;
            box-shadow: var(--sombra);
            margin-bottom: 2rem;
        }
        
        .data-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 1rem;
        }
        
        .data-table th {
            background: var(--cinza-claro);
            padding: 1rem;
            text-align: left;
            font-weight: 600;
            color: var(--preto);
            border-bottom: 2px solid var(--cinza);
        }
        
        .data-table td {
            padding: 1rem;
            border-bottom: 1px solid var(--cinza);
            vertical-align: middle;
        }
        
        .data-table tr:hover {
            background: var(--cinza-claro);
        }
        
        .status-badge {
            padding: 0.25rem 0.75rem;
            border-radius: 20px;
            font-size: 0.75rem;
            font-weight: 600;
            text-transform: uppercase;
        }
        
        .status-ativo {
            background: #D1FAE5;
            color: #065F46;
        }
        
        .status-inativo {
            background: #FEE2E2;
            color: #DC2626;
        }
        
        .action-buttons {
            display: flex;
            gap: 0.5rem;
        }
        
        .btn-small {
            padding: 0.5rem 0.75rem;
            font-size: 0.8rem;
            text-decoration: none;
            border-radius: 6px;
            display: flex;
            align-items: center;
            gap: 0.25rem;
            transition: all 0.3s;
            border: none;
            cursor: pointer;
        }
        
        .btn-edit {
            background: var(--azul);
            color: var(--branco);
        }
        
        .btn-delete {
            background: var(--vermelho);
            color: var(--branco);
        }
        
        .btn-status {
            background: var(--verde);
            color: var(--branco);
        }
        
        .banner-preview {
            width: 100px;
            height: 60px;
            object-fit: cover;
            border-radius: 4px;
            border: 1px solid var(--cinza);
        }
        
        .stats-cards {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1.5rem;
            margin-bottom: 2rem;
        }
        
        .stat-card {
            background: var(--branco);
            padding: 1.5rem;
            border-radius: 12px;
            box-shadow: var(--sombra);
            text-align: center;
            border-left: 4px solid var(--vermelho);
        }
        
        .stat-number {
            font-size: 2rem;
            font-weight: 700;
            color: var(--preto);
            margin-bottom: 0.5rem;
        }
        
        .stat-label {
            color: var(--cinza-escuro);
            font-size: 0.9rem;
        }
        
        .empty-state {
            text-align: center;
            padding: 3rem;
            color: var(--cinza-escuro);
        }
        
        .empty-state .icon {
            font-size: 3rem;
            margin-bottom: 1rem;
        }
        
        .posicao-badge {
            background: var(--cinza-escuro);
            color: var(--branco);
            padding: 0.25rem 0.5rem;
            border-radius: 4px;
            font-size: 0.7rem;
            font-weight: 600;
            text-transform: uppercase;
        }
        
        @media (max-width: 768px) {
            .tabs {
                flex-direction: column;
            }
            
            .tab {
                text-align: left;
            }
            
            .action-buttons {
                flex-direction: column;
            }
            
            .data-table {
                font-size: 0.8rem;
            }
            
            .stats-cards {
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
                <a href="dashboard.php" class="nav-item">
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
                <a href="anuncios.php" class="nav-item active">
                    <span>💰</span>
                    <span>Anúncios</span>
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
                    <h1>💰 Gerenciar Anúncios</h1>
                    <p>Controle de anunciantes e banners do Black Angel</p>
                </div>
                
                <div class="action-buttons">
                    <a href="cadastrar_anunciante.php" class="btn btn-primary">
                        <span>👥</span>
                        Novo Anunciante
                    </a>
                    <a href="cadastrar_banner.php" class="btn btn-secondary">
                        <span>🖼️</span>
                        Novo Banner
                    </a>
                </div>
            </div>

            <?php if (isset($sucesso)): ?>
                <div class="alert alert-success">
                    ✅ <?php echo $sucesso; ?>
                </div>
            <?php endif; ?>
            
            <?php if (isset($erro)): ?>
                <div class="alert alert-error">
                    ❌ <?php echo $erro; ?>
                </div>
            <?php endif; ?>

            <!-- Estatísticas -->
            <div class="stats-cards">
                <div class="stat-card">
                    <div class="stat-number"><?php echo count($anunciantes); ?></div>
                    <div class="stat-label">Total de Anunciantes</div>
                </div>
                <div class="stat-card">
                    <div class="stat-number"><?php echo count($banners); ?></div>
                    <div class="stat-label">Total de Banners</div>
                </div>
                <div class="stat-card">
                    <div class="stat-number">
                        <?php 
                        $banners_ativos = array_filter($banners, function($banner) {
                            return $banner['status'] === 'ativo';
                        });
                        echo count($banners_ativos); 
                        ?>
                    </div>
                    <div class="stat-label">Banners Ativos</div>
                </div>
                <div class="stat-card">
                    <div class="stat-number">
                        <?php 
                        $total_cliques = array_sum(array_column($banners, 'cliques'));
                        echo $total_cliques; 
                        ?>
                    </div>
                    <div class="stat-label">Total de Cliques</div>
                </div>
            </div>

            <!-- Tabs -->
            <div class="tabs-container">
                <div class="tabs">
                    <button class="tab active" onclick="abrirTab('tab-anunciantes')">👥 Anunciantes</button>
                    <button class="tab" onclick="abrirTab('tab-banners')">🖼️ Banners</button>
                </div>

                <!-- Tab Anunciantes -->
                <div id="tab-anunciantes" class="tab-content active">
                    <div class="table-container">
                        <div class="table-actions">
                            <h3>Lista de Anunciantes</h3>
                            <a href="cadastrar_anunciante.php" class="btn btn-primary">
                                <span>👥</span>
                                Novo Anunciante
                            </a>
                        </div>

                        <?php if ($anunciantes): ?>
                            <table class="data-table">
                                <thead>
                                    <tr>
                                        <th>Anunciante</th>
                                        <th>Contato</th>
                                        <th>Banners</th>
                                        <th>Data Cadastro</th>
                                        <th>Ações</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($anunciantes as $anunciante): ?>
                                    <tr>
                                        <td>
                                            <strong><?php echo $anunciante['nome']; ?></strong>
                                        </td>
                                        <td>
                                            <div style="font-size: 0.9rem;">
                                                <?php if ($anunciante['email']): ?>
                                                    <div>📧 <?php echo $anunciante['email']; ?></div>
                                                <?php endif; ?>
                                                <?php if ($anunciante['telefone']): ?>
                                                    <div>📞 <?php echo $anunciante['telefone']; ?></div>
                                                <?php endif; ?>
                                            </div>
                                        </td>
                                        <td>
                                            <span class="status-badge <?php echo $anunciante['total_banners'] > 0 ? 'status-ativo' : 'status-inativo'; ?>">
                                                <?php echo $anunciante['total_banners']; ?> banners
                                            </span>
                                        </td>
                                        <td>
                                            <?php echo formatarData($anunciante['created_at']); ?>
                                        </td>
                                        <td>
                                            <div class="action-buttons">
                                                <a href="cadastrar_banner.php?anunciante=<?php echo $anunciante['id']; ?>" 
                                                   class="btn-small btn-status" title="Adicionar Banner">
                                                    <span>🖼️</span>
                                                </a>
                                                <a href="cadastrar_anunciante.php?editar=<?php echo $anunciante['id']; ?>" 
                                                   class="btn-small btn-edit" title="Editar">
                                                    <span>✏️</span>
                                                </a>
                                                <a href="anuncios.php?excluir_anunciante=<?php echo $anunciante['id']; ?>" 
                                                   class="btn-small btn-delete" 
                                                   onclick="return confirm('Tem certeza? Isso excluirá TODOS os banners deste anunciante!')"
                                                   title="Excluir">
                                                    <span>🗑️</span>
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        <?php else: ?>
                            <div class="empty-state">
                                <div class="icon">👥</div>
                                <h3>Nenhum anunciante cadastrado</h3>
                                <p>Comece cadastrando seu primeiro anunciante!</p>
                                <a href="cadastrar_anunciante.php" class="btn btn-primary" style="margin-top: 1rem;">
                                    <span>👥</span>
                                    Cadastrar Primeiro Anunciante
                                </a>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Tab Banners -->
                <div id="tab-banners" class="tab-content">
                    <div class="table-container">
                        <div class="table-actions">
                            <h3>Lista de Banners</h3>
                            <a href="cadastrar_banner.php" class="btn btn-primary">
                                <span>🖼️</span>
                                Novo Banner
                            </a>
                        </div>

                        <?php if ($banners): ?>
                            <table class="data-table">
                                <thead>
                                    <tr>
                                        <th>Banner</th>
                                        <th>Anunciante</th>
                                        <th>Posição</th>
                                        <th>Status</th>
                                        <th>Cliques</th>
                                        <th>Período</th>
                                        <th>Ações</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($banners as $banner): ?>
                                    <tr>
                                        <td>
                                            <?php if ($banner['imagem']): ?>
                                                <img src="../uploads/anuncios/<?php echo $banner['imagem']; ?>" 
                                                     class="banner-preview" 
                                                     alt="<?php echo $banner['titulo'] ?: 'Banner'; ?>">
                                            <?php else: ?>
                                                <div class="banner-preview" style="background: var(--cinza-claro); display: flex; align-items: center; justify-content: center; font-size: 0.7rem; text-align: center;">
                                                    Sem Imagem
                                                </div>
                                            <?php endif; ?>
                                            <div style="font-size: 0.8rem; margin-top: 0.5rem;">
                                                <?php echo $banner['titulo'] ?: 'Sem título'; ?>
                                            </div>
                                        </td>
                                        <td>
                                            <strong><?php echo $banner['anunciante']; ?></strong>
                                        </td>
                                        <td>
                                            <span class="posicao-badge"><?php echo $banner['posicao']; ?></span>
                                        </td>
                                        <td>
                                            <span class="status-badge <?php echo $banner['status'] === 'ativo' ? 'status-ativo' : 'status-inativo'; ?>">
                                                <?php echo $banner['status']; ?>
                                            </span>
                                        </td>
                                        <td>
                                            <strong><?php echo $banner['cliques']; ?></strong> cliques
                                        </td>
                                        <td>
                                            <div style="font-size: 0.8rem;">
                                                <div>Início: <?php echo $banner['data_inicio'] ? date('d/m/Y', strtotime($banner['data_inicio'])) : 'Indefinido'; ?></div>
                                                <div>Fim: <?php echo $banner['data_fim'] ? date('d/m/Y', strtotime($banner['data_fim'])) : 'Indefinido'; ?></div>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="action-buttons">
                                                <a href="anuncios.php?alterar_status=<?php echo $banner['id']; ?>" 
                                                   class="btn-small btn-status" 
                                                   title="<?php echo $banner['status'] === 'ativo' ? 'Desativar' : 'Ativar'; ?>">
                                                    <span><?php echo $banner['status'] === 'ativo' ? '⏸️' : '▶️'; ?></span>
                                                </a>
                                                <a href="cadastrar_banner.php?editar=<?php echo $banner['id']; ?>" 
                                                   class="btn-small btn-edit" title="Editar">
                                                    <span>✏️</span>
                                                </a>
                                                <a href="anuncios.php?excluir_banner=<?php echo $banner['id']; ?>" 
                                                   class="btn-small btn-delete" 
                                                   onclick="return confirm('Tem certeza que deseja excluir este banner?')"
                                                   title="Excluir">
                                                    <span>🗑️</span>
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        <?php else: ?>
                            <div class="empty-state">
                                <div class="icon">🖼️</div>
                                <h3>Nenhum banner cadastrado</h3>
                                <p>Comece criando seu primeiro banner!</p>
                                <a href="cadastrar_banner.php" class="btn btn-primary" style="margin-top: 1rem;">
                                    <span>🖼️</span>
                                    Criar Primeiro Banner
                                </a>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <script>
        function abrirTab(tabName) {
            // Esconder todas as tabs
            const tabContents = document.getElementsByClassName('tab-content');
            for (let i = 0; i < tabContents.length; i++) {
                tabContents[i].classList.remove('active');
            }
            
            // Remover active de todos os tabs
            const tabs = document.getElementsByClassName('tab');
            for (let i = 0; i < tabs.length; i++) {
                tabs[i].classList.remove('active');
            }
            
            // Mostrar tab selecionada
            document.getElementById(tabName).classList.add('active');
            
            // Ativar tab clicada
            event.currentTarget.classList.add('active');
        }
        
        // Verificar se há parâmetro na URL para abrir tab específica
        const urlParams = new URLSearchParams(window.location.search);
        const tabParam = urlParams.get('tab');
        if (tabParam) {
            setTimeout(() => {
                abrirTab('tab-' + tabParam);
            }, 100);
        }
    </script>
</body>
</html>