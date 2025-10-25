<?php
require_once '../includes/auth.php';
require_once '../includes/functions.php';
verificarAuth();

$edicao = false;
$anunciante_editar = null;

// Verificar se é edição
if (isset($_GET['editar'])) {
    $edicao = true;
    $anunciante_id = $_GET['editar'];
    
    $stmt = $pdo->prepare("SELECT * FROM anunciantes WHERE id = ?");
    $stmt->execute([$anunciante_id]);
    $anunciante_editar = $stmt->fetch();
    
    if (!$anunciante_editar) {
        header("Location: anuncios.php");
        exit;
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome = $_POST['nome'];
    $email = $_POST['email'];
    $telefone = $_POST['telefone'];
    
    if ($edicao) {
        // EDIÇÃO - Atualizar anunciante
        $stmt = $pdo->prepare("
            UPDATE anunciantes 
            SET nome = ?, email = ?, telefone = ? 
            WHERE id = ?
        ");
        
        if ($stmt->execute([$nome, $email, $telefone, $anunciante_editar['id']])) {
            $sucesso = "Anunciante atualizado com sucesso!";
            // Atualizar dados do anunciante em edição
            $anunciante_editar = array_merge($anunciante_editar, [
                'nome' => $nome,
                'email' => $email,
                'telefone' => $telefone
            ]);
        } else {
            $erro = "Erro ao atualizar anunciante. Tente novamente.";
        }
    } else {
        // CADASTRO - Novo anunciante
        $stmt = $pdo->prepare("
            INSERT INTO anunciantes (nome, email, telefone) 
            VALUES (?, ?, ?)
        ");
        
        if ($stmt->execute([$nome, $email, $telefone])) {
            $sucesso = "Anunciante cadastrado com sucesso!";
            // Limpar formulário
            $_POST = array();
        } else {
            $erro = "Erro ao cadastrar anunciante. Tente novamente.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $edicao ? 'Editar Anunciante' : 'Novo Anunciante'; ?> - Black Angel</title>
    <link rel="stylesheet" href="../css/admin.css">
    <style>
        .editor-container {
            background: var(--branco);
            padding: 2rem;
            border-radius: 12px;
            box-shadow: var(--sombra);
            margin-bottom: 2rem;
            max-width: 600px;
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
        
        @media (max-width: 768px) {
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
                    <h1><?php echo $edicao ? '👥 Editar Anunciante' : '👥 Novo Anunciante'; ?></h1>
                    <p><?php echo $edicao ? 'Edite os dados do anunciante' : 'Cadastre um novo anunciante no sistema'; ?></p>
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

            <div class="editor-container">
                <form method="POST" id="formAnunciante">
                    <div class="form-group">
                        <label for="nome">🏢 Nome do Anunciante *</label>
                        <input type="text" id="nome" name="nome" class="form-control" 
                               value="<?php echo $edicao ? $anunciante_editar['nome'] : ($_POST['nome'] ?? ''); ?>" required 
                               placeholder="Digite o nome da empresa ou pessoa">
                    </div>

                    <div class="form-group">
                        <label for="email">📧 E-mail</label>
                        <input type="email" id="email" name="email" class="form-control" 
                               value="<?php echo $edicao ? $anunciante_editar['email'] : ($_POST['email'] ?? ''); ?>" 
                               placeholder="email@empresa.com">
                    </div>

                    <div class="form-group">
                        <label for="telefone">📞 Telefone</label>
                        <input type="text" id="telefone" name="telefone" class="form-control" 
                               value="<?php echo $edicao ? $anunciante_editar['telefone'] : ($_POST['telefone'] ?? ''); ?>" 
                               placeholder="(11) 99999-9999">
                    </div>

                    <div class="btn-group">
                        <?php if ($edicao): ?>
                            <button type="submit" class="btn btn-primary">
                                <span>💾</span>
                                Atualizar Anunciante
                            </button>
                        <?php else: ?>
                            <button type="submit" class="btn btn-primary">
                                <span>👥</span>
                                Cadastrar Anunciante
                            </button>
                        <?php endif; ?>
                        
                        <a href="anuncios.php" class="btn btn-secondary">
                            <span>↩️</span>
                            Voltar
                        </a>
                    </div>
                </form>
            </div>
        </main>
    </div>

    <script>
        // Validação do formulário
        document.getElementById('formAnunciante').addEventListener('submit', function(e) {
            const nome = document.getElementById('nome').value.trim();
            
            if (!nome) {
                e.preventDefault();
                alert('⚠️ Por favor, preencha o nome do anunciante.');
                return false;
            }
        });
        
        // Máscara para telefone
        document.getElementById('telefone').addEventListener('input', function(e) {
            let value = e.target.value.replace(/\D/g, '');
            if (value.length > 11) value = value.substring(0, 11);
            
            if (value.length > 10) {
                value = value.replace(/^(\d{2})(\d{5})(\d{4})/, '($1) $2-$3');
            } else if (value.length > 6) {
                value = value.replace(/^(\d{2})(\d{4})(\d{0,4})/, '($1) $2-$3');
            } else if (value.length > 2) {
                value = value.replace(/^(\d{2})(\d{0,5})/, '($1) $2');
            } else if (value.length > 0) {
                value = value.replace(/^(\d*)/, '($1');
            }
            
            e.target.value = value;
        });
    </script>
</body>
</html>