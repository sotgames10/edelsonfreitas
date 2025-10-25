<?php
require_once 'config.php';
require_once 'functions.php';

function exibirBanner($posicao) {
    $banners = obterBannersAtivos($posicao);
    
    if (empty($banners)) {
        // Banner padrão caso não haja banners ativos
        return '
        <div class="banner-placeholder ' . $posicao . '">
            <div style="background: var(--cinza-claro); padding: 1.5rem; text-align: center; border-radius: 8px; border: 2px dashed var(--cinza);">
                <h4 style="color: var(--vermelho); margin-bottom: 0.5rem; font-size: 1rem;">⭐ ESPAÇO PUBLICITÁRIO</h4>
                <p style="color: var(--cinza-escuro); font-size: 0.8rem; margin-bottom: 1rem;">
                    Seja um anunciante do Black Angel
                </p>
                <a href="contato.php" style="background: var(--vermelho); color: white; padding: 0.5rem 1rem; border-radius: 4px; text-decoration: none; font-size: 0.8rem; display: inline-block;">
                    📞 Fale Conosco
                </a>
            </div>
        </div>';
    }
    
    $html = '';
    foreach ($banners as $banner) {
        $html .= '
        <div class="banner-container ' . $posicao . '">
            <a href="clique_banner.php?id=' . $banner['id'] . '" target="_blank" class="banner-link">
                <img src="uploads/anuncios/' . $banner['imagem'] . '" 
                     alt="' . ($banner['titulo'] ?: 'Publicidade') . '" 
                     class="banner-image" 
                     loading="lazy">
            </a>
        </div>';
    }
    
    return $html;
}

// CSS para os banners - CORRIGIDO
function bannerCSS() {
    return '
    <style>
        .banner-container {
            margin-bottom: 1.5rem;
            text-align: center;
            overflow: hidden;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .banner-link {
            display: block;
            transition: opacity 0.3s;
            text-decoration: none;
        }
        
        .banner-link:hover {
            opacity: 0.9;
        }
        
        .banner-image {
            display: block;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            background: var(--cinza-claro);
        }
        
        /* DIMENSÕES FIXAS POR POSIÇÃO - CORRIGIDAS E CENTRALIZADAS */
        .banner-container.topo {
            width: 100%;
            height: 90px;
            margin: 0 auto 1.5rem;
            background: var(--branco);
            padding: 10px 0;
        }
        
        .banner-container.topo .banner-image {
            width: 728px;
            height: 90px;
            object-fit: cover;
        }
        
        .banner-container.sidebar {
            width: 100%;
            height: 250px;
            margin: 0 auto 1.5rem;
        }
        
        .banner-container.sidebar .banner-image {
            width: 300px;
            height: 250px;
            object-fit: cover;
        }
        
        .banner-container.meio {
            width: 100%;
            height: 60px;
            margin: 2rem auto;
            background: var(--cinza-claro);
            padding: 20px 0;
        }
        
        .banner-container.meio .banner-image {
            width: 468px;
            height: 60px;
            object-fit: cover;
        }
        
        .banner-container.rodape {
            width: 100%;
            height: 90px;
            margin: 2rem auto;
            background: var(--cinza-claro);
            padding: 20px 0;
        }
        
        .banner-container.rodape .banner-image {
            width: 728px;
            height: 90px;
            object-fit: cover;
        }
        
        .banner-container.lateral {
            width: 160px;
            height: 600px;
            position: fixed;
            top: 50%;
            transform: translateY(-50%);
        }
        
        .banner-container.lateral.esquerda {
            left: 10px;
        }
        
        .banner-container.lateral.direita {
            right: 10px;
        }
        
        .banner-container.lateral .banner-image {
            width: 160px;
            height: 600px;
            object-fit: cover;
        }
        
        /* Placeholder styles */
        .banner-placeholder {
            margin-bottom: 1.5rem;
            text-align: center;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .banner-placeholder.sidebar {
            width: 100%;
            height: 250px;
        }
        
        .banner-placeholder.topo {
            width: 100%;
            height: 90px;
            background: var(--branco);
            padding: 10px 0;
        }
        
        .banner-placeholder.meio {
            width: 100%;
            height: 60px;
            background: var(--cinza-claro);
            padding: 20px 0;
        }
        
        .banner-placeholder.rodape {
            width: 100%;
            height: 90px;
            background: var(--cinza-claro);
            padding: 20px 0;
        }

        /* RESPONSIVO - CORRIGIDO */
        @media (max-width: 1200px) {
            .banner-container.lateral {
                display: none;
            }
        }
        
        @media (max-width: 768px) {
            .banner-container.topo,
            .banner-placeholder.topo {
                height: 70px;
            }
            
            .banner-container.topo .banner-image,
            .banner-placeholder.topo {
                width: 100%;
                max-width: 728px;
                height: 70px;
            }
            
            .banner-container.sidebar,
            .banner-placeholder.sidebar {
                height: 200px;
            }
            
            .banner-container.sidebar .banner-image,
            .banner-placeholder.sidebar {
                width: 100%;
                max-width: 300px;
                height: 200px;
            }
            
            .banner-container.meio,
            .banner-placeholder.meio {
                height: 50px;
            }
            
            .banner-container.meio .banner-image,
            .banner-placeholder.meio {
                width: 100%;
                max-width: 468px;
                height: 50px;
            }
            
            .banner-container.rodape,
            .banner-placeholder.rodape {
                height: 70px;
            }
            
            .banner-container.rodape .banner-image,
            .banner-placeholder.rodape {
                width: 100%;
                max-width: 728px;
                height: 70px;
            }
        }
        
        @media (max-width: 480px) {
            .banner-container.sidebar,
            .banner-placeholder.sidebar {
                height: 150px;
            }
            
            .banner-container.sidebar .banner-image,
            .banner-placeholder.sidebar {
                height: 150px;
            }
        }
    </style>';
}
?>