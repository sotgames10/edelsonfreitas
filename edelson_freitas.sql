-- phpMyAdmin SQL Dump
-- version 5.2.2
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Tempo de geração: 25/10/2025 às 10:52
-- Versão do servidor: 5.7.34
-- Versão do PHP: 8.2.6

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Banco de dados: `edelson_freitas`
--

-- --------------------------------------------------------

--
-- Estrutura para tabela `anunciantes`
--

CREATE TABLE `anunciantes` (
  `id` int(11) NOT NULL,
  `nome` varchar(100) NOT NULL,
  `email` varchar(100) DEFAULT NULL,
  `telefone` varchar(20) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Despejando dados para a tabela `anunciantes`
--

INSERT INTO `anunciantes` (`id`, `nome`, `email`, `telefone`, `created_at`) VALUES
(1, 'Compre Mais', 'contato@compremais.com', '(79) 99900-5544', '2025-10-22 02:36:06'),
(2, 'Casa Dias', 'contato@casadias.com', '(79) 98844-5566', '2025-10-22 03:12:36'),
(3, 'Uauabr', 'contato@uauabr.com.br', '(79) 98888-7766', '2025-10-22 04:08:19'),
(4, 'Edelson ', 'contato@edelsonfreitas.com', '(79) 98835-2897', '2025-10-22 05:27:55');

-- --------------------------------------------------------

--
-- Estrutura para tabela `banners`
--

CREATE TABLE `banners` (
  `id` int(11) NOT NULL,
  `anunciante_id` int(11) DEFAULT NULL,
  `titulo` varchar(100) DEFAULT NULL,
  `imagem` varchar(255) DEFAULT NULL,
  `link` varchar(255) DEFAULT NULL,
  `posicao` varchar(50) DEFAULT NULL,
  `status` enum('ativo','inativo') DEFAULT 'ativo',
  `data_inicio` date DEFAULT NULL,
  `data_fim` date DEFAULT NULL,
  `cliques` int(11) DEFAULT '0',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Despejando dados para a tabela `banners`
--

INSERT INTO `banners` (`id`, `anunciante_id`, `titulo`, `imagem`, `link`, `posicao`, `status`, `data_inicio`, `data_fim`, `cliques`, `created_at`) VALUES
(3, 2, 'Casa Dias', '68f8575b9ef47.jpg', '', 'sidebar', 'ativo', '2025-10-22', '2025-10-23', 2, '2025-10-22 04:02:35'),
(4, 1, 'Compre mais', '68f857a05398f.jpg', '', 'sidebar', 'ativo', '2025-10-22', '2025-10-23', 2, '2025-10-22 04:03:44'),
(5, 1, 'Compre mais na notícia ', '68f85859ee567.jpg', '', 'meio', 'ativo', '2025-10-22', '2025-10-23', 1, '2025-10-22 04:06:50'),
(8, 4, '', '68fb01eaf0532.png', '', 'rodape', 'ativo', NULL, NULL, 0, '2025-10-23 17:13:35'),
(7, 4, 'Anuncie', '68fb01fa7ad49.png', '', 'topo', 'ativo', '2025-10-22', '2026-05-30', 0, '2025-10-22 05:28:26'),
(9, 1, '', '68fb02dec423a.png', '', 'sidebar', 'ativo', '2025-10-22', '2025-10-24', 0, '2025-10-24 03:52:03'),
(10, 4, '', '68faffdbdab55.png', '', 'sidebar', 'ativo', NULL, NULL, 0, '2025-10-24 04:26:03');

-- --------------------------------------------------------

--
-- Estrutura para tabela `configuracoes`
--

CREATE TABLE `configuracoes` (
  `id` int(11) NOT NULL,
  `chave` varchar(100) NOT NULL,
  `valor` text,
  `tipo` varchar(50) DEFAULT NULL,
  `descricao` text,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Despejando dados para a tabela `configuracoes`
--

INSERT INTO `configuracoes` (`id`, `chave`, `valor`, `tipo`, `descricao`, `created_at`, `updated_at`) VALUES
(1, 'logo_posicao', 'left', NULL, NULL, '2025-10-23 00:55:08', '2025-10-24 20:00:23'),
(2, 'texto_cabecalho', '', NULL, NULL, '2025-10-23 00:55:08', '2025-10-24 20:00:23'),
(3, 'cidade', 'Simão Dias ', NULL, NULL, '2025-10-23 00:55:08', '2025-10-24 20:00:23'),
(4, 'fuso_horario', 'America/Sao_Paulo', NULL, NULL, '2025-10-23 00:55:08', '2025-10-24 20:00:23'),
(5, 'exibir_horario', '1', NULL, NULL, '2025-10-23 00:55:08', '2025-10-24 20:00:23'),
(6, 'texto_rodape', '', NULL, NULL, '2025-10-23 00:55:08', '2025-10-24 18:01:41'),
(7, 'site_logo', 'site_logo_1761277073.png', NULL, NULL, '2025-10-23 15:02:36', '2025-10-24 03:37:53'),
(8, 'exibir_texto_cabecalho', '1', NULL, NULL, '2025-10-23 15:13:48', '2025-10-24 20:00:23'),
(9, 'footer_logo', 'footer_logo_1761232794.png', NULL, NULL, '2025-10-23 15:19:54', '2025-10-23 15:19:54'),
(10, 'categoria_1', 'Local', NULL, NULL, '2025-10-24 15:58:23', '2025-10-24 20:00:23'),
(11, 'categoria_2', 'Política ', NULL, NULL, '2025-10-24 15:58:23', '2025-10-24 20:00:23'),
(12, 'categoria_3', 'Esportes', NULL, NULL, '2025-10-24 15:58:23', '2025-10-24 20:00:23'),
(13, 'remover_footer_logo', '1', NULL, NULL, '2025-10-24 17:29:35', '2025-10-24 17:29:35');

-- --------------------------------------------------------

--
-- Estrutura para tabela `noticias`
--

CREATE TABLE `noticias` (
  `id` int(11) NOT NULL,
  `titulo` varchar(255) NOT NULL,
  `resumo` text NOT NULL,
  `conteudo` longtext NOT NULL,
  `imagem_destaque` varchar(255) DEFAULT NULL,
  `autor_id` int(11) DEFAULT NULL,
  `categoria` varchar(50) DEFAULT NULL,
  `destaque` enum('sim','nao') DEFAULT 'nao',
  `status` enum('publicado','rascunho') DEFAULT 'rascunho',
  `visualizacoes` int(11) DEFAULT '0',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Despejando dados para a tabela `noticias`
--

INSERT INTO `noticias` (`id`, `titulo`, `resumo`, `conteudo`, `imagem_destaque`, `autor_id`, `categoria`, `destaque`, `status`, `visualizacoes`, `created_at`, `updated_at`) VALUES
(1, 'IML: DEZESSEIS CORPOS RECOLHIDOS NAS ÚLTIMAS 48 HORAS', 'Relatório referente ao período de 19.06.2025 à 20.06.2025\r\n\r\n+ Corpo sem identificação, sexo masculino, recolhido no Hospital de Urgências João Alves Filho (HUSE)...', 'Relatório referente ao período de 19.06.2025 à 20.06.2025\r\n\r\n+ Corpo sem identificação, sexo masculino, recolhido no Hospital de Urgências João Alves Filho (HUSE), em Aracaju, morte por queda da própria altura;\r\n\r\n+ Nailza Francisca de Almeida, 50 anos, corpo recolhido no Conjunto Serapião Antônio de Góis, município de Itabaiana, causa morte a esclarecer;\r\n\r\n+ Corpo sem identificação, sexo não identificado, morte violenta por arma de fogo, na Rua Beira Rio, Porto Dantas, em Aracaju;\r\n\r\n+ André Souza, 42 anos, morte por acidente de trânsito, na Rua Rozimary Vieira de Jesus, Piabeta, município de Nossa Senhora do Socorro;\r\n\r\n+ Cleriston de Jesus Vieira, 37 anos, morte por acidente de trânsito, na Avenida Euclides Figueiredo, Lamarão, Nº 424, em Aracaju;\r\n\r\n+ Erick Kaique Medrade dos Santos, 18 anos, morte por acidente de trânsito, na Rodovia SE-230, município de Canindé de São Francisco;\r\n\r\n+ Antônio Guimarães, 62 anos, morte violenta por arma branca, na Rua Simão Dias, Nº 473, em Aracaju.\r\n\r\nRelatório referente ao período de 18.06.2025 à 19.06.2025\r\n\r\n+ Terezinha Figueiredo de Santana, 95 anos, corpo recolhido no Hospital Cirurgia, em Aracaju, morte por queda da própria altura;\r\n\r\n+ Sidney Rafael de Jesus Santos, 24 anos, morte violenta por arma de fogo, na Rua Professora Aginália M. Sales, Nº 91, município de São Cristóvão;\r\n\r\n+ Geovanio Vieira dos Santos Gonçalves, 26 anos, corpo recolhido na UPA 24 Horas Maria Dulcineia dos Santos, no município de Nossa Senhora das Dores, morte violenta por arma de fogo;\r\n\r\n+ Roberta Luana Santos Costa, 33 anos, corpo recolhido no Hospital Haydeê de Carvalho Leite Santos, município de Canindé de São Francisco, morte por suspeita de envenenamento;\r\n\r\n+ Anderson Garcia dos Santos, 28 anos, corpo recolhido na UPA 24 Horas Maria Dulcineia dos Santos, no município de Nossa Senhora das Dores, morte violenta por arma de fogo;\r\n\r\n+ Ricardo Santos, 47 anos, morte por afogamento, no Povoado Mussuipe, município de Neópolis;\r\n\r\n+ Pedro Marcos Silva de Jesus, 58 anos, morte por acidente de trânsito do tipo queda de moto, na Ponte Aracaju/Barra dos Coqueiros, na capital sergipana;\r\n\r\n+ José do Carmo Correia, 55 anos, corpo recolhido na Rua Maria Pureza de Jesus, Nº 1.255, Coroa do Meio, em Aracaju, causa morte a esclarecer;\r\n\r\n+ Joelio Cardoso, 46 anos, proveniente do município de Umbaúba, corpo recolhido no Hospital de Urgências João Alves Filho (HUSE), morte por acidente de trânsito do tipo atropelamento por moto.\r\n\r\nPor: Portal de Notícias Edelson Freitas com informações do IML de Sergipe', '68f83c720de6c.jpg', 1, 'Geral', 'sim', 'publicado', 48, '2025-10-22 01:02:41', '2025-10-24 15:41:10'),
(2, 'BOLSA FAMÍLIA: GOVERNO FEDERAL PREVÊ CANCELAMENTO DE 101 MIL DE BENEFÍCIOS NESTE ANO', 'O governo federal prevê 101 mil desligamentos no Bolsa Família neste ano por conta das mudanças', 'O governo federal prevê 101 mil desligamentos no Bolsa Família neste ano por conta das mudanças que limitaram a permanência no programa em casos de aumento de renda. A projeção é do Ministério do Desenvolvimento e Assistência Social, Família e Combate à Fome (MDS).\r\n\r\nO impacto orçamentário será de R$ 59 milhões, segundo dados obtidos pelo Globo, por meio da Lei de Acesso à Informação (LEI). Segundo integrantes do governo, o efeito total da medida será sentido somente no ano que vem, e os recursos poupados deverão ser usados no próprio programa.\r\n\r\nA regra de proteção foi pensada com o objetivo de ser uma transição suave para a saída do programa em direção ao mercado de trabalho. Ela permite que a família continue recebendo a metade do benefício por um período mesmo que supere os limites de renda do programa graças a uma vaga de emprego ou de ganhos com um negócio próprio, por exemplo. Para ser elegível ao Bolsa Família, o rendimento por integrante da família tem de ser até R$ 218.\r\n\r\nMudança\r\nDesde 2023, o tempo de permanência era de dois anos. Em maio, porém, o governo cortou esse período à metade, garantindo o pagamento de parte do auxílio por um ano. Famílias que tenham integrantes com renda estável e permanente, como aposentadoria, pensão ou algum idoso com Benefício de Prestação Continuada (BPC/Loas), poderão ficar no programa por dois meses.\r\n\r\nAlém disso, o teto de renda que dá acesso à regra de proteção também foi reduzido, de meio salário mínimo (R$ 759 em 2025) para R$ 706. As mudanças não afetam quem já estava na regra de proteção até abril. Em qualquer um dos casos, se durante o período de transição a renda voltar a ficar abaixo de R$ 218, a família volta a receber 100% do benefício.\r\n\r\nReduzir a fila de espera\r\nSegundo os cálculos do governo, a maior parte da economia vem do novo limite de renda, de R$ 706. Só no primeiro mês, a folha de junho, esperam-se 15.403 cancelamentos, o equivalente a R$ 10,3 milhões, considerando um benefício médio de R$ 671. Até dezembro, o governo calcula que 7.701 famílias deixarão o programa a cada mês. Com isso, a economia total no ano atingiria R$ 41,3 milhões.\r\n\r\nJá o efeito do encurtamento do prazo de permanência de dois para um ano só deve ser sentido em 2026. Em relação às famílias com alguma renda permanente, que agora só têm direito a dois meses de proteção, espera-se uma redução de 8 mil beneficiários por mês de julho a dezembro. Nesse caso, o benefício médio é de R$ 336,77, o que resultaria em uma economia este ano de R$ 17,6 milhões.\r\n\r\nAs mudanças na regra de proteção visam a aumentar a focalização do programa, de modo a privilegiar as famílias mais vulneráveis, em um contexto de orçamento apertado. O Bolsa Família sofreu um corte de R$ 7,7 bilhões no Orçamento deste ano.\r\n\r\n“O objetivo das mudanças é reduzir a fila de espera e priorizar famílias que de fato estão em situação de pobreza ou pobreza extrema, além de promover ajustes para manter a sustentabilidade e efetividade do programa de transferência de renda”, afirmou o governo em nota.\r\n\r\nMercado de trabalho\r\nO MDS avalia que o público do programa tem conseguido maior acesso ao mercado de trabalho, como mostram os últimos dados do Cadastro Geral de Empregados e Desempregados (Caged). Em 2024, 75,5% do saldo positivo do Caged (1,6 milhão de vagas com carteira assinada) era de beneficiários do Bolsa Família.\r\n\r\nSegundo os documentos preparatórios da mudança na regra de proteção, foram levadas em conta recomendações de um estudo do Banco Mundial e análises do MDS.\r\n\r\nDados do programa indicam que, após ingressarem na regra de proteção, cerca de 90% das famílias apresentaram elevação da renda decorrente de vínculo formal de trabalho. O tempo médio de permanência na regra é de oito meses. Para o MDS, “isso mostra que o mecanismo tem cumprido seu papel de incentivar a autonomia com segurança”.\r\n\r\nProteção\r\nNos documentos a que O Globo teve acesso, a pasta destacou que a nova duração da regra de proteção, de 12 meses, é o tempo necessário para o primeiro acesso ao seguro-desemprego. Isso, argumenta o MDS, impede que a família fique desassistida.\r\n\r\nO governo também concluiu que vincular ao salário mínimo o teto de renda para ter acesso à regra de proteção poderia privilegiar quem tem renda do trabalho e está fora da linha de pobreza, em detrimento de famílias mais vulneráveis. Além disso, a redução no limite de renda considerou padrões internacionais.\r\n\r\n“Com essa medida, o Bolsa Família reafirma seu papel como política de combate à pobreza e à desigualdade, sem abrir mão da sustentabilidade e da efetividade no uso dos recursos”, afirmou o MDS em nota.\r\n\r\nA pasta também tem buscado ampliar a participação de estados e municípios na rede de proteção dos beneficiários do programa, principalmente por meio de programas de qualificação profissional.\r\n\r\nCombate a fraudes\r\nNo atual governo, o Bolsa Família tem sido alvo de medidas de combate a fraudes. Há, por exemplo, um esforço para verificar a regularidade dos benefícios para famílias com um único membro, as chamadas unipessoais. Na gestão Jair Bolsonaro, os auxílios para pessoas que moram sozinhas dispararam diante das mudanças nas regras do programa.\r\n\r\nRecentemente, o Ministério do Desenvolvimento Social estabeleceu que novos cadastros de unipessoais só poderão ser feitos com base em uma coleta de informações presencial, de modo a combater fraudes.\r\n\r\nJá estava em vigor um teto de 16% por município para a parcela dos beneficiários que moravam sozinhos. Caso esse percentual seja ultrapassado, o município fica proibido de cadastrar novos beneficiários unipessoais. Na avaliação do ministério, porém, havia muita volatilidade. Assim que o percentual baixava de 16%, os municípios voltavam a cadastrar e essa fatia aumentava de novo. (Extra)', '68f837c8c957b.jpg', 1, 'Política', 'sim', 'publicado', 37, '2025-10-22 01:47:52', '2025-10-24 18:42:57'),
(3, 'PÉ-DE-MEIA: CALENDÁRIO DE PAGAMENTO COMEÇA NA PRÓXIMA SEGUNDA, 23', 'Nascidos em janeiro e fevereiro: 23 de junho;\r\nNascidos em março e abril: 24 de junho;', 'Nascidos em janeiro e fevereiro: 23 de junho;\r\nNascidos em março e abril: 24 de junho;\r\nNascidos em maio e junho: 25 de junho;\r\nNascidos em julho e agosto: 26 de junho;\r\nNascidos em setembro e outubro: 27 de junho;\r\nNascidos em novembro e dezembro: 30 de junho.', '68fb138354543.jpg', 1, 'Local', 'sim', 'publicado', 3, '2025-10-24 05:49:55', '2025-10-24 16:35:53'),
(4, 'FERIADOS: GOVERNO FEDERAL ADIA PARA 2026 NOVA REGRA SOBRE TRABALHO NO COMÉRCIO', 'O Ministério do Trabalho e Emprego (MTE) decidiu adiar para 1º de março de 2026 a entrada em vigor das novas regras sobre o trabalho em feriados no setor do comércio. ', 'O Ministério do Trabalho e Emprego (MTE) decidiu adiar para 1º de março de 2026 a entrada em vigor das novas regras sobre o trabalho em feriados no setor do comércio. A medida, que seria implementada a partir de 1º de julho deste ano, deve ser publicada oficialmente no Diário Oficial da União nesta quarta-feira (18).\r\n\r\nA portaria nº 3.665/2023, editada em novembro do ano passado, determina que o funcionamento do comércio em feriados só poderá ocorrer mediante negociação entre empregadores e trabalhadores, por meio de convenção coletiva, além de respeitar a legislação municipal vigente.\r\n\r\nA norma substitui a portaria nº 671/2021, aprovada durante o governo anterior, que permitia o trabalho nesses dias sem a exigência de acordos coletivos.\r\n\r\nSegundo o ministro Luiz Marinho, o adiamento tem como objetivo conceder um “prazo técnico para consolidar as negociações” entre as partes envolvidas. A decisão vem após pressão de entidades empresariais e parlamentares, e representa o quarto adiamento da medida pelo governo federal.\r\n\r\nO novo prazo amplia o período de transição para empregadores e sindicatos adaptarem-se às exigências da norma, que busca fortalecer a mediação coletiva nas relações de trabalho envolvendo feriados.', '68fb1406396d1.png', 1, 'Política', 'sim', 'publicado', 7, '2025-10-24 05:52:06', '2025-10-24 17:19:16'),
(5, 'PRESSÃO ARTERIAL: NOVA DIRETRIZ REDEFINE VALORES', 'A Sociedade Europeia de Cardiologia (ESC) atualizou suas diretrizes e alterou os parâmetros considerados normais', 'A Sociedade Europeia de Cardiologia (ESC) atualizou suas diretrizes e alterou os parâmetros considerados normais para a pressão arterial. De acordo com a nova classificação, valores entre 120/70 mmHg e 130/80 mmHg — anteriormente vistos como dentro da normalidade — passam a ser enquadrados na categoria de “pressão elevada”.\r\n\r\nA mudança se baseia em estudos recentes que apontam que mesmo pequenas elevações nos níveis de pressão arterial estão associadas a um maior risco de problemas cardiovasculares, como infarto e AVC. A ESC destaca que, embora esses valores ainda não configurem hipertensão, já indicam a necessidade de atenção médica.\r\n\r\nCom a nova abordagem, o objetivo é reforçar a prevenção. Pessoas com “pressão elevada” poderão ser acompanhadas mais de perto, com foco em mudanças no estilo de vida, controle de fatores de risco e, quando necessário, intervenções médicas precoces — tudo antes que se desenvolva um quadro de hipertensão.', '68fb14a09503c.jpg', 1, 'Geral', 'sim', 'publicado', 11, '2025-10-24 05:54:40', '2025-10-24 17:57:19'),
(6, 'BRASILEIRÃO 2025: TABELA É DIVULGADA; CONFIRA OS JOGOS', 'A CBF divulgou, na noite desta quarta-feira (12), a tabela do Campeonato Brasileiro de 2025.', 'A CBF divulgou, na noite desta quarta-feira (12), a tabela do Campeonato Brasileiro de 2025. Os destaques da 1ª rodada ficam para Palmeiras x Botafogo, Vasco x Santos e Flamengo x Inter.\r\n\r\nO campeonato será interrompido na 12ª rodada (em 11 ou 12 de junho) para a disputa do Mundial de Clubes. Palmeiras, Flamengo, Fluminense, Botafogo são os participantes da competição.\r\n \r\nA CBF decidiu soltar a tabela, o regulamento e os documentos técnicos do Brasileirão antes do conselho técnico da Série A para cumprir o que está previsto na legislação: esse material precisa ficar público em até 45 dias antes do início da competição.\r\nA tabela detalhada, com as datas de cada jogo, horário e transmissão, ainda será divulgada pela CBF.\r\n\r\nO UOL lista, abaixo, todos os jogos do 1° turno. O returno terá os mesmos duelos, mas com mandos invertidos.', '68fbc9e814065.jpg', 1, 'Esportes', 'sim', 'publicado', 0, '2025-10-24 18:48:08', '2025-10-24 18:48:08'),
(7, 'CARIOCA 2025: FLAMENGO BATE O VASCO EM ÓTIMO CLÁSSICO NO MARACANÃ', 'Flamengo venceu o primeiro Clássico dos Milhões de 2025', 'O Flamengo venceu o primeiro Clássico dos Milhões de 2025. Com gols de Bruno Henrique, de pênalti e Cebolinha, o Rubro-negro bateu o Vasco por 2 a 0 no Maracanã. Com um time melhor em cada tempo, o duelo foi muito bom e contou ainda com o melhor público deste campeonato, com pouco mais de 44 mil presentes.\r\n\r\nPrimeiro tempo\r\nO Flamengo comandou as ações nos 45 minutos iniciais. Com controle do jogo e sem permitir que o Vasco se encontrasse em campo, o Rubro-negro poderia ter feito um placar elástico no primeiro tempo. Michael, Plata, Bruno Henrique e Wesley infernizavam a zaga Cruzmaltina em todos os ataques, principalmente pelas pontas. Aos 30 minutos, em jogada individual de Plata, Zucarello cometeu o pênalti e Bruno Henrique bateu bem no canto, superando Léo Jardim e abrindo o placar. Aos 38 teve tempo de ampliar o marcador com Plata, de cabeça, mas o VAR viu impedimento no início da jogada. Enquanto isso, o Vasco assistia o Flamengo fazer o que queria.\r\n\r\nSegundo tempo\r\n \r\n\r\nNa volta do intervalo, o jogo teve uma reviravolta inesperada. O Vasco começou a encaixar a marcação e criar oportunidades de gol. Nos primeiros 20 minutos, somente o Cruzmaltino jogou e foi a vez do Flamengo assistir. Rossi fez boas defesas e Vegetti ainda cabeceou uma bola na trave. Com algumas mudanças de Filipe Luís, o Flamengo voltou a jogar melhor e ter chances de gol. O jogo ficou em aberto, com chances lá e cá. Aos 43, Cebolinha recebeu uma bola na ponta, cortou para dentro e acertou um belíssimo chute no ângulo, totalmente sem chances para Léo Jardim. Com 2 a 0 no placar, o Flamengo fechou o clássico e ficou com a vitória.\r\n\r\nO Flamengo volta a campo só no próximo fim de semana, contra o Maricá, pela última rodada do Campeonato Carioca. O Vasco tem compromisso na próxima terça-feira, contra o União Rondonópolis, pela Copa do Brasil. (Gustavo Mota/Super Rádio Tupi/Rio de Janeiro)', '68fbca4426b48.jpg', 1, 'Esportes', 'sim', 'publicado', 0, '2025-10-24 18:49:40', '2025-10-24 18:49:40'),
(8, 'BRASILEIRÃO 2025: VEJA QUEM SERÁ O CAMPEÃO; QUAIS CLUBES SERÃO REBAIXADOS DE ACORDO COM A INTELIGÊNCIA ARTIFICIAL', 'Com o Campeonato Brasileiro tendo início neste sábado (29), a expectativa toma conta dos torcedores de Botafogo', 'Com o Campeonato Brasileiro tendo início neste sábado (29), a expectativa toma conta dos torcedores de Botafogo, Flamengo, Fluminense e Vasco. Por isso, a Super Rádio Tupi consultou a inteligência artificial ‘Grok’ e pediu palpites para o Brasileirão dos quatro times. Com Flamengo e Botafogo entre os melhores, Fluminense na parte intermediária e o Vasco na luta contra o rebaixamento, a IA apostou no título do Palmeiras.\r\n\r\n“Vou te dar um palpite sobre as colocações dos clubes cariocas (Flamengo, Fluminense, Vasco e Botafogo) no Campeonato Brasileiro de 2025, além dos classificados para a Libertadores e os rebaixados, com base em uma análise geral e tendências observadas. Como não tenho acesso a dados definitivos do futuro, isso é apenas uma previsão especulativa e razoável.\r\n\r\nPalpite das colocações dos clubes cariocas:\r\nFlamengo: 2º lugar – O Flamengo tem mantido um elenco forte e consistência nos últimos anos, então é provável que brigue pelo título ou fique entre os primeiros.\r\nBotafogo: 4º lugar – Com investimentos recentes e boa gestão, o Botafogo deve se manter competitivo, garantindo uma vaga entre os melhores.\r\nFluminense: 11º lugar – O Fluminense pode oscilar, mas tende a ficar na parte intermediária da tabela, sem grandes riscos ou destaque.\r\nVasco: 14º lugar – O Vasco ainda está em processo de recuperação, então pode terminar fora da zona de rebaixamento, mas sem alcançar o topo.\r\nClassificados para a Libertadores:\r\nNo Brasileirão, geralmente os 6 primeiros colocados garantem vaga na Libertadores (os 4 primeiros diretos na fase de grupos, e 5º e 6º na pré-Libertadores), dependendo de outros torneios como Copa do Brasil. Meu palpite para os classificados:\r\n\r\nPalmeiras (campeão)\r\nFlamengo\r\nInternacional\r\nBotafogo\r\nAtlético-MG\r\nBahia\r\nRebaixados:\r\nOs 4 últimos colocados caem para a Série B. Meu palpite para os rebaixados:\r\n\r\nCeará\r\nSport\r\nMirassol\r\nVitória\r\nEsse palpite considera o desempenho histórico recente, investimentos e a força atual dos elencos, mas o futebol é imprevisível, e surpresas sempre podem acontecer.” \r\n\r\nCom informações de Wagner Ferreira da Super Rádio Tupi do Rio de Janeiro (RJ)', '68fbcaad8aa37.jpg', 1, 'Esportes', 'nao', 'publicado', 0, '2025-10-24 18:51:25', '2025-10-24 18:51:25'),
(9, 'VASCO E FLUMINENSE: INTELIGÊNCIA ARTIFICIAL FAZ PALPITES PARA OS DOIS TIMES NAS OITAVAS DA COPA SUL-AMERICANA', 'Nesta segunda-feira (2), a Conmebol realizou o sorteio das oitavas da Libertadores e da Sul-Americana', 'Nesta segunda-feira (2), a Conmebol realizou o sorteio das oitavas da Libertadores e da Sul-Americana com Flamengo, Vasco, Fluminense e Botafogo envolvidos. Pela Sul-Americana, o Tricolor vai enfrentar o vencedor de Bahia e América de Cali nos playoffs. Já o Vasco, se passar pelos playoffs, vai encarar o Mushuc Runa. Com a expectativa lá em cima por um título continental, a Super Rádio Tupi pediu para o ChatGPT fazer palpites de todos os cenários possíveis.\r\n\r\nConfira os palpites da inteligência artificial\r\nNesta segunda-feira (2), a Conmebol realizou o sorteio das oitavas da Libertadores e da Sul-Americana com Flamengo, Vasco, Fluminense e Botafogo envolvidos. Pela Sul-Americana, o Tricolor vai enfrentar o vencedor de Bahia e América de Cali nos playoffs. Já o Vasco, se passar pelos playoffs, vai encarar o Mushuc Runa. Com a expectativa lá em cima por um título continental, a Super Rádio Tupi pediu para o ChatGPT fazer palpites de todos os cenários possíveis.\r\nConfira os palpites da inteligência artificial\r\nFluminense:\r\n\r\nSe o adversário for o Bahia:\r\n\r\nCenário: Um confronto entre clubes brasileiros tende a ser equilibrado. O Bahia vive boa fase sob o comando de Rogério Ceni, com um time bem organizado e ofensivo.\r\nPonto-chave: O Fluminense costuma crescer em mata-mata e tem jogadores experientes (como Arias, Ganso e Cano), embora possa oscilar na temporada.\r\nPalpite: Jogo muito disputado, possivelmente decidido nos detalhes ou até nos pênaltis. O Fluminense teria leve favoritismo pela tradição recente em torneios continentais, mas o Bahia tem total capacidade de surpreender.\r\nSe o adversário for o América de Cali:\r\n\r\nCenário: O clube colombiano tem tradição, mas oscila no futebol local e pode sofrer em confrontos fora de casa.\r\nPonto-chave: A altitude não seria um fator relevante (Cali fica em altitude baixa), o que favorece o Flu. O estilo técnico do América pode dar espaços para o toque de bola tricolor.\r\nPalpite: Fluminense com certo favoritismo. Se jogar com foco e intensidade, tem boas chances de passar, especialmente se decidir no Maracanã.\r\nVasco:\r\n\r\nFase preliminar: Vasco x Independiente del Valle\r\n\r\nCenário: Jogo difícil. O Del Valle tem se consolidado como uma potência emergente na América do Sul, com excelente trabalho de base e estrutura.\r\nPonto-chave: O Vasco precisaria manter consistência tática e intensidade. O Del Valle, mesmo fora do Equador, costuma competir bem.\r\nPalpite: Confronto muito duro. Se o Vasco estiver encaixado e competitivo, pode surpreender. Mas não seria favorito.\r\nOitavas: Vasco x Mushuc Runa\r\n\r\nCenário: O Mushuc Runa é uma equipe modesta do Equador, de menor expressão internacional, e que joga na altitude (Ambato, cerca de 2.500m).\r\nPonto-chave: A altitude pode pesar no jogo fora, mas tecnicamente o Vasco é superior.\r\nPalpite: Se o Vasco chegar até aqui, é favorito contra o Mushuc Runa. Teria tudo para passar, desde que administre bem o jogo na altitude e confirme a vaga em São Januário. (Wagner Ferreira – Super Rádio Tupi – Rio de Janeiro/RJ)', '68fbcb39e3787.png', 1, 'Esportes', 'sim', 'publicado', 0, '2025-10-24 18:53:45', '2025-10-24 18:53:45'),
(10, 'BOTAFOGO E FLAMENGO: INTELIGÊNCIA ARTIFICIAL FAZ PALPITES OTIMISTAS PARA AS OITAVAS DA COPA LIBERTADORES', 'Nesta segunda-feira (2), a Conmebol realizou o sorteio das oitavas da Libertadores e da Sul-Americana com Flamengo', 'Nesta segunda-feira (2), a Conmebol realizou o sorteio das oitavas da Libertadores e da Sul-Americana com Flamengo, Vasco, Fluminense e Botafogo envolvidos. Pela Libertadores, o Flamengo vai enfrentar o Internacional, enquanto o Botafogo encara a LDU. Com a expectativa lá em cima por um título continental, a Super Rádio Tupi pediu para o ChatGPT fazer palpites de todos os cenários possíveis.\r\n\r\nConfira os palpites da inteligência artificial\r\nFlamengo x Internacional\r\nUm confronto nacional de muito peso, com dois elencos fortes e tradição na competição. O Flamengo tem mais profundidade no elenco e vem demonstrando evolução sob comando de Filipe Luís, com um meio-campo sólido e ataque letal com Pedro, Arrascaeta e Luís Araújo.\r\n\r\nO Internacional, embora com bons nomes como Enner Valencia e Alan Patrick, ainda oscila e depende bastante de atuações inspiradas de jogadores-chave. Além disso, o momento da equipe gaúcha é menos estável, especialmente defensivamente.\r\n\r\nPalpite: O Flamengo tem mais regularidade, melhor elenco e vem mais preparado taticamente. Deve se classificar, especialmente se conseguir controlar o jogo de volta fora de casa.\r\n\r\nNesta segunda-feira (2), a Conmebol realizou o sorteio das oitavas da Libertadores e da Sul-Americana com Flamengo, Vasco, Fluminense e Botafogo envolvidos. Pela Libertadores, o Flamengo vai enfrentar o Internacional, enquanto o Botafogo encara a LDU. Com a expectativa lá em cima por um título continental, a Super Rádio Tupi pediu para o ChatGPT fazer palpites de todos os cenários possíveis.\r\nConfira os palpites da inteligência artificial\r\nFlamengo x Internacional\r\nUm confronto nacional de muito peso, com dois elencos fortes e tradição na competição. O Flamengo tem mais profundidade no elenco e vem demonstrando evolução sob comando de Filipe Luís, com um meio-campo sólido e ataque letal com Pedro, Arrascaeta e Luís Araújo.\r\n\r\nO Internacional, embora com bons nomes como Enner Valencia e Alan Patrick, ainda oscila e depende bastante de atuações inspiradas de jogadores-chave. Além disso, o momento da equipe gaúcha é menos estável, especialmente defensivamente.\r\n\r\nPalpite: O Flamengo tem mais regularidade, melhor elenco e vem mais preparado taticamente. Deve se classificar, especialmente se conseguir controlar o jogo de volta fora de casa\r\n\r\n \r\nBotafogo x LDU\r\nEsse duelo é traiçoeiro. A LDU é experiente em mata-mata sul-americano, sabe jogar sob pressão e tem o fator altitude a seu favor em Quito.\r\n\r\nPor outro lado, o Botafogo tem mostrado bom desempenho neste momento da temporada, com uma defesa firme e ataque rápido. O time precisa abrir uma boa vantagem no Rio para depois só precisar sair vivo do jogo no Equador.\r\n\r\nPalpite: Confronto equilibrado, mas o Botafogo tem um elenco melhor. Com inteligência e intensidade, deve conseguir a classificação. (Wagner Ferreira – Super Rádio Tupi – Rio de Janeiro/RJ)', '68fbcfb39c25c.png', 1, 'Esportes', 'sim', 'publicado', 0, '2025-10-24 19:12:51', '2025-10-24 19:12:51'),
(11, 'Tudo o que se sabe sobre o escândalo de apostas que abalou a NBA', 'A NBA foi abalada na última quinta-feira (23) por uma investigação sobre apostas esportivas que prendeu dois astros da liga e tem mais de 30 suspeitos.', 'A NBA foi abalada na última quinta-feira (23) por uma investigação sobre apostas esportivas que prendeu dois astros da liga e tem mais de 30 suspeitos.\r\n\r\nAs acusações variam. Entre elas estão favorecimento de apostadores e envolvimento com jogos de pôquer fraudulentos supostamente em associação com famílias mafiosas.\r\n\r\nO escândalo acabou abrindo caminho para que o brasileiro Tiago Splitter se tornasse técnico interino do Portland Trail Blazers, algo inédito na NBA.', '68fbd0a4aaec4.jpg', 1, 'Esportes', 'sim', 'publicado', 0, '2025-10-24 19:16:52', '2025-10-24 19:16:52'),
(12, 'PL pressiona Câmara por anistia antes de possível prisão de Bolsonaro', 'Rebatizado de PL da Dosimetria, projeto só deve ser incluído na pauta por Hugo Motta após acordo com Senado', '\r\nCom a possibilidade de uma eventual decretação da prisão em regime fechado do ex-presidente Jair Bolsonaro (PL), o Partido Liberal voltará a insistir na aprovação do projeto de lei da Anistia na próxima semana.', '68fbd11865200.jpg', 1, 'Política', 'nao', 'publicado', 0, '2025-10-24 19:18:48', '2025-10-24 19:18:48'),
(13, 'Governo de São Paulo lança hub de dados para ampliar transparência sobre a situação hídrica', 'Iniciativa, desenvolvida pela Agência de Águas do Estado de São Paulo, busca ampliar a transparência sobre a situação hídrica e incentivar a população a adotar práticas de economia de água', 'O Governo de São Paulo lançou nesta sexta-feira (24) a plataforma SP Águas, um hub digital que reúne informações atualizadas sobre o abastecimento, os níveis dos reservatórios e as ações de resiliência hídrica no estado. A iniciativa, desenvolvida pela Agência de Águas do Estado de São Paulo, busca ampliar a transparência sobre a situação hídrica e incentivar a população a adotar práticas de economia de água.', '68fbd2babc4e3.jpg', 1, 'Política', 'sim', 'publicado', 0, '2025-10-24 19:25:46', '2025-10-24 19:25:56'),
(14, 'Moraes pede ajuda dos EUA para intimar Paulo Figueiredo no processo do golpe', 'Jornalista e influenciador vive fora do Brasil há cerca de dez anos e está sendo denunciado pela Procuradoria-Geral da República (PGR)', 'O ministro do Supremo Tribunal Federal (STF) Alexandre de Moraes, solicitou nesta quarta-feira (22) o envio de uma carta rogatória aos Estados Unidos, presidido por Donald Trump, para que possam ajudar a notificar Paulo Renato de Oliveira Figueiredo Filho, acusado de envolvimento em crimes relacionados aos atos de 8 de Janeiro.', '68fbd361a162a.png', 1, 'Política', 'sim', 'publicado', 0, '2025-10-24 19:28:33', '2025-10-24 19:28:33'),
(15, 'Relator da CPMI do INSS recusa visita a Bolsonaro para evitar questionamentos', 'Alfredo Gaspar destaca ainda que mantém ‘consideração, solidariedade e respeito’ pelo aliado, mas que, neste momento, prioriza suas atividades na comissão', 'O relator da CPMI do INSS, deputado Alfredo Gaspar (União Brasil-AL), enviou ofício ao ministro do Supremo Tribunal Federal (STF) Alexandre de Moraes comunicando o cancelamento da visita institucional ao ex-presidente Jair Bolsonaro, prevista para o dia 29 de outubro. A solicitação havia partido do próprio Bolsonaro.', '68fbd3b9e4ca8.jpg', 1, 'Política', 'sim', 'publicado', 0, '2025-10-24 19:30:01', '2025-10-24 19:30:01'),
(16, 'SIMÃO DIAS: VEJA INFORMAÇÕES SOBRE VALORES E NÚMEROS DO BPC E BOLSA FAMÍLIA', 'Nos primeiros três meses deste ano, o valor disponibilizado pelo governo federal para o pagamento do Bolsa Família ', 'Nos primeiros três meses deste ano, o valor disponibilizado pelo governo federal para o pagamento do Bolsa Família no município de Simão Dias/SE apresentou queda. Em janeiro, o valor disponível para pagamento do referido programa social no citado município sergipano foi de R$ 6.701.108,00. Em fevereiro, R$ 6.654.248,00 e em março, R$ 6.584.508,00. \r\n\r\nNo primeiro mês do ano, Simão Dias possuía 10.146 beneficiários; no mês seguinte, o número caiu para 10.113 e em março, 10.034. Neste três primeiros meses, 112 benefícios do Bolsa Família foram excluídos do sistema.\r\n\r\nBPC\r\n\r\nAté o momento, foram disponibilizadas informações somente referentes aos pagamentos de janeiro e fevereiro de 2025. Na ocasião, o governo federal liberou a quantia de R$ 2.961.683,75 para custeio no primeiro mês e R$ 2.961.683,75 também para o mês seguinte. No primeiro mês deste ano, o município de Simão Dias contava com 1.951 beneficiários ativos. Já no mês posterior manteve o mesmo número de pessoas apta de recebimento, 1.951.', '68fbd47f88ba9.jpg', 1, 'Local', 'sim', 'publicado', 0, '2025-10-24 19:33:19', '2025-10-24 19:33:19'),
(17, 'SIMÃO DIAS: POLÍCIA MILITAR APREENDE ADOLESCENTE POR PORTE ILEGAL DE ARMA DE FOGO DURANTE OPERAÇÃO RENOE', 'Na manhã desta quinta-feira (29), policiais do Batalhão de Polícia de Radiopatrulha (BPRp) efetuaram a apreensão de um adolescente por porte ilegal de arma de fogo.', 'Na manhã desta quinta-feira (29), policiais do Batalhão de Polícia de Radiopatrulha (BPRp) efetuaram a apreensão de um adolescente por porte ilegal de arma de fogo. O fato ocorreu no Conjunto Mutirão, no município de Simão Dias.\r\n\r\nDurante a operação, a equipe recebeu informações de que dois irmãos estariam de posse de uma arma de fogo e escondidos na residência do pai.\r\n\r\nAo chegarem ao local, os policiais encontraram uma equipe da Polícia Civil, que já estava cumprindo um mandado de busca e apreensão. Momentos depois, foi encontrado um revólver calibre .32 com quatro munições, além de mais três munições calibre .12. Na abordagem, um dos adolescentes que se encontrava na residência assumiu a posse do material.\r\n\r\nDiante do flagrante, o jovem e seu responsável legal foram encaminhados à delegacia de Simão Dias.\r\n\r\nInformações: Assessoria de Comunicação da Polícia Militar de Sergipe (PM/SE)', '68fbd4d5a09df.jpg', 1, 'Local', 'sim', 'publicado', 0, '2025-10-24 19:34:45', '2025-10-24 19:34:45'),
(18, 'SIMÃO DIAS: POLÍCIA CIVIL DEFLAGRA OPERAÇÃO CONTRA ORGANIZAÇÃO CRIMINOSA', 'A Polícia Civil deflagrou, na manhã desta quinta-feira (29), a Operação Damnatus, com o objetivo de cumprir 11 mandados de busca e apreensão', 'A Polícia Civil deflagrou, na manhã desta quinta-feira (29), a Operação Damnatus, com o objetivo de cumprir 11 mandados de busca e apreensão e um mandado de prisão preventiva no município de Simão Dias. Durante a operação, foram apreendidos vários celulares, uma grande quantidade de drogas e quatro armas de fogo.\r\n\r\nA ação foi motivada por fatos ocorridos no dia 9 de abril deste ano, quando uma organização criminosa tentou assumir o controle de uma região da cidade. No decorrer da operação, três dos investigados reagiram à abordagem policial e atiraram contra os agentes. Houve confronto, e os envolvidos foram atingidos, não resistiram aos ferimentos e vieram a óbito.\r\n\r\nA Polícia Civil solicita que informações e denúncias sobre crimes e suspeitos de ações criminosas sejam repassadas às autoridades por meio do Disque-Denúncia (181). O sigilo é garantido.\r\n\r\nInformações: Secretaria de Segurança Pública de Sergipe (SSP/SE)', '68fbd62c3cd0b.jpg', 1, 'Local', 'sim', 'publicado', 1, '2025-10-24 19:40:28', '2025-10-24 20:28:04'),
(19, 'SIMÃO DIAS: CONVOCAÇÃO PARA APROVADOS E SUPLENENTES DO JOVEM APRENDIZ DA DAKOTA', 'Os(as)  aprovados(as)  e suplentes abaixo devem comparecer', '\r\nJOVEM APRENDIZ\r\n \r\nOs(as)  aprovados(as)  e suplentes abaixo devem comparecer\r\n \r\nna empresa na Sexta-feira  dia 27.06.2025 às 07:20\r\n \r\nTrazer: caneta azul  e os documentos abaixo\r\n \r\nO não comparecimento será entendido como desistência da vaga\r\n \r\nTRAZER TODOS OS DOCUMENTOS  –  XEROX LEGÍVEL\r\n \r\nCPF, Titulo de Eleitor, Cartão do SUS, RG\r\n \r\nCarteira de Trabalho Digital: comprovante que está regular/gerada\r\n \r\nCarteira fisica para quem tiver:  xerox da pág da foto e do verso ( dados)\r\n \r\n01 foto colorida 3×4\r\n \r\nCert.de Nascimento – solteiros / Cert.Casamento ou divórcio\r\n \r\nBoletim, declaração ou histórico escolar ou comprovante de matrícula\r\n \r\nComprovante de residência: exceto Iguá ! Energisa no seu nome, se não tiver, trazer:\r\n \r\ndeclaração de residência assinada pelo dono com assinatura registrada em cartório, ou\r\n \r\n2 comprovantes diferentes  em seu nome, fatura, internet, telefone, loja ,etc.. ( não vale 2 faturas de banco)\r\n \r\nou xerox do documento do imóvel,  ou do terreno ou contrato de aluguel\r\n \r\nSituação Cadastral do CPF pelo site: www.receita.fazenda.gov.br\r\n \r\nNumero do PIS: Pode ser encontrado: Aplicativo Caixa Trabalhador, ligando no 135, meu INSS\r\n \r\nagencia da CEF ou pelo 0800 726 0207,  site do CNIS, cartão do cidadão ou extrato do FGTS\r\n \r\nPara homens: reservista ou prova de alistamento militar\r\n \r\nFilhos: Cert. Nascimento, CPF, RG e Cartão do SUS,  de qualquer idade\r\n \r\nObs.: Trazer dentro de um envelope com o nome por fora do candidato\r\n \r\nNão nos responsabilizamos pela grafia dos nomes, foram extraídos diretamente do link.\r\n \r\nAdenilson De Oliveira Santana\r\n \r\nadila menezes de jesus\r\n \r\nAdinalvo Nunes de Carvalho\r\n \r\nAlana Santos Pereira\r\n \r\nAline da costa santos\r\n \r\nAna Clecia Santana dos Santos\r\n \r\nAndressa santana de menezes\r\n \r\nAnthonny Fernandes Santos Das Virgens\r\n \r\nAnthony Moacir Santos Pereira\r\n \r\nAntoniel Sales Batista Rosa\r\n \r\nBruno Santana souza\r\n \r\nCamila Menezes Carvalho\r\n \r\nCamila Santos Batista\r\n \r\nCamile de Jesus santos\r\n \r\nCarine Nascimento Das virgens\r\n \r\nCarla Alcione Santos de Andrade\r\n \r\nCarlos Henrique Fernandes Santos\r\n \r\nCassandra Damasceno de Jesus\r\n \r\nCauã Santos Castro\r\n \r\nClara Gabrielle Oliveira Santos\r\n \r\nCrislaine Oliveira Ramos\r\n \r\nCristiano Santana Santos\r\n \r\nDanilo Oliveira Santos\r\n \r\nDavi Roberto de Andrade Fontes\r\n \r\nDavid Souza dos Santos\r\n \r\nDiogo Santos Santana\r\n \r\nEmanoel Nascimento Santos\r\n \r\nEmile siqueira Costa\r\n \r\nÉrica Fernanda Andrade Santos\r\n \r\nEstefany de Jesus Santos\r\n \r\nEvellin Rayane Oliveira Matos\r\n \r\nFlávio Antônio da Cruz Silva\r\n \r\nGilmario de Oliveira Santana\r\n \r\nGrazielly Souza Santos\r\n \r\nIasmim soares de souza\r\n \r\nIgor Souza dos Santos\r\n \r\nIngredy Conceição de Santana Santos\r\n \r\nÍris Kamilla Reis Bomfim\r\n \r\nIsaac Arthur de Moura Prata\r\n \r\nIsaac kenedy Santos Pereira\r\n \r\nJainane Maikelle Dos Santos\r\n \r\nJoão Guilherme Santos Pinto\r\n \r\nJoão Paulo De Souza Santos\r\n \r\nJonas Carlos dos Santos\r\n \r\nJosé Eduardo de Souza Santos\r\n \r\nJosé herlon dos Santos Assunção\r\n \r\nJosé Mário Matos Reis\r\n \r\nJosefa Monique Silva de Jesus\r\n \r\nJosilene Leal\r\n \r\nJuscelino Ferreira dos Santos Santana\r\n \r\nKawan Henrique Nascimento Silva\r\n \r\nKawan Martins da Silva Rodrigues\r\n \r\nLaiane Conceição de Santana\r\n \r\nLaisa Souza Santos\r\n \r\nLarissa Vitoria De Almeida Santos\r\n \r\nLázaro Costa de Santana\r\n \r\nLeonardo Dos Santos Santana\r\n \r\nLetícia dos Santos Conceição\r\n \r\nLÍVIA SANTOS DE ALMEIDA\r\n \r\nLucas Dos Santos De Jesus\r\n \r\nMarco Antônio de Jesus Santos\r\n \r\nMaria Eduarda de Santana Santos\r\n \r\nMaria Jamille Nunes Santana\r\n \r\nMaria Letícia de jesus Souza\r\n \r\nMaria Luiza Santos Santa Rosa\r\n \r\nMaria Tainá Souza de Oliveira\r\n \r\nMariana de Jesus Morais\r\n \r\nMatias de Souza Nascimento\r\n \r\nMikael Santos da Cruz\r\n \r\nnicolly fraga dos santos\r\n \r\nNina Valéria de Souza Matos .\r\n \r\nPablo Riquelme Da Silva Menezes\r\n \r\nPedro Vitor de jesus Alves\r\n \r\nPriscila De Jesus Santos\r\n \r\nPriscila Silva Santos\r\n \r\nRafael de Almeida santos\r\n \r\nRai de Almeida Santos\r\n \r\nRaissa dos Santos\r\n \r\nRanielly de Souza Santos\r\n \r\nRayssa Santos de Souza\r\n \r\nSantilio Santana de Jesus\r\n \r\nSilmara Gomes de Oliveira\r\n \r\nTairane de jesus ferreira silva\r\n \r\nThaís dos Santos do Nascimento\r\n \r\nValdeclan Monteiro Carneiro\r\n \r\nVanessa Santos Menezes\r\n \r\nVerônica Souza Dos Santos\r\n \r\nVictor Natanael Santana Santos\r\n \r\nVinicius de Souza Alves\r\n \r\nVITOR APARECIDO ANDRADE NASCIMENTO\r\n \r\nVitoria Cordeiro Santos\r\n \r\nYara Thayna Gomes de Oliveira\r\n \r\n \r\n \r\n \r\n \r\nSuplentes\r\n \r\nDanielly iara braga\r\n \r\nDarlan de Jesus Santana\r\n \r\nDyego Gabriel Do Nascimento Andrade\r\n \r\nLarissa Santana silva\r\n \r\nMarco Antônio Mendes Do Nascimento\r\n \r\nMayara bomfim santos\r\n \r\nMaysa de jesus oliveira\r\n \r\nNicole Costa Santos\r\n \r\nTainara aguiar de Santana\r\n \r\nVerônica de Souza Santos\r\n \r\nWelithon Emerson Leal Matos', '68fbd6b465d6d.jpg', 1, 'Local', 'sim', 'publicado', 0, '2025-10-24 19:42:44', '2025-10-24 19:42:44'),
(20, 'SIMÃO DIAS: PARÓQUIA DIVULGA PROGRAMAÇÃO PARA CORPUS CHRISTI', 'Em Simão Dias, a celebração de Corpus Christi é uma data importante para a comunidade católica, marcada por celebrações religiosas e, em 2025', 'Em Simão Dias, a celebração de Corpus Christi é uma data importante para a comunidade católica, marcada por celebrações religiosas e, em 2025, resultou em ponto facultativo na quinta-feira, 19 de junho, e na sexta-feira, 20 de junho, conforme decretado pela Prefeitura. Em anos anteriores, também houve procissões que atraíram muitos fiéis e autoridades, como a presença do governador. \r\nRelevância religiosa: É uma celebração litúrgica católica que simboliza a presença de Cristo na Eucaristia.\r\nPonto facultativo: Em 2025, a prefeitura decretou ponto facultativo na quinta-feira (19 de junho) e na sexta-feira (20 de junho) por causa do feriado religioso.\r\nServiços essenciais: Em anos anteriores, os serviços essenciais como saúde e coleta de lixo continuaram funcionando durante o ponto facultativo, de acordo com a administração municipal.\r\nEventos pasados: Em 2019, por exemplo, a celebração contou com missa e procissão que lotaram as ruas da cidade, com a participação do então governador. ', '68fbd732055a5.jpg', 1, 'Local', 'sim', 'publicado', 0, '2025-10-24 19:44:50', '2025-10-24 19:44:50'),
(21, 'Trump encerra negociações comerciais entre EUA e Canadá', 'O presidente dos Estados Unidos, Donald Trump, anunciou o encerramento de todas as negociações comerciais com o Canadá.', 'O presidente dos Estados Unidos, Donald Trump, anunciou o encerramento de todas as negociações comerciais com o Canadá. A decisão representa uma escalada nas tensões entre os dois países vizinhos, que mantêm uma das maiores parcerias comerciais do mundo. Segundo analistas, a medida funciona como um recado direto não apenas para o Canadá, mas para todas as nações afetadas pelas políticas tarifárias de Trump: ou as negociações seguem seus termos, ou não haverá acordo.', '68fbd7bf316c2.jpg', 1, 'Política', 'sim', 'publicado', 1, '2025-10-24 19:47:11', '2025-10-24 19:50:59'),
(22, 'China e EUA podem encontrar soluções para disputa comercial', 'China e Estados Unidos podem “encontrar formas de resolver as preocupações um do outro”,', 'China e Estados Unidos podem “encontrar formas de resolver as preocupações um do outro”, afirmou nesta sexta-feira (24) o ministro chinês do Comércio, antes de uma reunião entre representantes dos dois países na Malásia que se concentrará nas disputas comerciais. As duas maiores economias do mundo passaram grande parte do ano envolvidas em disputas comerciais, mas no momento parecem que tentam evitar um agravamento ainda maior.', '68fbd8099d7de.jpg', 1, 'Política', 'sim', 'publicado', 8, '2025-10-24 19:48:25', '2025-10-24 20:08:39');

-- --------------------------------------------------------

--
-- Estrutura para tabela `usuarios`
--

CREATE TABLE `usuarios` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `nome` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Despejando dados para a tabela `usuarios`
--

INSERT INTO `usuarios` (`id`, `username`, `password`, `nome`, `email`, `created_at`) VALUES
(1, 'edelson', '$2y$10$wlYoUVv2WnBH6fa2/HtWF.iS2XSqsAjBBhdtZXweFb6MGsb1bt7DW', 'Edelson Freitas', 'contato@edelsonfreitas.com', '2025-10-21 21:46:08');

--
-- Índices para tabelas despejadas
--

--
-- Índices de tabela `anunciantes`
--
ALTER TABLE `anunciantes`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `banners`
--
ALTER TABLE `banners`
  ADD PRIMARY KEY (`id`),
  ADD KEY `anunciante_id` (`anunciante_id`);

--
-- Índices de tabela `configuracoes`
--
ALTER TABLE `configuracoes`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `chave` (`chave`);

--
-- Índices de tabela `noticias`
--
ALTER TABLE `noticias`
  ADD PRIMARY KEY (`id`),
  ADD KEY `autor_id` (`autor_id`);

--
-- Índices de tabela `usuarios`
--
ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- AUTO_INCREMENT para tabelas despejadas
--

--
-- AUTO_INCREMENT de tabela `anunciantes`
--
ALTER TABLE `anunciantes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de tabela `banners`
--
ALTER TABLE `banners`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT de tabela `configuracoes`
--
ALTER TABLE `configuracoes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT de tabela `noticias`
--
ALTER TABLE `noticias`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;

--
-- AUTO_INCREMENT de tabela `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
