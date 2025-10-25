<?php
function gerarSlug($texto) {
    $slug = preg_replace('/[^a-z0-9]/', '-', strtolower($texto));
    $slug = preg_replace('/-+/', '-', $slug);
    return trim($slug, '-');
}

function formatarData($data) {
    return date('d/m/Y H:i', strtotime($data));
}

function limitarTexto($texto, $limite = 150) {
    if (strlen($texto) <= $limite) {
        return $texto;
    }
    return substr($texto, 0, $limite) . '...';
}

function estaLogado() {
    return isset($_SESSION['usuario_id']);
}

function redirecionar($url) {
    header("Location: $url");
    exit;
}

function calcularTempoLeitura($texto) {
    $palavras = str_word_count(strip_tags($texto));
    $minutos = ceil($palavras / 200); // 200 palavras por minuto
    return max(1, $minutos); // Mínimo 1 minuto
}

function obterNoticiasRecentes($limite = 5) {
    global $pdo;
    
    $stmt = $pdo->prepare("
        SELECT * FROM noticias 
        WHERE status = 'publicado' 
        ORDER BY created_at DESC 
        LIMIT ?
    ");
    $stmt->execute([$limite]);
    return $stmt->fetchAll();
}

function obterNoticiasDestaque($limite = 3) {
    global $pdo;
    
    $stmt = $pdo->prepare("
        SELECT * FROM noticias 
        WHERE destaque = 'sim' AND status = 'publicado' 
        ORDER BY created_at DESC 
        LIMIT ?
    ");
    $stmt->execute([$limite]);
    return $stmt->fetchAll();
}

function formatarTextoNoticia($texto) {
    // Converter quebras de linha em parágrafos
    $texto = nl2br(htmlspecialchars($texto));
    
    // Adicionar classes para estilização
    $texto = preg_replace('/<br\s*\/?>\s*<br\s*\/?>/', '</p><p>', $texto);
    $texto = '<p>' . $texto . '</p>';
    
    return $texto;
}

// FUNÇÕES DE BANNERS
function obterBannersAtivos($posicao = null) {
    global $pdo;
    
    $sql = "
        SELECT * FROM banners 
        WHERE status = 'ativo' 
        AND (data_inicio IS NULL OR data_inicio <= CURDATE())
        AND (data_fim IS NULL OR data_fim >= CURDATE())
    ";
    
    if ($posicao) {
        $sql .= " AND posicao = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$posicao]);
    } else {
        $stmt = $pdo->prepare($sql);
        $stmt->execute();
    }
    
    return $stmt->fetchAll();
}

function registrarCliqueBanner($banner_id) {
    global $pdo;
    
    $stmt = $pdo->prepare("UPDATE banners SET cliques = cliques + 1 WHERE id = ?");
    $stmt->execute([$banner_id]);
}

function obterEstatisticasBanners() {
    global $pdo;
    
    $stmt = $pdo->prepare("
        SELECT 
            COUNT(*) as total_banners,
            SUM(cliques) as total_cliques,
            COUNT(CASE WHEN status = 'ativo' THEN 1 END) as banners_ativos,
            COUNT(CASE WHEN status = 'inativo' THEN 1 END) as banners_inativos
        FROM banners
    ");
    $stmt->execute();
    return $stmt->fetch();
}

// FUNÇÕES DE ANUNCIANTES
function obterTotalAnunciantes() {
    global $pdo;
    
    $stmt = $pdo->prepare("SELECT COUNT(*) as total FROM anunciantes");
    $stmt->execute();
    $result = $stmt->fetch();
    return $result['total'];
}

function obterAnunciantePorId($id) {
    global $pdo;
    
    $stmt = $pdo->prepare("SELECT * FROM anunciantes WHERE id = ?");
    $stmt->execute([$id]);
    return $stmt->fetch();
}

// FUNÇÕES DE ESTATÍSTICAS
function obterEstatisticasGerais() {
    global $pdo;
    
    $stmt = $pdo->prepare("
        SELECT 
            (SELECT COUNT(*) FROM noticias) as total_noticias,
            (SELECT COUNT(*) FROM noticias WHERE status = 'publicado') as noticias_publicadas,
            (SELECT COUNT(*) FROM noticias WHERE destaque = 'sim') as noticias_destaque,
            (SELECT SUM(visualizacoes) FROM noticias) as total_visualizacoes,
            (SELECT COUNT(*) FROM anunciantes) as total_anunciantes,
            (SELECT COUNT(*) FROM banners) as total_banners
    ");
    $stmt->execute();
    return $stmt->fetch();
}

// FUNÇÃO PARA VALIDAÇÃO DE EMAIL
function validarEmail($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
}

// FUNÇÃO PARA SANITIZAR DADOS
function sanitizar($dados) {
    if (is_array($dados)) {
        return array_map('sanitizar', $dados);
    }
    return htmlspecialchars(trim($dados), ENT_QUOTES, 'UTF-8');
}

// FUNÇÃO PARA GERAR SENHA SEGURA
function gerarSenhaHash($senha) {
    return password_hash($senha, PASSWORD_DEFAULT);
}

// FUNÇÃO PARA VERIFICAR SENHA
function verificarSenha($senha, $hash) {
    return password_verify($senha, $hash);
}

// ============================================================================
// FUNÇÕES DE CONFIGURAÇÕES - CORRIGIDAS E COMPLETAS
// ============================================================================

function salvarConfiguracao($chave, $valor) {
    global $pdo;
    
    try {
        $stmt = $pdo->prepare("
            INSERT INTO configuracoes (chave, valor) 
            VALUES (?, ?) 
            ON DUPLICATE KEY UPDATE valor = ?, updated_at = CURRENT_TIMESTAMP
        ");
        
        return $stmt->execute([$chave, $valor, $valor]);
    } catch (Exception $e) {
        error_log("Erro ao salvar configuração: " . $e->getMessage());
        return false;
    }
}

function buscarConfiguracao($chave) {
    global $pdo;
    
    try {
        $stmt = $pdo->prepare("SELECT valor FROM configuracoes WHERE chave = ?");
        $stmt->execute([$chave]);
        $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
        
        return $resultado ? $resultado['valor'] : null;
    } catch (Exception $e) {
        error_log("Erro ao buscar configuração: " . $e->getMessage());
        return null;
    }
}

function buscarConfiguracoes() {
    global $pdo;
    
    try {
        $stmt = $pdo->prepare("SELECT chave, valor FROM configuracoes");
        $stmt->execute();
        $resultados = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        $configuracoes = [];
        foreach ($resultados as $config) {
            $configuracoes[$config['chave']] = $config['valor'];
        }
        
        return $configuracoes;
    } catch (Exception $e) {
        error_log("Erro ao buscar configurações: " . $e->getMessage());
        return [];
    }
}

function obterConfiguracao($chave, $padrao = '') {
    $valor = buscarConfiguracao($chave);
    return $valor !== null ? $valor : $padrao;
}

function obterHorarioAtual() {
    $configuracoes = buscarConfiguracoes();
    $fuso_horario = $configuracoes['fuso_horario'] ?? 'America/Sao_Paulo';
    
    try {
        $timezone = new DateTimeZone($fuso_horario);
        $agora = new DateTime('now', $timezone);
        return $agora->format('H:i');
    } catch (Exception $e) {
        error_log("Erro no fuso horário: " . $e->getMessage());
        return date('H:i');
    }
}

function obterCidade() {
    return obterConfiguracao('cidade', 'São Paulo');
}

// FUNÇÃO CORRIGIDA: obterLogoSite
function obterLogoSite() {
    $logo = obterConfiguracao('site_logo');
    if ($logo && file_exists('uploads/' . $logo)) {
        return 'uploads/' . $logo;
    }
    return null;
}

// FUNÇÃO CORRIGIDA: obterLogoRodape
function obterLogoRodape() {
    $logo = obterConfiguracao('footer_logo');
    if ($logo && file_exists('uploads/' . $logo)) {
        return 'uploads/' . $logo;
    }
    return null;
}

// FUNÇÃO CORRIGIDA: obterPosicaoLogo
function obterPosicaoLogo() {
    $posicao = obterConfiguracao('logo_posicao');
    return in_array($posicao, ['left', 'center', 'right']) ? $posicao : 'left';
}

function obterTextoCabecalho() {
    return obterConfiguracao('texto_cabecalho', 'Jornalismo Independente');
}

function obterTextoRodape() {
    return obterConfiguracao('texto_rodape', '');
}

function exibirTextoCabecalho() {
    return obterConfiguracao('exibir_texto_cabecalho', '1') === '1';
}

function exibirHorario() {
    return obterConfiguracao('exibir_horario', '1') === '1';
}

// FUNÇÃO PARA VERIFICAR SE A TABELA DE CONFIGURAÇÕES EXISTE
function tabelaConfiguracoesExiste() {
    global $pdo;
    
    try {
        $stmt = $pdo->prepare("SHOW TABLES LIKE 'configuracoes'");
        $stmt->execute();
        return $stmt->rowCount() > 0;
    } catch (Exception $e) {
        error_log("Erro ao verificar tabela: " . $e->getMessage());
        return false;
    }
}

// FUNÇÃO PARA CRIAR TABELA DE CONFIGURAÇÕES SE NÃO EXISTIR
function criarTabelaConfiguracoes() {
    global $pdo;
    
    if (!tabelaConfiguracoesExiste()) {
        try {
            $sql = "
                CREATE TABLE configuracoes (
                    id INT PRIMARY KEY AUTO_INCREMENT,
                    chave VARCHAR(100) UNIQUE NOT NULL,
                    valor TEXT,
                    tipo VARCHAR(50),
                    descricao TEXT,
                    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
                )
            ";
            $pdo->exec($sql);
            error_log("Tabela configuracoes criada com sucesso");
            return true;
        } catch (Exception $e) {
            error_log("Erro ao criar tabela configuracoes: " . $e->getMessage());
            return false;
        }
    }
    return true;
}

// FUNÇÃO PARA INICIALIZAR CONFIGURAÇÕES PADRÃO
function inicializarConfiguracoesPadrao() {
    $configuracoes_padrao = [
        'texto_cabecalho' => 'Jornalismo Independente',
        'exibir_texto_cabecalho' => '1',
        'cidade' => 'São Paulo',
        'fuso_horario' => 'America/Sao_Paulo',
        'exibir_horario' => '1',
        'logo_posicao' => 'left'
    ];
    
    foreach ($configuracoes_padrao as $chave => $valor) {
        if (buscarConfiguracao($chave) === null) {
            salvarConfiguracao($chave, $valor);
        }
    }
}

// INICIALIZAR CONFIGURAÇÕES AO CARREGAR O ARQUIVO
if (function_exists('tabelaConfiguracoesExiste')) {
    criarTabelaConfiguracoes();
    inicializarConfiguracoesPadrao();
}
?>