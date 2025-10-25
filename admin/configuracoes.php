<?php
require_once '../includes/auth.php';
require_once '../includes/functions.php';
verificarAuth();

// Processar remoção de logos
if (isset($_POST['remover_site_logo'])) {
    if (!empty($configuracoes['site_logo'])) {
        $arquivo_logo = '../uploads/' . $configuracoes['site_logo'];
        if (file_exists($arquivo_logo)) {
            unlink($arquivo_logo);
        }
        salvarConfiguracao('site_logo', '');
        $_SESSION['sucesso'] = 'Logo do site removida com sucesso!';
        header('Location: configuracoes.php');
        exit;
    }
}

// Processar formulário de configurações
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Processar uploads de imagens primeiro
    if (isset($_FILES['site_logo']) && $_FILES['site_logo']['error'] === UPLOAD_ERR_OK) {
        $arquivo = $_FILES['site_logo'];
        $extensao = strtolower(pathinfo($arquivo['name'], PATHINFO_EXTENSION));
        $extensoesPermitidas = ['png', 'jpg', 'jpeg', 'gif', 'svg'];
        
        if (in_array($extensao, $extensoesPermitidas)) {
            // Criar pasta uploads se não existir
            if (!is_dir('../uploads')) {
                mkdir('../uploads', 0755, true);
            }
            
            $nomeArquivo = 'site_logo_' . time() . '.' . $extensao;
            $caminhoDestino = '../uploads/' . $nomeArquivo;
            
            if (move_uploaded_file($arquivo['tmp_name'], $caminhoDestino)) {
                salvarConfiguracao('site_logo', $nomeArquivo);
            }
        }
    }
    
    // Processar campos de texto
    foreach ($_POST as $chave => $valor) {
        // Pular campos de arquivo
        if ($chave === 'site_logo') {
            continue;
        }
        
        // Pular botões de remover
        if ($chave === 'remover_site_logo') {
            continue;
        }
        
        // Para checkboxes, verificar se estão marcados
        if (strpos($chave, 'exibir_') === 0) {
            $valor = isset($_POST[$chave]) ? '1' : '0';
        }
        
        salvarConfiguracao($chave, $valor);
    }
    
    $_SESSION['sucesso'] = 'Configurações salvas com sucesso!';
    header('Location: configuracoes.php');
    exit;
}

// Buscar configurações atuais
$configuracoes = buscarConfiguracoes();
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Configurações - Black Angel</title>
    <link rel="stylesheet" href="../css/admin.css">
    <style>
        .config-section {
            background: var(--branco);
            padding: 2rem;
            border-radius: 12px;
            box-shadow: var(--sombra);
            margin-bottom: 2rem;
        }
        
        .config-section h3 {
            color: var(--preto);
            margin-bottom: 1.5rem;
            font-size: 1.3rem;
            border-bottom: 2px solid var(--vermelho);
            padding-bottom: 0.5rem;
        }
        
        .form-group {
            margin-bottom: 1.5rem;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: 600;
            color: var(--preto);
        }
        
        .form-control {
            width: 100%;
            padding: 0.75rem;
            border: 2px solid var(--cinza);
            border-radius: 8px;
            font-size: 1rem;
            transition: border-color 0.3s;
        }
        
        .form-control:focus {
            outline: none;
            border-color: var(--vermelho);
        }
        
        .checkbox-group {
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        
        .checkbox-group input[type="checkbox"] {
            width: 18px;
            height: 18px;
        }
        
        .logo-preview {
            max-width: 300px;
            max-height: 150px;
            margin-top: 1rem;
            border: 2px dashed var(--cinza);
            padding: 1rem;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            background: var(--cinza-claro);
        }
        
        .logo-preview img {
            max-width: 100%;
            max-height: 100px;
        }
        
        .logo-actions {
            margin-top: 10px;
            text-align: center;
        }
        
        .btn-remover-logo {
            background: #dc2626;
            color: white;
            border: none;
            padding: 8px 15px;
            border-radius: 5px;
            cursor: pointer;
            font-size: 14px;
            transition: background 0.3s;
            margin-top: 10px;
        }
        
        .btn-remover-logo:hover {
            background: #b91c1c;
        }
        
        .logo-info {
            font-size: 0.8rem;
            color: var(--cinza-escuro);
            margin-top: 0.5rem;
        }
        
        .current-logo {
            margin-top: 0.5rem;
            font-size: 0.9rem;
            color: var(--cinza-escuro);
        }
        
        .radio-group {
            display: flex;
            gap: 2rem;
            margin-top: 0.5rem;
        }
        
        .radio-option {
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        
        .btn-salvar {
            background: var(--vermelho);
            color: var(--branco);
            padding: 1rem 2rem;
            border: none;
            border-radius: 8px;
            font-size: 1.1rem;
            font-weight: 600;
            cursor: pointer;
            transition: background 0.3s;
        }
        
        .btn-salvar:hover {
            background: #cc0000;
        }
        
        .config-actions {
            text-align: center;
            margin-top: 2rem;
            padding-top: 2rem;
            border-top: 2px solid var(--cinza);
        }
        
        .alert {
            padding: 1rem;
            border-radius: 8px;
            margin-bottom: 2rem;
        }
        
        .alert-success {
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        
        .alert-error {
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
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
                <a href="anuncios.php" class="nav-item">
                    <span>💰</span>
                    <span>Anúncios</span>
                </a>
                <a href="configuracoes.php" class="nav-item active">
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
                    <h1>⚙️ Configurações do Site</h1>
                    <p>Personalize a aparência e configurações do portal</p>
                </div>
            </div>

            <?php if (isset($_SESSION['sucesso'])): ?>
                <div class="alert alert-success">
                    ✅ <?php echo $_SESSION['sucesso']; unset($_SESSION['sucesso']); ?>
                </div>
            <?php endif; ?>

            <?php if (isset($_SESSION['erro'])): ?>
                <div class="alert alert-error">
                    ❌ <?php echo $_SESSION['erro']; unset($_SESSION['erro']); ?>
                </div>
            <?php endif; ?>

            <form action="" method="POST" enctype="multipart/form-data">
                <!-- SEÇÃO: LOGO E CABEÇALHO -->
                <div class="config-section">
                    <h3>🎨 Logo e Cabeçalho</h3>
                    
                    <div class="form-group">
                        <label for="site_logo">Logo do Site</label>
                        <input type="file" id="site_logo" name="site_logo" accept="image/*" class="form-control">
                        
                        <?php if (!empty($configuracoes['site_logo']) && file_exists('../uploads/' . $configuracoes['site_logo'])): ?>
                        <div class="logo-preview">
                            <img src="../uploads/<?php echo $configuracoes['site_logo']; ?>" alt="Logo atual">
                            <div class="logo-actions">
                                <button type="submit" name="remover_site_logo" value="1" class="btn-remover-logo" onclick="return confirm('Tem certeza que deseja remover esta logo?')">
                                    🗑️ Remover Logo
                                </button>
                            </div>
                        </div>
                        <div class="current-logo">
                            ✅ Logo atual: <?php echo $configuracoes['site_logo']; ?>
                        </div>
                        <?php else: ?>
                        <div class="logo-info">
                            ⚠️ Nenhuma logo definida. O texto "⚡ Black Angel" será exibido.
                        </div>
                        <?php endif; ?>
                    </div>
                    
                    <div class="form-group">
                        <label>Posição da Logo</label>
                        <div class="radio-group">
                            <div class="radio-option">
                                <input type="radio" id="pos_left" name="logo_posicao" value="left" 
                                    <?php echo ($configuracoes['logo_posicao'] ?? 'left') === 'left' ? 'checked' : ''; ?>>
                                <label for="pos_left">Esquerda</label>
                            </div>
                            <div class="radio-option">
                                <input type="radio" id="pos_center" name="logo_posicao" value="center" 
                                    <?php echo ($configuracoes['logo_posicao'] ?? 'left') === 'center' ? 'checked' : ''; ?>>
                                <label for="pos_center">Centro</label>
                            </div>
                            <div class="radio-option">
                                <input type="radio" id="pos_right" name="logo_posicao" value="right" 
                                    <?php echo ($configuracoes['logo_posicao'] ?? 'left') === 'right' ? 'checked' : ''; ?>>
                                <label for="pos_right">Direita</label>
                            </div>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="texto_cabecalho">Texto do Cabeçalho</label>
                        <input type="text" id="texto_cabecalho" name="texto_cabecalho" 
                               value="<?php echo htmlspecialchars($configuracoes['texto_cabecalho'] ?? 'Jornalismo Independente'); ?>" 
                               class="form-control" placeholder="Ex: Jornalismo Independente">
                    </div>
                    
                    <div class="form-group">
                        <div class="checkbox-group">
                            <input type="checkbox" id="exibir_texto_cabecalho" name="exibir_texto_cabecalho" value="1"
                                <?php echo ($configuracoes['exibir_texto_cabecalho'] ?? '1') === '1' ? 'checked' : ''; ?>>
                            <label for="exibir_texto_cabecalho">Exibir texto no cabeçalho</label>
                        </div>
                    </div>
                </div>

                <!-- SEÇÃO: LOCALIZAÇÃO E HORÁRIO -->
                <div class="config-section">
                    <h3>📍 Localização e Horário</h3>
                    
                    <div class="form-group">
                        <label for="cidade">Cidade</label>
                        <input type="text" id="cidade" name="cidade" 
                               value="<?php echo htmlspecialchars($configuracoes['cidade'] ?? 'São Paulo'); ?>" 
                               class="form-control" placeholder="Ex: São Paulo">
                    </div>
                    
                    <div class="form-group">
                        <label for="fuso_horario">Fuso Horário</label>
                        <select id="fuso_horario" name="fuso_horario" class="form-control">
                            <option value="America/Sao_Paulo" <?php echo ($configuracoes['fuso_horario'] ?? 'America/Sao_Paulo') === 'America/Sao_Paulo' ? 'selected' : ''; ?>>Horário de Brasília</option>
                            <option value="America/Noronha" <?php echo ($configuracoes['fuso_horario'] ?? '') === 'America/Noronha' ? 'selected' : ''; ?>>Fernando de Noronha</option>
                            <option value="America/Manaus" <?php echo ($configuracoes['fuso_horario'] ?? '') === 'America/Manaus' ? 'selected' : ''; ?>>Amazonas</option>
                            <option value="America/Rio_Branco" <?php echo ($configuracoes['fuso_horario'] ?? '') === 'America/Rio_Branco' ? 'selected' : ''; ?>>Acre</option>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <div class="checkbox-group">
                            <input type="checkbox" id="exibir_horario" name="exibir_horario" value="1"
                                <?php echo ($configuracoes['exibir_horario'] ?? '1') === '1' ? 'checked' : ''; ?>>
                            <label for="exibir_horario">Exibir horário no site</label>
                        </div>
                    </div>
                </div>

                <!-- SEÇÃO: CATEGORIAS DOS BLOCOS -->
                <div class="config-section">
                    <h3>📂 Categorias dos Blocos</h3>
                    
                    <div class="form-group">
                        <label for="categoria_1">Categoria do Bloco 1</label>
                        <input type="text" id="categoria_1" name="categoria_1" 
                               value="<?php echo htmlspecialchars($configuracoes['categoria_1'] ?? 'Política'); ?>" 
                               class="form-control" placeholder="Ex: Política">
                    </div>
                    
                    <div class="form-group">
                        <label for="categoria_2">Categoria do Bloco 2</label>
                        <input type="text" id="categoria_2" name="categoria_2" 
                               value="<?php echo htmlspecialchars($configuracoes['categoria_2'] ?? 'Economia'); ?>" 
                               class="form-control" placeholder="Ex: Economia">
                    </div>
                    
                    <div class="form-group">
                        <label for="categoria_3">Categoria do Bloco 3</label>
                        <input type="text" id="categoria_3" name="categoria_3" 
                               value="<?php echo htmlspecialchars($configuracoes['categoria_3'] ?? 'Cultura'); ?>" 
                               class="form-control" placeholder="Ex: Cultura">
                    </div>
                </div>

                <!-- BOTÃO SALVAR -->
                <div class="config-actions">
                    <button type="submit" class="btn-salvar">
                        💾 Salvar Configurações
                    </button>
                </div>
            </form>
        </main>
    </div>
</body>
</html>