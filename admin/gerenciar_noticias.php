<?php
require_once '../includes/auth.php';
require_once '../includes/functions.php';
verificarAuth();

// Buscar todas as notícias
$stmt = $pdo->prepare("
    SELECT n.*, u.nome as autor 
    FROM noticias n 
    LEFT JOIN usuarios u ON n.autor_id = u.id 
    ORDER BY n.created_at DESC
");
$stmt->execute();
$noticias = $stmt->fetchAll();

// Excluir notícia
if (isset($_GET['excluir'])) {
    $id_excluir = $_GET['excluir'];
    
    // Buscar imagem para excluir
    $stmt_img = $pdo->prepare("SELECT imagem_destaque FROM noticias WHERE id = ?");
    $stmt_img->execute([$id_excluir]);
    $noticia_img = $stmt_img->fetch();
    
    // Excluir imagem do servidor
    if ($noticia_img && $noticia_img['imagem_destaque']) {
        $caminho_imagem = '../uploads/noticias/' . $noticia_img['imagem_destaque'];
        if (file_exists($caminho_imagem)) {
            unlink($caminho_imagem);
        }
    }
    
    // Excluir do banco
    $stmt_del = $pdo->prepare("DELETE FROM noticias WHERE id = ?");
    if ($stmt_del->execute([$id_excluir])) {
        $sucesso = "Notícia excluída com sucesso!";
    } else {
        $erro = "Erro ao excluir notícia.";
    }
    
    // Recarregar a página sem o parâmetro
    header("Location: gerenciar_noticias.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gerenciar Notícias - Black Angel</title>
    <link rel="stylesheet" href="../css/admin.css">
    <style>
        .table-container {
            background: var(--branco);
            padding: 2rem;
            border-radius: 12px;
            box-shadow: var(--sombra);
            overflow-x: auto;
        }
        
        .news-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 1rem;
        }
        
        .news-table th {
            background: var(--cinza-claro);
            padding: 1rem;
            text-align: left;
            font-weight: 600;
            color: var(--preto);
            border-bottom: 2px solid var(--cinza);
        }
        
        .news-table td {
            padding: 1rem;
            border-bottom: 1px solid var(--cinza);
            vertical-align: middle;
        }
        
        .news-table tr:hover {
            background: var(--cinza-claro);
        }
        
        .noticia-titulo {
            font-weight: 600;
            color: var(--preto);
            display: block;
            line-height: 1.4;
        }
        
        .noticia-meta {
            display: flex;
            gap: 1rem;
            font-size: 0.8rem;
            color: var(--cinza-escuro);
            margin-top: 0.5rem;
            flex-wrap: wrap;
        }
        
        .status-badge {
            padding: 0.25rem 0.75rem;
            border-radius: 20px;
            font-size: 0.75rem;
            font-weight: 600;
            text-transform: uppercase;
        }
        
        .status-publicado {
            background: #D1FAE5;
            color: #065F46;
        }
        
        .status-rascunho {
            background: #FEF3C7;
            color: #92400E;
        }
        
        .destaque-badge {
            background: var(--vermelho);
            color: var(--branco);
            padding: 0.25rem 0.5rem;
            border-radius: 4px;
            font-size: 0.7rem;
            font-weight: 600;
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
        }
        
        .btn-edit {
            background: var(--azul);
            color: var(--branco);
        }
        
        .btn-delete {
            background: var(--vermelho);
            color: var(--branco);
        }
        
        .btn-view {
            background: var(--verde);
            color: var(--branco);
        }
        
        .table-actions {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1.5rem;
            flex-wrap: wrap;
            gap: 1rem;
        }
        
        .search-box {
            display: flex;
            gap: 0.5rem;
        }
        
        .search-box input {
            padding: 0.5rem;
            border: 2px solid var(--cinza);
            border-radius: 6px;
            min-width: 250px;
        }
        
        .filters {
            display: flex;
            gap: 1rem;
            flex-wrap: wrap;
        }
        
        .filter-select {
            padding: 0.5rem;
            border: 2px solid var(--cinza);
            border-radius: 6px;
            background: var(--branco);
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
        
        @media (max-width: 768px) {
            .table-actions {
                flex-direction: column;
                align-items: stretch;
            }
            
            .search-box {
                width: 100%;
            }
            
            .search-box input {
                min-width: auto;
                flex: 1;
            }
            
            .filters {
                justify-content: center;
            }
            
            .news-table {
                font-size: 0.8rem;
            }
            
            .action-buttons {
                flex-direction: column;
            }
            
            .noticia-meta {
                flex-direction: column;
                gap: 0.25rem;
            }
            
            .news-table th:nth-child(1),
            .news-table td:nth-child(1) {
                width: 40%;
            }
            
            .news-table th:nth-child(2),
            .news-table td:nth-child(2) {
                width: 15%;
            }
            
            .news-table th:nth-child(3),
            .news-table td:nth-child(3) {
                width: 15%;
            }
            
            .news-table th:nth-child(4),
            .news-table td:nth-child(4) {
                width: 15%;
            }
            
            .news-table th:nth-child(5),
            .news-table td:nth-child(5) {
                width: 15%;
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
                <a href="gerenciar_noticias.php" class="nav-item active">
                    <span>📰</span>
                    <span>Gerenciar Notícias</span>
                </a>
                <a href="anuncios.php" class="nav-item">
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
                    <h1>📰 Gerenciar Notícias</h1>
                    <p>Gerencie todas as notícias do Black Angel</p>
                </div>
                
                <a href="cadastrar_noticia.php" class="btn btn-primary">
                    <span>📝</span>
                    Nova Notícia
                </a>
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

            <div class="table-container">
                <div class="table-actions">
                    <div class="search-box">
                        <input type="text" id="searchInput" placeholder="🔍 Buscar notícias...">
                        <button class="btn btn-secondary" onclick="buscarNoticias()">Buscar</button>
                    </div>
                    
                    <div class="filters">
                        <select class="filter-select" id="filterStatus" onchange="filtrarNoticias()">
                            <option value="">Todos os status</option>
                            <option value="publicado">Publicadas</option>
                            <option value="rascunho">Rascunhos</option>
                        </select>
                        
                        <select class="filter-select" id="filterCategoria" onchange="filtrarNoticias()">
                            <option value="">Todas categorias</option>
                            <option value="Política">Política</option>
                            <option value="Economia">Economia</option>
                            <option value="Cultura">Cultura</option>
                            <option value="Esportes">Esportes</option>
                            <option value="Local">Local</option>
                        </select>
                    </div>
                </div>

                <?php if ($noticias): ?>
                    <table class="news-table" id="newsTable">
                        <thead>
                            <tr>
                                <th style="width: 40%;">Notícia</th>
                                <th style="width: 15%;">Categoria</th>
                                <th style="width: 15%;">Status</th>
                                <th style="width: 15%;">Data</th>
                                <th style="width: 15%;">Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($noticias as $noticia): ?>
                            <tr>
                                <td>
                                    <span class="noticia-titulo">
                                        <?php echo $noticia['titulo']; ?>
                                        <?php if ($noticia['destaque'] === 'sim'): ?>
                                            
                      <?php endif; ?>
                          </span>
                                   
                                </td>
                                <td>
                                    <?php echo $noticia['categoria']; ?>
                                </td>
                                <td>
                                    <span class="status-badge <?php echo $noticia['status'] === 'publicado' ? 'status-publicado' : 'status-rascunho'; ?>">
                                        <?php echo $noticia['status']; ?>
                                    </span>
                                </td>
                                <td>
                                    <?php echo formatarData($noticia['created_at']); ?>
                                </td>
                                <td>
                                    <div class="action-buttons">
                                        <a href="../noticia.php?id=<?php echo $noticia['id']; ?>" 
                                           target="_blank" class="btn-small btn-view" title="Ver no site">
                                            <span>👁️</span>
                                        </a>
                                        <a href="cadastrar_noticia.php?editar=<?php echo $noticia['id']; ?>" 
                                           class="btn-small btn-edit" title="Editar">
                                            <span>✏️</span>
                                        </a>
                                        <a href="gerenciar_noticias.php?excluir=<?php echo $noticia['id']; ?>" 
                                           class="btn-small btn-delete" 
                                           onclick="return confirm('Tem certeza que deseja excluir esta notícia?')"
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
                        <div class="icon">📝</div>
                        <h3>Nenhuma notícia encontrada</h3>
                        <p>Comece criando sua primeira notícia!</p>
                        <a href="cadastrar_noticia.php" class="btn btn-primary" style="margin-top: 1rem;">
                            <span>📝</span>
                            Criar Primeira Notícia
                        </a>
                    </div>
                <?php endif; ?>
            </div>
        </main>
    </div>

    <script>
        function buscarNoticias() {
            const termo = document.getElementById('searchInput').value.toLowerCase();
            const linhas = document.querySelectorAll('#newsTable tbody tr');
            
            linhas.forEach(linha => {
                const texto = linha.textContent.toLowerCase();
                if (texto.includes(termo)) {
                    linha.style.display = '';
                } else {
                    linha.style.display = 'none';
                }
            });
        }
        
        function filtrarNoticias() {
            const status = document.getElementById('filterStatus').value;
            const categoria = document.getElementById('filterCategoria').value;
            const linhas = document.querySelectorAll('#newsTable tbody tr');
            
            linhas.forEach(linha => {
                const statusLinha = linha.querySelector('.status-badge').textContent.trim();
                const categoriaLinha = linha.children[1].textContent.trim();
                
                const statusMatch = !status || statusLinha === status;
                const categoriaMatch = !categoria || categoriaLinha === categoria;
                
                if (statusMatch && categoriaMatch) {
                    linha.style.display = '';
                } else {
                    linha.style.display = 'none';
                }
            });
        }
        
        // Permitir busca com Enter
        document.getElementById('searchInput').addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                buscarNoticias();
            }
        });
    </script>
</body>
</html>