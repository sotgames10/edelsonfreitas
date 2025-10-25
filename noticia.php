<?php
require_once 'includes/config.php';
require_once 'includes/functions.php';

if (!isset($_GET['id']) || empty($_GET['id'])) {
    header("Location: index.php");
    exit;
}

$noticia_id = $_GET['id'];

// Buscar a notícia
$stmt = $pdo->prepare("
    SELECT n.*, u.nome as autor 
    FROM noticias n 
    LEFT JOIN usuarios u ON n.autor_id = u.id 
    WHERE n.id = ? AND n.status = 'publicado'
");
$stmt->execute([$noticia_id]);
$noticia = $stmt->fetch();

if (!$noticia) {
    header("Location: index.php");
    exit;
}

// Incrementar visualizações
$stmt_views = $pdo->prepare("UPDATE noticias SET visualizacoes = visualizacoes + 1 WHERE id = ?");
$stmt_views->execute([$noticia_id]);

// Buscar notícias relacionadas
$stmt_relacionadas = $pdo->prepare("
    SELECT * FROM noticias 
    WHERE categoria = ? AND id != ? AND status = 'publicado' 
    ORDER BY created_at DESC 
    LIMIT 3
");
$stmt_relacionadas->execute([$noticia['categoria'], $noticia_id]);
$relacionadas = $stmt_relacionadas->fetchAll();
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $noticia['titulo']; ?> - Black Angel</title>
    <link rel="stylesheet" href="css/style.css">
    <?php 
    // Adicionar CSS dos banners
    require_once 'includes/banners.php';
    echo bannerCSS();
    ?>
    <style>
        .noticia-header {
            background: linear-gradient(135deg, var(--vermelho), var(--vermelho-escuro));
            color: var(--branco);
            padding: 4rem 0 2rem;
            margin-bottom: 2rem;
        }
        
        .noticia-titulo {
            font-size: 2.5rem;
            font-weight: 800;
            line-height: 1.2;
            margin-bottom: 1rem;
            text-align: center;
        }
        
        .noticia-meta {
            display: flex;
            justify-content: center;
            gap: 2rem;
            flex-wrap: wrap;
            margin-bottom: 1rem;
            font-size: 0.9rem;
            opacity: 0.9;
        }
        
        .noticia-meta span {
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        
        .noticia-categoria {
            background: var(--branco);
            color: var(--vermelho);
            padding: 0.25rem 1rem;
            border-radius: 20px;
            font-weight: 600;
            font-size: 0.8rem;
            display: inline-block;
            margin-bottom: 1rem;
        }
        
        .noticia-imagem {
            width: 100%;
            max-height: 600px;
            object-fit: cover;
            border-radius: 12px;
            margin: 2rem 0;
            box-shadow: var(--sombra-grande);
        }
        
        .conteudo-principal-full {
            max-width: 1000px;
            margin: 0 auto;
            padding: 0 1rem;
        }
        
        .conteudo-texto {
            font-size: 1.1rem;
            line-height: 1.8;
            color: var(--preto);
            margin-bottom: 3rem;
        }
        
        .conteudo-texto p {
            margin-bottom: 1.5rem;
        }
        
        .conteudo-texto h2, 
        .conteudo-texto h3 {
            color: var(--vermelho);
            margin: 2rem 0 1rem;
        }
        
        .conteudo-texto h2 {
            font-size: 1.8rem;
        }
        
        .conteudo-texto h3 {
            font-size: 1.4rem;
        }
        
        .noticia-actions {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 2rem 0;
            border-top: 1px solid var(--cinza);
            border-bottom: 1px solid var(--cinza);
            margin: 2rem 0;
            flex-wrap: wrap;
            gap: 1rem;
        }
        
        .social-share {
            display: flex;
            gap: 1rem;
            align-items: center;
        }
        
        .btn-share {
            background: var(--cinza-claro);
            color: var(--preto);
            padding: 0.5rem 1rem;
            border-radius: 6px;
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: 0.5rem;
            transition: all 0.3s;
            border: 1px solid var(--cinza);
        }
        
        .btn-share:hover {
            background: var(--cinza);
            transform: translateY(-1px);
        }
        
        .btn-voltar {
            background: var(--vermelho);
            color: var(--branco);
            padding: 0.75rem 1.5rem;
            border-radius: 6px;
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: 0.5rem;
            transition: all 0.3s;
            font-weight: 600;
        }
        
        .btn-voltar:hover {
            background: var(--vermelho-escuro);
            transform: translateY(-1px);
        }
        
        .relacionadas-section {
            margin: 4rem 0;
        }
        
        .relacionadas-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 2rem;
            margin-top: 2rem;
        }
        
        .relacionada-card {
            background: var(--branco);
            border-radius: 12px;
            overflow: hidden;
            box-shadow: var(--sombra);
            transition: all 0.3s;
            border: 1px solid var(--cinza);
        }
        
        .relacionada-card:hover {
            transform: translateY(-4px);
            box-shadow: var(--sombra-grande);
        }
        
        .relacionada-card img {
            width: 100%;
            height: 200px;
            object-fit: cover;
        }
        
        .relacionada-conteudo {
            padding: 1.5rem;
        }
        
        .relacionada-conteudo h3 {
            font-size: 1.2rem;
            margin-bottom: 1rem;
            line-height: 1.3;
        }
        
        .relacionada-conteudo a {
            color: var(--preto);
            text-decoration: none;
        }
        
        .relacionada-conteudo a:hover {
            color: var(--vermelho);
        }
        
        .admin-actions {
            background: var(--cinza-claro);
            padding: 1rem;
            border-radius: 8px;
            margin: 2rem 0;
            display: flex;
            gap: 1rem;
            flex-wrap: wrap;
        }
        
        @media (max-width: 768px) {
            .noticia-titulo {
                font-size: 2rem;
            }
            
            .noticia-meta {
                flex-direction: column;
                gap: 0.5rem;
                align-items: center;
            }
            
            .noticia-actions {
                flex-direction: column;
                align-items: flex-start;
            }
            
            .social-share {
                width: 100%;
                justify-content: center;
            }
            
            .relacionadas-grid {
                grid-template-columns: 1fr;
            }
            
            .conteudo-texto {
                font-size: 1rem;
            }
            
            .noticia-imagem {
                max-height: 400px;
            }
        }
    </style>
</head>
<body>
    <!-- Header -->
    <header class="header-jornal">
        <!-- Header Top -->
        <div class="header-top">
            <div class="container">
                <div class="header-info">
                    <span>📅 <?php echo date('d/m/Y'); ?></span>
                    <span>📍 São Paulo, SP</span>
                    <span>⚡ Notícias em Tempo Real</span>
                </div>
                
                <!-- BOTÃO ADMIN -->
                <?php if (isset($_SESSION['usuario_id'])): ?>
                    <a href="admin/dashboard.php" class="admin-btn">
                        <span>⚡</span>
                        <span>Painel Admin</span>
                    </a>
                <?php else: ?>
                    <a href="admin/login.php" class="admin-btn">
                        <span>🔐</span>
                        <span>Área Admin</span>
                    </a>
                <?php endif; ?>
            </div>
        </div>

<!-- Header Main - COM POSIÇÃO DINÂMICA -->
<?php
$posicao = obterPosicaoLogo();
$classeContainer = 'pos-' . $posicao;
$classeLogoArea = 'pos-' . $posicao;
?>
<!-- Header Main - SIMPLIFICADO -->
<br>
<div class="header-main">
    <div class="container <?php echo $classeContainer; ?>">
        <div class="logo-area <?php echo $classeLogoArea; ?>">
            <?php 
            $logoSite = obterLogoSite();
            if ($logoSite && file_exists($logoSite)): 
            ?>
                <img src="<?php echo $logoSite; ?>" alt="Black Angel" class="logo-img" 
                     style="width: 300px; height: 80px; object-fit: contain;">
            <?php else: ?>
                <h1 class="logo">⚡ Black Angel</h1>
            <?php endif; ?>
            
            <?php if (exibirTextoCabecalho()): ?>
                <p class="slogan"><?php echo obterTextoCabecalho(); ?></p>
            <?php endif; ?>
        </div>
    </div>
</div><br>

        <!-- BANNER TOPO -->
        <div class="banner-topo-section">
            <div class="container">
                <?php echo exibirBanner('topo'); ?>
            </div>
        </div>

        <!-- Menu Principal -->
        <div class="menu-container">
            <nav class="menu-principal">
                <div class="menu-mobile-header">
                    <span>☰ Menu</span>
                    <button class="menu-close">✕</button>
                </div>
                <ul>
                    <li><a href="index.php">🏠 Home</a></li>
                    <li><a href="#">🏛️ Política</a></li>
                    <li><a href="#">💼 Economia</a></li>
                    <li><a href="#">🎭 Cultura</a></li>
                    <li><a href="#">⚽ Esportes</a></li>
                    <li><a href="#">📍 Local</a></li>
                    <li><a href="contato.php">📞 Contato</a></li>
                </ul>
            </nav>
            
            <!-- Botão Menu Mobile -->
            <button class="menu-toggle">☰</button>
            
            <!-- Overlay Menu Mobile -->
            <div class="menu-overlay"></div>
        </div>
    </header>

    <!-- Conteúdo da Notícia -->
    <article class="noticia-content">
        <div class="noticia-header">
            <div class="container">
                <div class="noticia-categoria">
                    <?php echo $noticia['categoria']; ?>
                </div>
                <h1 class="noticia-titulo"><?php echo $noticia['titulo']; ?></h1>
                
                <div class="noticia-meta">
                    <span>✍️ Por <?php echo $noticia['autor']; ?></span>
                    <span>📅 <?php echo formatarData($noticia['created_at']); ?></span>
                    <span>👁️ <?php echo $noticia['visualizacoes'] + 1; ?> visualizações</span>
                    <span>⏱️ <?php echo calcularTempoLeitura($noticia['conteudo']); ?> min de leitura</span>
                </div>
            </div>
        </div>

        <div class="container">
            <!-- Conteúdo Principal - 100% da largura -->
            <main class="conteudo-principal-full">
                <?php if (isset($_SESSION['usuario_id'])): ?>
                    <div class="admin-actions">
                        <a href="admin/cadastrar_noticia.php?editar=<?php echo $noticia['id']; ?>" class="btn btn-primary">
                            <span>✏️</span>
                            Editar Notícia
                        </a>
                        <a href="admin/gerenciar_noticias.php?excluir=<?php echo $noticia['id']; ?>" 
                           class="btn btn-secondary" 
                           onclick="return confirm('Tem certeza que deseja excluir esta notícia?')">
                            <span>🗑️</span>
                            Excluir Notícia
                        </a>
                    </div>
                <?php endif; ?>

                <?php if ($noticia['imagem_destaque']): ?>
                    <img src="uploads/noticias/<?php echo $noticia['imagem_destaque']; ?>" 
                         alt="<?php echo $noticia['titulo']; ?>" 
                         class="noticia-imagem">
                <?php endif; ?>

                <div class="conteudo-texto">
                    <?php echo nl2br(htmlspecialchars($noticia['conteudo'])); ?>
                </div>

                <!-- BANNER MEIO NA NOTÍCIA -->
                <div class="banner-meio-section">
                    <?php echo exibirBanner('meio'); ?>
                </div>

                <div class="noticia-actions">
                    <div class="social-share">
                        <span>Compartilhar:</span>
                        <a href="https://www.facebook.com/sharer/sharer.php?u=<?php echo urlencode('http://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']); ?>" 
                           target="_blank" class="btn-share">
                            <span>📘</span> Facebook
                        </a>
                        <a href="https://twitter.com/intent/tweet?text=<?php echo urlencode($noticia['titulo'] . ' - Black Angel'); ?>&url=<?php echo urlencode('http://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']); ?>" 
                           target="_blank" class="btn-share">
                            <span>🐦</span> Twitter
                        </a>
                        <a href="https://api.whatsapp.com/send?text=<?php echo urlencode($noticia['titulo'] . ' - ' . 'http://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']); ?>" 
                           target="_blank" class="btn-share">
                            <span>💚</span> WhatsApp
                        </a>
                    </div>
                    
                    <a href="index.php" class="btn-voltar">
                        <span>←</span>
                        Voltar para Home
                    </a>
                </div>

                <?php if ($relacionadas): ?>
                    <section class="relacionadas-section">
                        <h2 style="color: var(--vermelho); margin-bottom: 1rem;">📰 Notícias Relacionadas</h2>
                        <div class="relacionadas-grid">
                            <?php foreach ($relacionadas as $relacionada): ?>
                                <article class="relacionada-card">
                                    <?php if ($relacionada['imagem_destaque']): ?>
                                        <img src="uploads/noticias/<?php echo $relacionada['imagem_destaque']; ?>" 
                                             alt="<?php echo $relacionada['titulo']; ?>">
                                    <?php endif; ?>
                                    <div class="relacionada-conteudo">
                                        <h3>
                                            <a href="noticia.php?id=<?php echo $relacionada['id']; ?>">
                                                <?php echo $relacionada['titulo']; ?>
                                            </a>
                              
                                    </div>
                                </article>
                            <?php endforeach; ?>
                        </div>
                    </section>
                <?php endif; ?>
            </main>
        </div>
    </article>

    <!-- BANNER RODAPÉ -->
    <div class="banner-rodape-section">
        <div class="container">
            <?php echo exibirBanner('rodape'); ?>
        </div>
    </div>
<!-- Footer com Ícones -->
<footer class="footer">
    <div class="container">
        <div class="footer-links">
            <a href="index.php">🏠 Home</a>
            <a href="categoria.php?categoria=Política">🏛️ Política</a>
            <a href="categoria.php?categoria=Economia">💼 Economia</a>
            <a href="categoria.php?categoria=Cultura">🎭 Cultura</a>
            <a href="categoria.php?categoria=Esportes">⚽ Esportes</a>
            <a href="categoria.php?categoria=Local">📍 Local</a>
            <a href="contato.php">📞 Contato</a>
        </div>
        
        <div class="footer-bottom">
            <p>&copy; 2024 Black Angel - Edelson Freitas. Todos os direitos reservados.</p>
        </div>
    </div>
</footer>

    <script src="js/menu.js"></script>
</body>
</html>