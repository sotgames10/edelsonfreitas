<?php
require_once 'includes/config.php';
require_once 'includes/functions.php';

$categoria = $_GET['categoria'] ?? '';

// Buscar notícias em destaque para o carrossel (apenas da categoria)
$stmtDestaques = $pdo->prepare("
    SELECT * FROM noticias 
    WHERE categoria = ? AND destaque = 'sim' AND status = 'publicado' 
    ORDER BY created_at DESC 
    LIMIT 5
");
$stmtDestaques->execute([$categoria]);
$destaques = $stmtDestaques->fetchAll();

// Buscar últimas notícias da categoria
$stmtNoticias = $pdo->prepare("
    SELECT * FROM noticias 
    WHERE categoria = ? AND status = 'publicado' 
    ORDER BY created_at DESC 
    LIMIT 15
");
$stmtNoticias->execute([$categoria]);
$noticias = $stmtNoticias->fetchAll();

// Buscar notícias mais visualizadas da categoria
$stmtMaisLidas = $pdo->prepare("
    SELECT * FROM noticias 
    WHERE categoria = ? AND status = 'publicado' 
    ORDER BY visualizacoes DESC 
    LIMIT 3
");
$stmtMaisLidas->execute([$categoria]);
$mais_lidas = $stmtMaisLidas->fetchAll();
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $categoria; ?> - Black Angel</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/categoria.css">
    <?php 
    require_once 'includes/banners.php';
    echo bannerCSS();
    ?>
</head>
<body>
    <!-- Header -->
    <header class="header-jornal">
        <!-- Header Top -->
        <div class="header-top">
            <div class="container">
                <div class="header-info">
                    <span>📅 <?php echo date('d/m/Y'); ?></span>
                    <span>📍 <?php echo obterCidade(); ?></span>
                    <?php if (exibirHorario()): ?>
                        <span>🕒 <?php echo obterHorarioAtual(); ?> (Horário de <?php echo obterCidade(); ?>)</span>
                    <?php endif; ?>
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
<br>
        <!-- Header Main -->
        <div class="header-main">
            <div class="container" style="justify-content: <?php 
                $pos = obterPosicaoLogo();
                echo $pos === 'left' ? 'flex-start' : ($pos === 'right' ? 'flex-end' : 'center');
            ?>;">
                <div class="logo-area" style="align-items: <?php echo obterPosicaoLogo(); ?>;">
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
        </div>
<br>
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
                    <li><a href="categoria.php?categoria=Política">🏛️ Política</a></li>
                    <li><a href="categoria.php?categoria=Economia">💼 Economia</a></li>
                    <li><a href="categoria.php?categoria=Cultura">🎭 Cultura</a></li>
                    <li><a href="categoria.php?categoria=Esportes">⚽ Esportes</a></li>
                    <li><a href="categoria.php?categoria=Local">📍 Local</a></li>
                    <li><a href="contato.php">📞 Contato</a></li>
                </ul>
            </nav>
            
            <!-- Botão Menu Mobile -->
            <button class="menu-toggle">☰</button>
            
            <!-- Overlay Menu Mobile -->
            <div class="menu-overlay"></div>
        </div>
    </header>

    <!-- Carrossel de Destaques da Categoria -->
    <?php if ($destaques): ?>
    <div class="carrossel-destaques">
        <?php foreach ($destaques as $index => $destaque): ?>
        <div class="slide-destaque <?php echo $index === 0 ? 'ativo' : ''; ?>">
            <?php if ($destaque['imagem_destaque']): ?>
                <img src="uploads/noticias/<?php echo $destaque['imagem_destaque']; ?>" alt="<?php echo $destaque['titulo']; ?>">
            <?php else: ?>
                <div style="background: linear-gradient(135deg, var(--vermelho), var(--vermelho-escuro)); height: 100%; display: flex; align-items: center; justify-content: center; color: white; font-size: 2rem;">
                    📰 <?php echo $categoria; ?>
                </div>
            <?php endif; ?>
            <div class="overlay-destaque">
                <div class="container">
                    <h2><?php echo $destaque['titulo']; ?></h2>
                    <p><?php echo limitarTexto($destaque['resumo'], 200); ?></p>
                    <a href="noticia.php?id=<?php echo $destaque['id']; ?>" class="btn-leia-mais">
                        <span>📖</span>
                        Leia Mais
                    </a>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
    <?php endif; ?>

    <!-- Conteúdo Principal -->
    <div class="container">
        <div class="layout-principal">
            <!-- Coluna de Notícias da Categoria -->
<main class="coluna-noticias">
    <h2>
        <span>🎯</span>
        <?php echo $categoria; ?>
    </h2>
    
    <?php if ($noticias): ?>
        <!-- DUAS NOTÍCIAS GRANDES NO TOPO -->
        <div class="layout-duas-grandes">
            <?php 
            // Primeiras 2 notícias em destaque
            for ($i = 0; $i <= 11; $i++): 
                if (isset($noticias[$i])):
                    $noticia = $noticias[$i];
            ?>
            <article class="noticia-card grande">
                <a href="noticia.php?id=<?php echo $noticia['id']; ?>" class="noticia-link">
                    <?php if ($noticia['imagem_destaque']): ?>
                    <div class="noticia-imagem">
                        <img src="uploads/noticias/<?php echo $noticia['imagem_destaque']; ?>" alt="<?php echo $noticia['titulo']; ?>">
                    </div>
                    <?php endif; ?>
                    
                    <div class="noticia-conteudo">
                        <h3><?php echo $noticia['titulo']; ?></h3>
                        <p class="resumo"><?php echo limitarTexto($noticia['resumo'], 120); ?></p>
                       
                    </div>
                </a>
            </article>
            <?php 
                endif;
            endfor; 
            ?>
        </div>

        <!-- RESTANTE DAS NOTÍCIAS DA CATEGORIA -->
        

    <?php else: ?>
        
    <?php endif; ?>
</main>

            <!-- Sidebar -->
            <aside class="sidebar">
                <!-- Banner Sidebar -->
                <div class="banner-sidebar-section">
                    <?php echo exibirBanner('sidebar'); ?>
                </div>

                <!-- Mais Lidas da Categoria -->
                <h3>
                    <span>🔥</span>
                    Mais Lidas em <?php echo $categoria; ?>
                </h3>
                <div class="lista-mais-lidas">
                    <?php if ($mais_lidas): 
                        $contador = 1;
                        foreach ($mais_lidas as $noticia): ?>
                            <div class="item-mais-lida">
                                <span class="numero"><?php echo $contador++; ?>.</span>
                                <a href="noticia.php?id=<?php echo $noticia['id']; ?>">
                                    <?php echo limitarTexto($noticia['titulo'], 50); ?>
                                </a>
                            </div>
                        <?php endforeach; 
                    else: ?>
                        <div class="item-mais-lida">
                            <span class="numero">1.</span>
                            <a href="#">Nenhuma notícia ainda</a>
                        </div>
                    <?php endif; ?>
                </div>

                <!-- Newsletter -->
                <h3>
                    <span>📧</span>
                    Newsletter
                </h3>
                <p>Receba as notícias de <?php echo $categoria; ?> em primeira mão!</p>
                <form class="form-newsletter">
                    <input type="email" placeholder="Seu melhor e-mail" required>
                    <button type="submit" class="btn-leia-mais">
                        <span>✉️</span>
                        Assinar
                    </button>
                </form>

                <!-- Redes Sociais -->
                <h3>
                    <span>🌐</span>
                    Siga-nos
                </h3>
                <div class="redes-sociais">
                    <a href="#" class="btn-social">📘 Facebook</a>
                    <a href="#" class="btn-social">📷 Instagram</a>
                    <a href="#" class="btn-social">🐦 Twitter</a>
                </div>
            </aside>
        </div>
    </div>

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

    <script src="js/carrossel.js"></script>
    <script src="js/menu.js"></script>
</body>
</html>