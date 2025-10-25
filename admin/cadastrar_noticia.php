<?php
require_once '../includes/auth.php';
verificarAuth();

$edicao = false;
$noticia_editar = null;

// Verificar se é edição
if (isset($_GET['editar'])) {
    $edicao = true;
    $noticia_id = $_GET['editar'];
    
    $stmt = $pdo->prepare("SELECT * FROM noticias WHERE id = ?");
    $stmt->execute([$noticia_id]);
    $noticia_editar = $stmt->fetch();
    
    if (!$noticia_editar) {
        header("Location: gerenciar_noticias.php");
        exit;
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $titulo = $_POST['titulo'];
    $resumo = $_POST['resumo'];
    $conteudo = $_POST['conteudo'];
    $categoria = $_POST['categoria'];
    $destaque = isset($_POST['destaque']) ? 'sim' : 'nao';
    $status = $_POST['status'];
    
    if ($edicao) {
        // EDIÇÃO - Atualizar notícia existente
        if (isset($_FILES['imagem_destaque']) && $_FILES['imagem_destaque']['error'] === 0) {
            // Upload da nova imagem
            $extensao = strtolower(pathinfo($_FILES['imagem_destaque']['name'], PATHINFO_EXTENSION));
            $extensoes_permitidas = ['jpg', 'jpeg', 'png', 'webp'];
            
            if (in_array($extensao, $extensoes_permitidas)) {
                $nome_arquivo = uniqid() . '.' . $extensao;
                $caminho_upload = '../uploads/noticias/' . $nome_arquivo;
                
                if (move_uploaded_file($_FILES['imagem_destaque']['tmp_name'], $caminho_upload)) {
                    // Excluir imagem antiga se existir
                    if ($noticia_editar['imagem_destaque']) {
                        $caminho_antigo = '../uploads/noticias/' . $noticia_editar['imagem_destaque'];
                        if (file_exists($caminho_antigo)) {
                            unlink($caminho_antigo);
                        }
                    }
                    $imagem_destaque = $nome_arquivo;
                } else {
                    $imagem_destaque = $noticia_editar['imagem_destaque'];
                }
            } else {
                $imagem_destaque = $noticia_editar['imagem_destaque'];
            }
        } else {
            $imagem_destaque = $noticia_editar['imagem_destaque'];
        }
        
        // Atualizar no banco
        $stmt = $pdo->prepare("
            UPDATE noticias 
            SET titulo = ?, resumo = ?, conteudo = ?, imagem_destaque = ?, 
                categoria = ?, destaque = ?, status = ?, updated_at = NOW() 
            WHERE id = ?
        ");
        
        if ($stmt->execute([$titulo, $resumo, $conteudo, $imagem_destaque, $categoria, $destaque, $status, $noticia_editar['id']])) {
            $sucesso = "Notícia atualizada com sucesso!";
            // Atualizar dados da notícia em edição
            $noticia_editar = array_merge($noticia_editar, [
                'titulo' => $titulo,
                'resumo' => $resumo,
                'conteudo' => $conteudo,
                'categoria' => $categoria,
                'destaque' => $destaque,
                'status' => $status,
                'imagem_destaque' => $imagem_destaque
            ]);
        } else {
            $erro = "Erro ao atualizar notícia. Tente novamente.";
        }
    } else {
        // CADASTRO - Nova notícia
        $imagem_destaque = null;
        if (isset($_FILES['imagem_destaque']) && $_FILES['imagem_destaque']['error'] === 0) {
            $extensao = strtolower(pathinfo($_FILES['imagem_destaque']['name'], PATHINFO_EXTENSION));
            $extensoes_permitidas = ['jpg', 'jpeg', 'png', 'webp'];
            
            if (in_array($extensao, $extensoes_permitidas)) {
                $nome_arquivo = uniqid() . '.' . $extensao;
                $caminho_upload = '../uploads/noticias/' . $nome_arquivo;
                
                if (move_uploaded_file($_FILES['imagem_destaque']['tmp_name'], $caminho_upload)) {
                    $imagem_destaque = $nome_arquivo;
                }
            }
        }
        
        // Inserir no banco
        $stmt = $pdo->prepare("
            INSERT INTO noticias (titulo, resumo, conteudo, imagem_destaque, autor_id, categoria, destaque, status) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?)
        ");
        
        if ($stmt->execute([$titulo, $resumo, $conteudo, $imagem_destaque, $_SESSION['usuario_id'], $categoria, $destaque, $status])) {
            $sucesso = "Notícia cadastrada com sucesso!";
            // Limpar formulário
            $_POST = array();
            $edicao = false;
        } else {
            $erro = "Erro ao cadastrar notícia. Tente novamente.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $edicao ? 'Editar Notícia' : 'Nova Notícia'; ?> - Black Angel</title>
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
        
        textarea.form-control {
            min-height: 120px;
            resize: vertical;
        }
        
        #conteudo {
            min-height: 300px;
        }
        
        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1rem;
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
        
        .btn-group {
            display: flex;
            gap: 1rem;
            margin-top: 2rem;
        }
        
        .btn-salvar {
            background: var(--verde);
            color: var(--branco);
        }
        
        .btn-rascunho {
            background: var(--cinza-escuro);
            color: var(--branco);
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
        }
        
        .upload-area:hover {
            border-color: var(--vermelho);
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
            display: <?php echo ($edicao && $noticia_editar['imagem_destaque']) ? 'block' : 'none'; ?>;
        }
        
        .imagem-atual {
            margin-top: 1rem;
            font-size: 0.9rem;
            color: var(--cinza-escuro);
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
                <a href="cadastrar_noticia.php" class="nav-item active">
                    <span>📝</span>
                    <span><?php echo $edicao ? 'Editar Notícia' : 'Nova Notícia'; ?></span>
                </a>
                <a href="gerenciar_noticias.php" class="nav-item">
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
                    <h1><?php echo $edicao ? '✏️ Editar Notícia' : '📝 Nova Notícia'; ?></h1>
                    <p><?php echo $edicao ? 'Edite a notícia existente' : 'Crie uma nova matéria para o Black Angel'; ?></p>
                </div>
                
                <?php if ($edicao): ?>
                    <a href="../noticia.php?id=<?php echo $noticia_editar['id']; ?>" target="_blank" class="btn btn-primary">
                        <span>👁️</span>
                        Ver no Site
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
                <form method="POST" enctype="multipart/form-data" id="formNoticia">
                    <div class="form-group">
                        <label for="titulo">📌 Título da Notícia *</label>
                        <input type="text" id="titulo" name="titulo" class="form-control" 
                               value="<?php echo $edicao ? $noticia_editar['titulo'] : ($_POST['titulo'] ?? ''); ?>" required 
                               placeholder="Digite o título impactante da notícia">
                    </div>

                    <div class="form-group">
                        <label for="resumo">📋 Resumo *</label>
                        <textarea id="resumo" name="resumo" class="form-control" required 
                                  placeholder="Digite um resumo curto e atraente para a notícia"><?php echo $edicao ? $noticia_editar['resumo'] : ($_POST['resumo'] ?? ''); ?></textarea>
                        <small style="color: var(--cinza-escuro);">Este texto aparecerá na lista de notícias e no carrossel.</small>
                    </div>

                    <div class="form-group">
                        <label for="conteudo">📖 Conteúdo Completo *</label>
                        <textarea id="conteudo" name="conteudo" class="form-control" required 
                                  placeholder="Digite o conteúdo completo da notícia"><?php echo $edicao ? $noticia_editar['conteudo'] : ($_POST['conteudo'] ?? ''); ?></textarea>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label for="categoria">📂 Categoria *</label>
                            <select id="categoria" name="categoria" class="form-control" required>
                                <option value="">Selecione uma categoria</option>
                                <option value="Política" <?php echo ($edicao ? $noticia_editar['categoria'] : ($_POST['categoria'] ?? '')) === 'Política' ? 'selected' : ''; ?>>🏛️ Política</option>
                                <option value="Economia" <?php echo ($edicao ? $noticia_editar['categoria'] : ($_POST['categoria'] ?? '')) === 'Economia' ? 'selected' : ''; ?>>💼 Economia</option>
                                <option value="Cultura" <?php echo ($edicao ? $noticia_editar['categoria'] : ($_POST['categoria'] ?? '')) === 'Cultura' ? 'selected' : ''; ?>>🎭 Cultura</option>
                                <option value="Esportes" <?php echo ($edicao ? $noticia_editar['categoria'] : ($_POST['categoria'] ?? '')) === 'Esportes' ? 'selected' : ''; ?>>⚽ Esportes</option>
                                <option value="Local" <?php echo ($edicao ? $noticia_editar['categoria'] : ($_POST['categoria'] ?? '')) === 'Local' ? 'selected' : ''; ?>>📍 Local</option>
                                <option value="Geral" <?php echo ($edicao ? $noticia_editar['categoria'] : ($_POST['categoria'] ?? '')) === 'Geral' ? 'selected' : ''; ?>>📰 Geral</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="status">📊 Status *</label>
                            <select id="status" name="status" class="form-control" required>
                                <option value="rascunho" <?php echo ($edicao ? $noticia_editar['status'] : ($_POST['status'] ?? '')) === 'rascunho' ? 'selected' : ''; ?>>📝 Rascunho</option>
                                <option value="publicado" <?php echo ($edicao ? $noticia_editar['status'] : ($_POST['status'] ?? '')) === 'publicado' ? 'selected' : ''; ?>>🚀 Publicado</option>
                            </select>
                        </div>
                    </div>

                    <div class="form-group">
                        <label>🖼️ Imagem de Destaque</label>
                        <div class="upload-area" id="uploadArea">
                            <input type="file" id="imagem_destaque" name="imagem_destaque" 
                                   accept="image/*" style="display: none;">
                            <div style="font-size: 3rem;">📷</div>
                            <p><strong>Clique para <?php echo $edicao ? 'alterar' : 'selecionar'; ?> a imagem</strong></p>
                            <p style="color: var(--cinza-escuro); font-size: 0.9rem;">
                                Formatos: JPG, PNG, WEBP (Máx: 5MB)
                            </p>
                            
                            <?php if ($edicao && $noticia_editar['imagem_destaque']): ?>
                                <div class="imagem-atual">
                                    <strong>Imagem atual:</strong><br>
                                    <img src="../uploads/noticias/<?php echo $noticia_editar['imagem_destaque']; ?>" 
                                         class="preview-imagem" id="previewImagem"
                                         alt="Imagem atual" style="display: block;">
                                </div>
                            <?php else: ?>
                                <img id="previewImagem" class="preview-imagem" alt="Preview">
                            <?php endif; ?>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <div class="checkbox-group">
                                <input type="checkbox" id="destaque" name="destaque" 
                                       <?php echo ($edicao ? $noticia_editar['destaque'] : (isset($_POST['destaque']) ? 'sim' : 'nao')) === 'sim' ? 'checked' : ''; ?>>
                                <label for="destaque">⭐ Colocar em Destaque</label>
                            </div>
                            <small style="color: var(--cinza-escuro);">
                                Notícias em destaque aparecem no carrossel principal
                            </small>
                        </div>
                    </div>

                    <div class="btn-group">
                        <?php if ($edicao): ?>
                            <button type="submit" class="btn btn-primary btn-salvar">
                                <span>💾</span>
                                Atualizar Notícia
                            </button>
                        <?php else: ?>
                            <button type="submit" name="action" value="publicar" class="btn btn-primary btn-salvar">
                                <span>🚀</span>
                                Publicar Notícia
                            </button>
                            
                            <button type="submit" name="action" value="rascunho" class="btn btn-secondary btn-rascunho">
                                <span>💾</span>
                                Salvar como Rascunho
                            </button>
                        <?php endif; ?>
                        
                        <a href="gerenciar_noticias.php" class="btn btn-secondary">
                            <span>↩️</span>
                            Voltar
                        </a>
                    </div>
                </form>
            </div>
        </main>
    </div>

    <script>
        // Upload de imagem com preview
        document.getElementById('uploadArea').addEventListener('click', function() {
            document.getElementById('imagem_destaque').click();
        });
        
        document.getElementById('imagem_destaque').addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) {
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
            document.getElementById('imagem_destaque').files = files;
            
            // Disparar change event
            const event = new Event('change');
            document.getElementById('imagem_destaque').dispatchEvent(event);
        }
        
        // Validação do formulário
        document.getElementById('formNoticia').addEventListener('submit', function(e) {
            const titulo = document.getElementById('titulo').value.trim();
            const resumo = document.getElementById('resumo').value.trim();
            const conteudo = document.getElementById('conteudo').value.trim();
            const categoria = document.getElementById('categoria').value;
            
            if (!titulo || !resumo || !conteudo || !categoria) {
                e.preventDefault();
                alert('⚠️ Por favor, preencha todos os campos obrigatórios.');
                return false;
            }
            
            if (resumo.length > 200) {
                e.preventDefault();
                alert('⚠️ O resumo deve ter no máximo 200 caracteres.');
                return false;
            }
        });
    </script>
</body>
</html>