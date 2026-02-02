<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Faq;

class FaqSeeder extends Seeder
{
    public function run(): void
    {
        $faqs = [
            // CATEGORIA: SOBRE O PETGRE
            [
                'categoria' => 'Sobre o PetGre',
                'pergunta' => 'O que é o PetGre?',
                'resposta' => 'O PetGre é uma plataforma que conecta donos de pets a lojas e serviços especializados em produtos e cuidados para animais de estimação. Você encontra petshops, clínicas veterinárias, banho e tosa, e muito mais, tudo em um só lugar!',
                'ordem' => 1
            ],
            [
                'categoria' => 'Sobre o PetGre',
                'pergunta' => 'Como funciona o PetGre?',
                'resposta' => 'É simples! Você se cadastra gratuitamente, busca por lojas e produtos perto de você, adiciona os itens ao carrinho e envia o pedido direto para o WhatsApp da loja. A loja recebe seu pedido e entra em contato para confirmar e combinar entrega ou retirada.',
                'ordem' => 2
            ],
            [
                'categoria' => 'Sobre o PetGre',
                'pergunta' => 'O PetGre cobra alguma taxa?',
                'resposta' => 'Não! O uso do PetGre é 100% gratuito para clientes. Você não paga nada para fazer pedidos ou usar nossos serviços.',
                'ordem' => 3
            ],

            // CATEGORIA: CADASTRO E CONTA
            [
                'categoria' => 'Cadastro e Conta',
                'pergunta' => 'Como faço para me cadastrar?',
                'resposta' => 'Clique em "Entrar" na tela inicial, depois em "Criar conta". Preencha seu nome, e-mail e crie uma senha. Pronto! Você já pode começar a usar o PetGre.',
                'ordem' => 1
            ],
            [
                'categoria' => 'Cadastro e Conta',
                'pergunta' => 'Esqueci minha senha, o que faço?',
                'resposta' => 'Na tela de login, clique em "Esqueci minha senha". Digite seu e-mail cadastrado e você receberá instruções para criar uma nova senha.',
                'ordem' => 2
            ],
            [
                'categoria' => 'Cadastro e Conta',
                'pergunta' => 'Como edito meus dados cadastrais?',
                'resposta' => 'Acesse "Meu Perfil" > "Meus Dados". Lá você pode alterar seu nome, e-mail, telefone e senha. Não esqueça de salvar as alterações!',
                'ordem' => 3
            ],
            [
                'categoria' => 'Cadastro e Conta',
                'pergunta' => 'Como cadastro meu endereço?',
                'resposta' => 'Vá em "Meu Perfil" > "Meus Dados" > "Meus Endereços". Clique em "Adicionar Endereço" e preencha os dados: CEP, rua, número, bairro, cidade e complemento. Você pode cadastrar vários endereços!',
                'ordem' => 4
            ],
            [
                'categoria' => 'Cadastro e Conta',
                'pergunta' => 'Posso ter mais de um endereço cadastrado?',
                'resposta' => 'Sim! Você pode cadastrar quantos endereços quiser (casa, trabalho, etc). Na hora de fazer o pedido, basta escolher qual endereço deseja usar para entrega.',
                'ordem' => 5
            ],

            // CATEGORIA: COMO FAZER PEDIDOS
            [
                'categoria' => 'Como Fazer Pedidos',
                'pergunta' => 'Como faço um pedido?',
                'resposta' => '1. Busque a loja desejada, 2. Escolha os produtos e adicione ao carrinho, 3. Clique no carrinho, revise seu pedido, 4. Selecione endereço e forma de pagamento, 5. Clique em "Enviar Pedido via WhatsApp". Pronto! Seu pedido será enviado para a loja.',
                'ordem' => 1
            ],
            [
                'categoria' => 'Como Fazer Pedidos',
                'pergunta' => 'Posso fazer pedido em mais de uma loja ao mesmo tempo?',
                'resposta' => 'Não. Você precisa finalizar o pedido de uma loja antes de começar outro em outra loja. Isso garante que cada loja receba seu pedido correto e completo.',
                'ordem' => 2
            ],
            [
                'categoria' => 'Como Fazer Pedidos',
                'pergunta' => 'Posso editar meu pedido depois de enviar?',
                'resposta' => 'Depois que o pedido é enviado para o WhatsApp da loja, você precisa falar diretamente com a loja para fazer alterações. Eles podem te ajudar a modificar o pedido se ainda não iniciaram a preparação.',
                'ordem' => 3
            ],
            [
                'categoria' => 'Como Fazer Pedidos',
                'pergunta' => 'O que é pedido mínimo?',
                'resposta' => 'Pedido mínimo é o valor mínimo que a loja aceita para realizar uma entrega. Por exemplo, se o pedido mínimo é R$ 30, seu pedido precisa ter pelo menos R$ 30 em produtos para ser aceito pela loja.',
                'ordem' => 4
            ],
            [
                'categoria' => 'Como Fazer Pedidos',
                'pergunta' => 'Como funciona a entrega?',
                'resposta' => 'Cada loja tem suas próprias regras de entrega. Algumas entregam em determinados bairros, outras cobram taxa de entrega, e algumas oferecem entrega grátis acima de certo valor. Essas informações aparecem na página da loja.',
                'ordem' => 5
            ],

            // CATEGORIA: PAGAMENTO
            [
                'categoria' => 'Pagamento',
                'pergunta' => 'Quais formas de pagamento são aceitas?',
                'resposta' => 'Cada loja define suas próprias formas de pagamento. As opções comuns são: dinheiro, Pix, cartão de débito e crédito. Você escolhe a forma de pagamento na hora de fazer o pedido e combina os detalhes com a loja no WhatsApp.',
                'ordem' => 1
            ],
            [
                'categoria' => 'Pagamento',
                'pergunta' => 'O pagamento é feito pelo aplicativo?',
                'resposta' => 'Não. O PetGre não processa pagamentos. Você combina o pagamento direto com a loja via WhatsApp. O pagamento é feito na entrega ou quando você for retirar o pedido.',
                'ordem' => 2
            ],
            [
                'categoria' => 'Pagamento',
                'pergunta' => 'Posso pagar com Pix?',
                'resposta' => 'Sim, desde que a loja aceite Pix. Na hora de fazer o pedido, você seleciona "Pix" como forma de pagamento e a loja te envia a chave Pix via WhatsApp.',
                'ordem' => 3
            ],
            [
                'categoria' => 'Pagamento',
                'pergunta' => 'Preciso ter troco se for pagar em dinheiro?',
                'resposta' => 'Não necessariamente. No campo de observações do pedido, você pode informar se precisa de troco (exemplo: "Vou pagar com R$ 100"). Assim a loja já se organiza com o troco certo.',
                'ordem' => 4
            ],

            // CATEGORIA: CUPONS DE DESCONTO
            [
                'categoria' => 'Cupons de Desconto',
                'pergunta' => 'Como funciona o sistema de cupons?',
                'resposta' => 'Existem 2 tipos de cupons: 1) Cupons do Sistema (dados pelo PetGre) que você encontra em "Meus Cupons", e 2) Cupons da Loja (criados pela própria loja) que você vê nas redes sociais deles. Ambos dão desconto no seu pedido!',
                'ordem' => 1
            ],
            [
                'categoria' => 'Cupons de Desconto',
                'pergunta' => 'Como uso um cupom de desconto?',
                'resposta' => 'No carrinho, antes de enviar o pedido, clique em "Aplicar Cupom de Desconto". Digite o código do cupom e clique em "Aplicar". Se o cupom for válido, o desconto será aplicado automaticamente no total.',
                'ordem' => 2
            ],
            [
                'categoria' => 'Cupons de Desconto',
                'pergunta' => 'Onde vejo meus cupons disponíveis?',
                'resposta' => 'Acesse "Meu Perfil" > "Meus Cupons". Lá você vê todos os cupons que ganhou do PetGre, com informações de validade, desconto e em quais lojas pode usar.',
                'ordem' => 3
            ],
            [
                'categoria' => 'Cupons de Desconto',
                'pergunta' => 'Posso usar mais de um cupom no mesmo pedido?',
                'resposta' => 'Não. Você pode usar apenas 1 cupom por pedido. Escolha o cupom que dá mais desconto ou o que está perto de vencer!',
                'ordem' => 4
            ],
            [
                'categoria' => 'Cupons de Desconto',
                'pergunta' => 'O cupom não funcionou, por quê?',
                'resposta' => 'Verifique: 1) Se o cupom está válido (não expirou), 2) Se seu pedido atingiu o valor mínimo exigido, 3) Se a loja aceita aquele cupom, 4) Se você já não usou esse cupom antes. Se tudo estiver certo e ainda não funcionar, entre em contato com o suporte.',
                'ordem' => 5
            ],
            [
                'categoria' => 'Cupons de Desconto',
                'pergunta' => 'Se a loja cancelar meu pedido, perco o cupom?',
                'resposta' => 'Não! Se a loja cancelar seu pedido, o cupom volta automaticamente para você e pode ser usado em outro pedido.',
                'ordem' => 6
            ],

            // CATEGORIA: BUSCA E FILTROS
            [
                'categoria' => 'Busca e Filtros',
                'pergunta' => 'Como busco por uma loja específica?',
                'resposta' => 'Na tela inicial, use a barra de busca no topo e digite o nome da loja ou tipo de estabelecimento (exemplo: "petshop", "veterinária"). Os resultados aparecem automaticamente.',
                'ordem' => 1
            ],
            [
                'categoria' => 'Busca e Filtros',
                'pergunta' => 'Como filtro lojas por categoria?',
                'resposta' => 'Na tela inicial, logo abaixo da busca, você vê cards de categorias (Petshop, Veterinária, Banho e Tosa, etc). Clique na categoria desejada para ver apenas lojas daquele tipo.',
                'ordem' => 2
            ],
            [
                'categoria' => 'Busca e Filtros',
                'pergunta' => 'Como vejo só lojas abertas agora?',
                'resposta' => 'Clique no botão "Filtros" na tela inicial. Em "Status", selecione "Abertas Agora" e clique em "Aplicar Filtros". Você verá apenas lojas que estão funcionando no momento.',
                'ordem' => 3
            ],
            [
                'categoria' => 'Busca e Filtros',
                'pergunta' => 'Posso filtrar por avaliação?',
                'resposta' => 'Sim! Clique em "Filtros" e em "Avaliação Mínima" escolha quantas estrelas deseja (exemplo: 4 estrelas ou mais). Assim você vê apenas lojas bem avaliadas.',
                'ordem' => 4
            ],
            [
                'categoria' => 'Busca e Filtros',
                'pergunta' => 'Como ordenar os resultados?',
                'resposta' => 'Clique em "Filtros" e em "Ordenar Por" escolha: Relevância (padrão), Melhor Avaliação, Nome A-Z ou Nome Z-A. Clique em "Aplicar Filtros" para ver os resultados ordenados.',
                'ordem' => 5
            ],

            // CATEGORIA: LOJAS E PRODUTOS
            [
                'categoria' => 'Lojas e Produtos',
                'pergunta' => 'Como sei se uma loja está aberta?',
                'resposta' => 'Na listagem de lojas, você vê uma tag verde "Aberto" ou vermelha "Fechado" em cada loja. Você também pode ver o horário de funcionamento na página da loja.',
                'ordem' => 1
            ],
            [
                'categoria' => 'Lojas e Produtos',
                'pergunta' => 'Posso fazer pedido em loja fechada?',
                'resposta' => 'Sim, você pode montar seu pedido e enviar. A loja receberá e responderá quando abrir. Porém, é melhor fazer pedido quando a loja está aberta para ter resposta mais rápida.',
                'ordem' => 2
            ],
            [
                'categoria' => 'Lojas e Produtos',
                'pergunta' => 'Como adiciono um produto ao carrinho?',
                'resposta' => 'Entre na loja, clique no produto desejado, escolha a quantidade e clique em "Adicionar ao Carrinho". O produto ficará salvo até você finalizar ou limpar o carrinho.',
                'ordem' => 3
            ],
            [
                'categoria' => 'Lojas e Produtos',
                'pergunta' => 'Posso adicionar observações no pedido?',
                'resposta' => 'Sim! No carrinho, há um campo "Observações" onde você pode escrever informações importantes para a loja (exemplo: "retirar na loja", "produto para filhote", etc).',
                'ordem' => 4
            ],
            [
                'categoria' => 'Lojas e Produtos',
                'pergunta' => 'O que significa "produto a granel"?',
                'resposta' => 'Produtos a granel são vendidos por peso (exemplo: ração a granel, areia). Você especifica a quantidade em kg que deseja e a loja calcula o valor total.',
                'ordem' => 5
            ],

            // CATEGORIA: FAVORITOS E AVALIAÇÕES
            [
                'categoria' => 'Favoritos e Avaliações',
                'pergunta' => 'Como favorito uma loja?',
                'resposta' => 'Clique no ícone de coração (♡) que aparece em cada loja, tanto na listagem quanto na página da loja. O coração fica vermelho (♥) quando favoritado. Para desfavoritar, clique novamente.',
                'ordem' => 1
            ],
            [
                'categoria' => 'Favoritos e Avaliações',
                'pergunta' => 'Onde vejo minhas lojas favoritas?',
                'resposta' => 'Acesse "Meu Perfil" > "Lojas Favoritas". Lá você vê todas as lojas que favoritou, facilitando o acesso rápido aos seus estabelecimentos preferidos.',
                'ordem' => 2
            ],
            [
                'categoria' => 'Favoritos e Avaliações',
                'pergunta' => 'Como avalio uma loja?',
                'resposta' => 'Após receber seu pedido, vá em "Meus Pedidos", clique no pedido e depois em "Avaliar Pedido". Dê de 1 a 5 estrelas e escreva um comentário sobre sua experiência com a loja.',
                'ordem' => 3
            ],
            [
                'categoria' => 'Favoritos e Avaliações',
                'pergunta' => 'Posso avaliar antes de receber o pedido?',
                'resposta' => 'Não. A avaliação só fica disponível depois que o pedido for marcado como "Entregue" pela loja. Isso garante que você avalie baseado na experiência completa.',
                'ordem' => 4
            ],

            // CATEGORIA: PEDIDOS E ACOMPANHAMENTO
            [
                'categoria' => 'Pedidos e Acompanhamento',
                'pergunta' => 'Como acompanho meu pedido?',
                'resposta' => 'Vá em "Meu Perfil" > "Pedidos". Você vê todos seus pedidos com status atual: Pendente, Confirmado, Em Preparação, Em Entrega ou Entregue. Clique no pedido para ver detalhes.',
                'ordem' => 1
            ],
            [
                'categoria' => 'Pedidos e Acompanhamento',
                'pergunta' => 'Quanto tempo a loja demora para confirmar?',
                'resposta' => 'Depende de cada loja. Geralmente é rápido, entre alguns minutos a 1 hora. Se demorar muito, você pode entrar em contato diretamente pelo WhatsApp da loja.',
                'ordem' => 2
            ],
            [
                'categoria' => 'Pedidos e Acompanhamento',
                'pergunta' => 'Posso cancelar meu pedido?',
                'resposta' => 'Se o pedido ainda está "Pendente" e a loja não confirmou, você pode entrar em contato via WhatsApp para cancelar. Após confirmado, fale com a loja para verificar se ainda é possível cancelar.',
                'ordem' => 3
            ],
            [
                'categoria' => 'Pedidos e Acompanhamento',
                'pergunta' => 'O que significa cada status do pedido?',
                'resposta' => 'Pendente: aguardando confirmação da loja. Confirmado: loja aceitou seu pedido. Em Preparação: loja está preparando. Em Entrega: pedido saiu para entrega. Entregue: pedido foi entregue. Cancelado: pedido foi cancelado.',
                'ordem' => 4
            ],

            // CATEGORIA: PROBLEMAS E SUPORTE
            [
                'categoria' => 'Problemas e Suporte',
                'pergunta' => 'Tive um problema com meu pedido, o que faço?',
                'resposta' => 'Primeiro, entre em contato direto com a loja via WhatsApp. Se não resolver, entre em contato com nosso suporte através da página "Ajuda" > "Contato" no aplicativo.',
                'ordem' => 1
            ],
            [
                'categoria' => 'Problemas e Suporte',
                'pergunta' => 'Como entro em contato com o suporte do PetGre?',
                'resposta' => 'Acesse "Meu Perfil" > "Ajuda" > "Contato". Preencha o formulário com sua dúvida ou problema e nossa equipe responderá por e-mail em até 24 horas.',
                'ordem' => 2
            ],
            [
                'categoria' => 'Problemas e Suporte',
                'pergunta' => 'O aplicativo está com erro, o que faço?',
                'resposta' => 'Tente: 1) Fechar e abrir o aplicativo novamente, 2) Limpar o cache do navegador, 3) Atualizar a página. Se o problema persistir, entre em contato com o suporte informando o erro.',
                'ordem' => 3
            ],
            [
                'categoria' => 'Problemas e Suporte',
                'pergunta' => 'Não recebi meu pedido, o que fazer?',
                'resposta' => 'Entre em contato imediatamente com a loja via WhatsApp para saber o status. Se não conseguir resolver, entre em contato com nosso suporte informando o número do pedido.',
                'ordem' => 4
            ],

            // CATEGORIA: PARA LOJISTAS
            [
                'categoria' => 'Para Lojistas',
                'pergunta' => 'Como cadastro minha loja no PetGre?',
                'resposta' => 'Entre em contato conosco através do e-mail comercial@petgre.com.br ou pelo WhatsApp disponível na página de contato. Nossa equipe te ajudará com o cadastro e configuração da sua loja.',
                'ordem' => 1
            ],
            [
                'categoria' => 'Para Lojistas',
                'pergunta' => 'Quanto custa para ter minha loja no PetGre?',
                'resposta' => 'Temos planos acessíveis para todos os tamanhos de negócio. Entre em contato com nosso time comercial para conhecer as opções e escolher o melhor plano para você.',
                'ordem' => 2
            ],
            [
                'categoria' => 'Para Lojistas',
                'pergunta' => 'Como funciona o sistema de cupons para lojistas?',
                'resposta' => 'Como lojista, você pode criar seus próprios cupons de desconto para divulgar nas redes sociais. Além disso, pode aceitar cupons do sistema PetGre, onde nós restituímos o valor do desconto para você.',
                'ordem' => 3
            ],
        ];

        foreach ($faqs as $faq) {
            Faq::create($faq);
        }
    }
}
