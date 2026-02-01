# PetGre - Sistema Multiempresa de Pedidos e Vendas

## 📋 Visão Geral

O **PetGre** é um sistema multiempresa de pedidos e vendas focado inicialmente no nicho pet (petshops, agropecuárias, banho e tosa, etc.), funcionando como um "iFood light" sem marketplace. O sistema organiza, controla e profissionaliza o processo de vendas que hoje acontece de forma caótica via WhatsApp.

## 🎯 Conceito Central

**As lojas não vendem dentro do sistema, elas vendem via WhatsApp.**

O PetGre entra como **organizador, controlador e profissionalizador do caos**:
- Registra pedidos que chegam pelo WhatsApp
- Controla status de pedidos
- Gerencia pagamentos
- Organiza endereços de entrega
- Mantém histórico completo

## 🏗️ Arquitetura Multiempresa

### Estrutura de Usuários

#### 👑 **Masters (Donos de Empresas)**
- Podem ter **múltiplas empresas**
- Criam empresas automaticamente ao se cadastrar
- Têm controle total sobre suas empresas
- Só podem ver usuários da **mesma empresa**

#### 👷 **Funcionários**
- Trabalham em uma empresa específica
- Recebem permissões específicas
- Usam endereço da empresa automaticamente
- Só podem ver outros funcionários da mesma empresa

#### 🛒 **Clientes**
- Usuários finais que fazem pedidos
- Não estão vinculados a empresas
- Fazem pedidos nas empresas via site/app
- **Não podem ser visualizados** por ninguém (privacidade)

### Isolamento por Empresa

Cada empresa tem seu próprio ecossistema completamente isolado:
- ✅ Masters veem apenas funcionários da sua empresa
- ✅ Funcionários veem apenas colegas da mesma empresa
- ❌ Ninguém vê clientes de outras empresas
- ❌ Clientes não se veem entre si

## 🚀 Funcionalidades Principais

### 📊 **Painel para Lojistas**
- Controle de pedidos que chegam pelo WhatsApp
- Histórico completo de status
- Gestão de funcionários e permissões
- Configuração de formas de pagamento
- Definição de bairros e valores de entrega

### 🌐 **Site/App para Clientes**
- Catálogo de produtos por empresa
- Sistema de pedidos organizado
- Cadastro de endereços
- Histórico de pedidos
- Interface intuitiva para navegação

### 📦 **Gestão de Pedidos**
- Status padronizados (recebido, preparando, pronto, em entrega, entregue)
- Histórico de alterações
- Controle de pagamentos
- Endereços de entrega
- Itens do pedido com quantidades e valores

### 💰 **Sistema de Pagamentos**
- Configurável por empresa
- Múltiplas formas de pagamento
- Controle de status de pagamento
- Integração futura com gateways

### 🚚 **Sistema de Entrega**
- Entrega simples (fixa ou "a combinar")
- Bairros de entrega por empresa
- Valores configuráveis por bairro
- Controle de endereços

## 🛠️ Tecnologias Utilizadas

### Backend
- **Laravel 11** - Framework PHP
- **MySQL** - Banco de dados
- **Sanctum** - Autenticação API
- **Eloquent ORM** - Mapeamento objeto-relacional

### Estrutura de Dados
- **Usuários** (`usuarios`) - Masters, funcionários e clientes
- **Empresas** (`empresas`) - Petshops, agropecuárias, etc.
- **Produtos** (`produtos`) - Catálogo de cada empresa
- **Pedidos** (`pedidos`) - Controle completo de vendas
- **Permissões** (`permissoes`) - Sistema granular de acessos

## 📈 Modelo de Negócio

### Estratégia Inicial
- **Sem marketplace** - Cada empresa independente
- **Sem comissão por pedido** - Foco em organização, não receita direta
- **Modelo SaaS** - Assinatura mensal por empresa
- **Setup simples** - Entrega fixa ou "a combinar"

### Monetização
- Assinaturas mensais por empresa
- Possíveis upgrades (entrega avançada, relatórios, etc.)
- Sem interferir no processo de venda principal

### Escalabilidade
- **Pensado para crescer** sem refazer o banco
- Estrutura preparada para marketplace futuro
- APIs bem documentadas
- Código modular e organizado

## 🔄 Fluxo de Funcionamento

### 1. **Cadastro de Empresa**
```
Cliente interessado → Cadastra-se como Master → Sistema cria empresa automaticamente
```

### 2. **Configuração da Loja**
```
Master configura:
- Endereço da empresa
- Formas de pagamento
- Bairros de entrega
- Funcionários (com permissões)
- Produtos do catálogo
```

### 3. **Funcionamento do Dia a Dia**
```
Cliente acessa site/app → Vê produtos da empresa → Faz pedido organizado
↓
Sistema registra pedido → Master recebe notificação → Confirma via WhatsApp
↓
Atualiza status no sistema → Controla entrega → Histórico completo mantido
```

## 🎯 Diferenciais Competitivos

### Para Lojistas
- **Organização** do caos atual
- **Profissionalização** do atendimento
- **Histórico completo** de vendas
- **Controle de funcionários**
- **Setup rápido** e intuitivo

### Para Clientes
- **Pedidos organizados** vs WhatsApp bagunçado
- **Histórico completo** de compras
- **Endereços salvos**
- **Interface profissional**

### Para o Mercado
- **Modelo light** sem complexidade desnecessária
- **Foco no essencial** para pequenos negócios
- **Preço acessível** para começar
- **Escalável** para crescer com o negócio

## 📊 Status do Projeto

### ✅ **Implementado**
- Estrutura básica do banco de dados
- Autenticação e autorização
- Sistema multiempresa
- Controle de usuários e permissões
- Isolamento por empresa
- API RESTful básica

### 🚧 **Em Desenvolvimento**
- Sistema de produtos
- Gestão de pedidos
- Interface do cliente
- Painel administrativo

### 📋 **Próximas Etapas**
- Implementação completa do fluxo de pedidos
- Interface responsiva
- Sistema de notificações
- Relatórios e analytics
- Integrações de pagamento

## 🎨 Identidade Visual

- **Nome**: PetGre
- **Foco inicial**: Nicho pet (petshops, agropecuárias, banho e tosa)
- **Cores**: Paleta friendly, cores pets (azul, verde, amarelo)
- **Tom**: Profissional mas acessível

## 📞 Contato e Suporte

Sistema desenvolvido para profissionalizar o comércio local através da organização e controle de pedidos, transformando o caos do WhatsApp em um processo estruturado e eficiente.

---

**PetGre - Transformando pedidos em resultados!** 🐕🐱🛒