<?php
require_once '../includes/auth.php';
require_once '../includes/functions.php';
verificarAuth();

$edicao = false;
$banner_editar = null;
$anunciante_selecionado = null;

// Verificar se é edição
if (isset($_GET['editar'])) {
    $edicao = true;
    $banner_id = $_GET['editar'];
    
    $stmt = $pdo->prepare("
        SELECT b.*, a.nome as anunciante_nome 
        FROM banners b 
        LEFT JOIN anunciantes a ON b.anunciante_id = a.id 
        WHERE b.id = ?
    ");
    $stmt->execute([$banner_id]);
    $banner_editar = $stmt->fetch();
    
    if (!$banner_editar) {
        header("Location: anuncios.php");
        exit;
    }
    
    $anunciante_selecionado = $banner_editar['anunciante_id'];
}

// Verificar se veio de um anunciante específico
if (isset($_GET['anunciante'])) {
    $anunciante_selecionado = $_GET['anunciante'];
}

// Buscar anunciantes para o select
$stmt_anunciantes = $pdo->prepare("SELECT id, nome FROM anunciantes ORDER BY nome");
$stmt_anunciantes->execute();
$anunciantes = $stmt_anunciantes->fetchAll();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $anunciante_id = $_POST['anunciante_id'];
    $titulo = $_POST['titulo'];
    $link = $_POST['link'];
    $posicao = $_POST['posicao'];
    $status = $_POST['status'];
    $data_inicio = $_POST['data_inicio'] ?: null;
    $data_fim = $_POST['data_fim'] ?: null;
    
    if ($edicao) {
        // EDIÇÃO - Atualizar banner existente
        if (isset($_FILES['imagem']) && $_FILES['imagem']['error'] === 0) {
            // Upload da nova imagem
            $extensao = strtolower(pathinfo($_FILES['imagem']['name'], PATHINFO_EXTENSION));
            $extensoes_permitidas = ['jpg', 'jpeg', 'png', 'webp', 'gif'];
            
            if (in_array($extensao, $extensoes_permitidas)) {
                $nome_arquivo = uniqid() . '.' . $extensao;
                $caminho_upload = '../uploads/anuncios/' . $nome_arquivo;
                
                if (move_uploaded_file($_FILES['imagem']['tmp_name'], $caminho_upload)) {
                    // Excluir imagem antiga se existir
                    if ($banner_editar['imagem']) {
                        $caminho_antigo = '../uploads/anuncios/' . $banner_editar['imagem'];
                        if (file_exists($caminho_antigo)) {
                            unlink($caminho_antigo);
                        }
                    }
                    $imagem = $nome_arquivo;
                } else {
                    $imagem = $banner_editar['imagem'];
                }
            } else {
                $imagem = $banner_editar['imagem'];
            }
        } else {
            $imagem = $banner_editar['imagem'];
        }
        
        // Atualizar no banco
        $stmt = $pdo->prepare("
            UPDATE banners 
            SET anunciante_id = ?, titulo = ?, imagem = ?, link = ?, 
                posicao = ?, status = ?, data_inicio = ?, data_fim = ? 
            WHERE id = ?
        ");
        
        if ($stmt->execute([$anunciante_id, $titulo, $imagem, $link, $posicao, $status, $data_inicio, $data_fim, $banner_editar['id']])) {
            $sucesso = "Banner atualizado com sucesso!";
            // Atualizar dados do banner em edição
            $banner_editar = array_merge($banner_editar, [
                'anunciante_id' => $anunciante_id,
                'titulo' => $titulo,
                'link' => $link,
                'posicao' => $posicao,
                'status' => $status,
                'data_inicio' => $data_inicio,
                'data_fim' => $data_fim,
                'imagem' => $imagem
            ]);
        } else {
            $erro = "Erro ao atualizar banner. Tente novamente.";
        }
    } else {
        // CADASTRO - Novo banner
        $imagem = null;
        if (isset($_FILES['imagem']) && $_FILES['imagem']['error'] === 0) {
            $extensao = strtolower(pathinfo($_FILES['imagem']['name'], PATHINFO_EXTENSION));
            $extensoes_permitidas = ['jpg', 'jpeg', 'png', 'webp', 'gif'];
            
            if (in_array($extensao, $extensoes_permitidas)) {
                $nome_arquivo = uniqid() . '.' . $extensao;
                $caminho_upload = '../uploads/anuncios/' . $nome_arquivo;
                
                if (move_uploaded_file($_FILES['imagem']['tmp_name'], $caminho_upload)) {
                    $imagem = $nome_arquivo;
                }
            }
        }
        
        // Inserir no banco
        $stmt = $pdo->prepare("
            INSERT INTO banners (anunciante_id, titulo, imagem, link, posicao, status, data_inicio, data_fim) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?)
        ");
        
        if ($stmt->execute([$anunciante_id, $titulo, $imagem, $link, $posicao, $status, $data_inicio, $data_fim])) {
            $sucesso = "Banner cadastrado com sucesso!";
            // Limpar formulário
            $_POST = array();
            $edicao = false;
        } else {
            $erro = "Erro ao cadastrar banner. Tente novamente.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $edicao ? 'Editar Banner' : 'Novo Banner'; ?> - Black Angel</title>
    <link rel="stylesheet" href="../css/admin.css">
    <style>
        .editor-container {
            background: var(--branco);
            padding: 2rem;
            border-radius: 12px;
            box-shadow: var(--sombra);
            margin-bottom: 2rem;
        }
        
        .form-group {
            margin-bottom: 1.5rem;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 0.5rem;
            color: var(--preto);
            font-weight: 600;
        }
        
        .form-control {
            width: 100%;
            padding: 0.75rem;
            border: 2px solid var(--cinza);
            border-radius: 8px;
            font-size: 1rem;
            transition: all 0.3s;
            font-family: inherit;
        }
        
        .form-control:focus {
            outline: none;
            border-color: var(--vermelho);
            box-shadow: 0 0 0 3px rgba(220, 38, 38, 0.1);
        }
        
        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1rem;
        }
        
        .btn-group {
            display: flex;
            gap: 1rem;
            margin-top: 2rem;
        }
        
        .alert {
            padding: 1rem;
            border-radius: 8px;
            margin-bottom: 1.5rem;
            font-weight: 500;
        }
        
        .alert-success {
            background: #D1FAE5;
            color: #065F46;
            border: 1px solid #A7F3D0;
        }
        
        .alert-error {
            background: #FEE2E2;
            color: #DC2626;
            border: 1px solid #FECACA;
        }
        
        .upload-area {
            border: 2px dashed var(--cinza);
            border-radius: 8px;
            padding: 2rem;
            text-align: center;
            transition: all 0.3s;
            cursor: pointer;
            background: var(--cinza-claro);
        }
        
        .upload-area:hover {
            border-color: var(--vermelho);
            background: var(--branco);
        }
        
        .upload-area.dragover {
            border-color: var(--vermelho);
            background: var(--cinza-claro);
        }
        
        .preview-imagem {
            max-width: 300px;
            max-height: 200px;
            margin-top: 1rem;
            border-radius: 8px;
            border: 2px solid var(--cinza);
            display: <?php echo ($edicao && $banner_editar['imagem']) ? 'block' : 'none'; ?>;
        }
        
        .dimensoes-info {
            background: var(--cinza-claro);
            padding: 1rem;
            border-radius: 8px;
            margin-top: 1rem;
            font-size: 0.9rem;
        }
        
        .dimensao-item {
            display: flex;
            justify-content: space-between;
            margin-bottom: 0.5rem;
            padding-bottom: 0.5rem;
            border-bottom: 1px solid var(--cinza);
        }
        
        .dimensao-item:last-child {
            margin-bottom: 0;
            border-bottom: none;
        }
        
        .posicao-info {
            background: var(--cinza-claro);
            padding: 1rem;
            border-radius: 8px;
            margin-top: 0.5rem;
            font-size: 0.9rem;
        }
        
        .info-icon {
            color: var(--vermelho);
            font-weight: bold;
        }
        
        @media (max-width: 768px) {
            .form-row {
                grid-template-columns: 1fr;
            }
            
            .btn-group {
                flex-direction: column;
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
                    <h1><?php echo $edicao ? '🖼️ Editar Banner' : '🖼️ Novo Banner'; ?></h1>
                    <p><?php echo $edicao ? 'Edite as informações do banner' : 'Crie um novo banner publicitário'; ?></p>
                </div>
                
                <?php if ($edicao): ?>
                    <a href="../uploads/anuncios/<?php echo $banner_editar['imagem']; ?>" target="_blank" class="btn btn-primary">
                        <span>👁️</span>
                        Ver Banner Original
                    </a>
                <?php endif; ?>
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

            <div class="editor-container">
                <form method="POST" enctype="multipart/form-data" id="formBanner">
                    <div class="form-group">
                        <label for="anunciante_id">🏢 Anunciante *</label>
                        <select id="anunciante_id" name="anunciante_id" class="form-control" required>
                            <option value="">Selecione um anunciante</option>
                            <?php foreach ($anunciantes as $anunciante): ?>
                                <option value="<?php echo $anunciante['id']; ?>" 
                                    <?php echo ($edicao ? $banner_editar['anunciante_id'] : $anunciante_selecionado) == $anunciante['id'] ? 'selected' : ''; ?>>
                                    <?php echo $anunciante['nome']; ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <?php if (empty($anunciantes)): ?>
                            <small style="color: var(--vermelho);">
                                ⚠️ Nenhum anunciante cadastrado. 
                                <a href="cadastrar_anunciante.php">Cadastre um anunciante primeiro</a>.
                            </small>
                        <?php endif; ?>
                    </div>

                    <div class="form-group">
                        <label for="titulo">📌 Título do Banner</label>
                        <input type="text" id="titulo" name="titulo" class="form-control" 
                               value="<?php echo $edicao ? $banner_editar['titulo'] : ($_POST['titulo'] ?? ''); ?>" 
                               placeholder="Ex: Promoção Especial - Loja XYZ">
                        <small style="color: var(--cinza-escuro);">Título para identificação interna</small>
                    </div>

                    <div class="form-group">
                        <label for="link">🔗 Link de Destino</label>
                        <input type="url" id="link" name="link" class="form-control" 
                               value="<?php echo $edicao ? $banner_editar['link'] : ($_POST['link'] ?? ''); ?>" 
                               placeholder="https://www.exemplo.com.br">
                        <small style="color: var(--cinza-escuro);">URL para onde o usuário será direcionado ao clicar</small>
                    </div>

                    <div class="form-group">
                        <label for="posicao">📍 Posição no Site *</label>
                        <select id="posicao" name="posicao" class="form-control" required>
                            <option value="">Selecione a posição</option>
                            <option value="topo" <?php echo ($edicao ? $banner_editar['posicao'] : ($_POST['posicao'] ?? '')) === 'topo' ? 'selected' : ''; ?>>🔝 Topo - 728×90px (Header)</option>
                            <option value="sidebar" <?php echo ($edicao ? $banner_editar['posicao'] : ($_POST['posicao'] ?? '')) === 'sidebar' ? 'selected' : ''; ?>>📏 Sidebar - 300×250px (Lateral Direita)</option>
                            <option value="meio" <?php echo ($edicao ? $banner_editar['posicao'] : ($_POST['posicao'] ?? '')) === 'meio' ? 'selected' : ''; ?>>📄 Meio - 468×60px (Entre Notícias)</option>
                            <option value="rodape" <?php echo ($edicao ? $banner_editar['posicao'] : ($_POST['posicao'] ?? '')) === 'rodape' ? 'selected' : ''; ?>>🔻 Rodapé - 728×90px (Footer)</option>
                            <option value="lateral" <?php echo ($edicao ? $banner_editar['posicao'] : ($_POST['posicao'] ?? '')) === 'lateral' ? 'selected' : ''; ?>>📐 Lateral - 160×600px (Lados Laterais)</option>
                        </select>
                        
                        <div class="posicao-info">
                            <strong>📋 Dimensões Fixas (Importante!):</strong>
                            <div class="dimensao-item">
                                <span>🔝 Topo:</span>
                                <span><strong>728 × 90 pixels</strong></span>
                            </div>
                            <div class="dimensao-item">
                                <span>📏 Sidebar:</span>
                                <span><strong>300 × 250 pixels</strong></span>
                            </div>
                            <div class="dimensao-item">
                                <span>📄 Meio:</span>
                                <span><strong>468 × 60 pixels</strong></span>
                            </div>
                            <div class="dimensao-item">
                                <span>🔻 Rodapé:</span>
                                <span><strong>728 × 90 pixels</strong></span>
                            </div>
                            <div class="dimensao-item">
                                <span>📐 Lateral:</span>
                                <span><strong>160 × 600 pixels</strong></span>
                            </div>
                            <div style="margin-top: 0.5rem; padding-top: 0.5rem; border-top: 1px solid var(--cinza);">
                                <span class="info-icon">⚠️</span>
                                <strong>Use imagens com estas dimensões exatas para melhor visualização!</strong>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label>🖼️ Imagem do Banner *</label>
                        <div class="upload-area" id="uploadArea">
                            <input type="file" id="imagem" name="imagem" 
                                   accept="image/*" style="display: none;">
                            <div style="font-size: 3rem;">📷</div>
                            <p><strong>Clique para <?php echo $edicao ? 'alterar' : 'selecionar'; ?> a imagem</strong></p>
                            <p style="color: var(--cinza-escuro); font-size: 0.9rem;">
                                Formatos: JPG, PNG, WEBP, GIF (Máx: 2MB)
                            </p>
                            
                            <?php if ($edicao && $banner_editar['imagem']): ?>
                                <div style="margin-top: 1rem;">
                                    <strong>Imagem atual:</strong><br>
                                    <img src="../uploads/anuncios/<?php echo $banner_editar['imagem']; ?>" 
                                         class="preview-imagem" id="previewImagem"
                                         alt="Banner atual" style="display: block;">
                                </div>
                            <?php else: ?>
                                <img id="previewImagem" class="preview-imagem" alt="Preview">
                            <?php endif; ?>
                        </div>
                        
                        <div class="dimensoes-info">
                            <span class="info-icon">💡</span>
                            <strong>Dica:</strong> Use imagens otimizadas para web para melhor carregamento.
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label for="status">📊 Status *</label>
                            <select id="status" name="status" class="form-control" required>
                                <option value="ativo" <?php echo ($edicao ? $banner_editar['status'] : ($_POST['status'] ?? 'ativo')) === 'ativo' ? 'selected' : ''; ?>>✅ Ativo</option>
                                <option value="inativo" <?php echo ($edicao ? $banner_editar['status'] : ($_POST['status'] ?? '')) === 'inativo' ? 'selected' : ''; ?>>⏸️ Inativo</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="data_inicio">📅 Data de Início</label>
                            <input type="date" id="data_inicio" name="data_inicio" class="form-control" 
                                   value="<?php echo $edicao ? ($banner_editar['data_inicio'] ?: '') : ($_POST['data_inicio'] ?? ''); ?>">
                            <small style="color: var(--cinza-escuro);">Data em que o banner começará a ser exibido</small>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label for="data_fim">📅 Data de Término</label>
                            <input type="date" id="data_fim" name="data_fim" class="form-control" 
                                   value="<?php echo $edicao ? ($banner_editar['data_fim'] ?: '') : ($_POST['data_fim'] ?? ''); ?>">
                            <small style="color: var(--cinza-escuro);">Data em que o banner deixará de ser exibido</small>
                        </div>
                    </div>

                    <div class="btn-group">
                        <?php if ($edicao): ?>
                            <button type="submit" class="btn btn-primary">
                                <span>💾</span>
                                Atualizar Banner
                            </button>
                        <?php else: ?>
                            <button type="submit" class="btn btn-primary">
                                <span>🖼️</span>
                                Cadastrar Banner
                            </button>
                        <?php endif; ?>
                        
                        <a href="anuncios.php" class="btn btn-secondary">
                            <span>↩️</span>
                            Voltar
                        </a>
                        
                        <?php if ($edicao): ?>
                            <a href="anuncios.php?excluir_banner=<?php echo $banner_editar['id']; ?>" 
                               class="btn btn-secondary" 
                               onclick="return confirm('Tem certeza que deseja excluir este banner?')"
                               style="background: var(--vermelho);">
                                <span>🗑️</span>
                                Excluir Banner
                            </a>
                        <?php endif; ?>
                    </div>
                </form>
            </div>
        </main>
    </div>

    <script>
        // Upload de imagem com preview
        document.getElementById('uploadArea').addEventListener('click', function() {
            document.getElementById('imagem').click();
        });
        
        document.getElementById('imagem').addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                // Verificar tamanho do arquivo (2MB máximo)
                if (file.size > 2 * 1024 * 1024) {
                    alert('⚠️ A imagem deve ter no máximo 2MB.');
                    this.value = '';
                    return;
                }
                
                const reader = new FileReader();
                reader.onload = function(e) {
                    const preview = document.getElementById('previewImagem');
                    preview.src = e.target.result;
                    preview.style.display = 'block';
                }
                reader.readAsDataURL(file);
            }
        });
        
        // Drag and drop
        const uploadArea = document.getElementById('uploadArea');
        
        ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
            uploadArea.addEventListener(eventName, preventDefaults, false);
        });
        
        function preventDefaults(e) {
            e.preventDefault();
            e.stopPropagation();
        }
        
        ['dragenter', 'dragover'].forEach(eventName => {
            uploadArea.addEventListener(eventName, highlight, false);
        });
        
        ['dragleave', 'drop'].forEach(eventName => {
            uploadArea.addEventListener(eventName, unhighlight, false);
        });
        
        function highlight() {
            uploadArea.classList.add('dragover');
        }
        
        function unhighlight() {
            uploadArea.classList.remove('dragover');
        }
        
        uploadArea.addEventListener('drop', handleDrop, false);
        
        function handleDrop(e) {
            const dt = e.dataTransfer;
            const files = dt.files;
            
            if (files.length > 0) {
                const file = files[0];
                
                // Verificar se é imagem
                if (!file.type.startsWith('image/')) {
                    alert('⚠️ Por favor, selecione apenas arquivos de imagem.');
                    return;
                }
                
                // Verificar tamanho
                if (file.size > 2 * 1024 * 1024) {
                    alert('⚠️ A imagem deve ter no máximo 2MB.');
                    return;
                }
                
                document.getElementById('imagem').files = files;
                
                // Disparar change event
                const event = new Event('change');
                document.getElementById('imagem').dispatchEvent(event);
            }
        }
        
        // Validação do formulário
        document.getElementById('formBanner').addEventListener('submit', function(e) {
            const anuncianteId = document.getElementById('anunciante_id').value;
            const posicao = document.getElementById('posicao').value;
            const status = document.getElementById('status').value;
            const imagem = document.getElementById('imagem').files[0];
            const dataInicio = document.getElementById('data_inicio').value;
            const dataFim = document.getElementById('data_fim').value;
            
            if (!anuncianteId || !posicao || !status) {
                e.preventDefault();
                alert('⚠️ Por favor, preencha todos os campos obrigatórios.');
                return false;
            }
            
            // Verificar se é novo banner e não tem imagem
            if (!<?php echo $edicao ? 'true' : 'false'; ?> && !imagem) {
                e.preventDefault();
                alert('⚠️ Por favor, selecione uma imagem para o banner.');
                return false;
            }
            
            // Validar datas
            if (dataInicio && dataFim) {
                const inicio = new Date(dataInicio);
                const fim = new Date(dataFim);
                
                if (fim < inicio) {
                    e.preventDefault();
                    alert('⚠️ A data de término não pode ser anterior à data de início.');
                    return false;
                }
            }
        });
        
        // Validação de datas em tempo real
        document.getElementById('data_inicio').addEventListener('change', function() {
            const dataFim = document.getElementById('data_fim');
            if (this.value && dataFim.value) {
                const inicio = new Date(this.value);
                const fim = new Date(dataFim.value);
                
                if (fim < inicio) {
                    alert('⚠️ A data de término não pode ser anterior à data de início.');
                    dataFim.value = '';
                }
            }
        });
        
        document.getElementById('data_fim').addEventListener('change', function() {
            const dataInicio = document.getElementById('data_inicio');
            if (this.value && dataInicio.value) {
                const inicio = new Date(dataInicio.value);
                const fim = new Date(this.value);
                
                if (fim < inicio) {
                    alert('⚠️ A data de término não pode ser anterior à data de início.');
                    this.value = '';
                }
            }
        });
    </script>
</body>
</html>