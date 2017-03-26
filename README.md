Memcahced para otimizaçao aplicações web com PHP.
=================================================

## Instalando o Memcached

1. Faça o download do arquivo php_memecache.dll no link: [windows.php.net](http://windows.php.net/downloads/pecl/releases/memcache/3.0.8/php_memcache-3.0.8-5.5-ts-vc11-x86.zip)

2. Descompacte o arquivo e copie o arquivo 'php_memcache.dll' para a pasta **C:\xampp\php\ext** ou o diretorio correspondente no seu computador.

3. Abra o seu php.ini localizado em:  
**C:\xampp\php**

4. No php.ini procure por "extension=" sem as apas e localize a linha a seguir:  
;extension=php_memcache.dll

5. Descomente removendo o "ponto-e-vírgula" do início, se não existir adicione:  
extension=php_memcache.dll

6. Opcionalmente você pode personalizar a extensão Memcache, exemplo:    
extension=php_memcache.dll  
[Memcache]  
memcache.allow_failover = 1  
memcache.max_failover_attempts = 20  
memcache.chunk_size = 8192  
memcache.default_port = 11211

5. Finalizado! Reinicie o servidor XAMPP, se tudo ocorreu certo deverá aparecer no phpinfo algo parecido como a imagem abaixo.

![Figura memcached](http://3.bp.blogspot.com/-DvJopE_ZLC4/VoTzNmCr5FI/AAAAAAAAAaY/O7fe1njFxsg/s1600/memcache-installed.png)

## Introdução
<p> O memcached é usado para otimizar processos de aplicações web e mobile, usando as melhores práticas para sua implementação dentro de suas aplicações e ambientes. Isto inclui o que deveria e o que não deveria ser armazenado, como tratar a distribuição flexível de dados e como regular o método para atualizar o memcached e versões armazenadas dos dados.</p>

Todos os aplicativos, especialmente muitos aplicatições web, precisam otimizar a velocidade com que acessam e retornam informações para o cliente. Frequentemente, no entanto, as mesmas informações são retornadas. Carregar os dados de sua origem de dados (banco de dados ou sistema de arquivos) é ineficiente, especialmente se forem executadas as mesmas consultas toda vez que quiser acessar as informações.

Apesar de vários servidores web poderem ser configurados para usar um cache para retornar informações, isto não funciona com a natureza dinâmica da maioria dos aplicativos. É aqui que o memcached pode ajudar. Ele fornece um armazenamento generalizado em memória que pode conter qualquer coisa, incluindo objetivos nativos de linguagens, permitindo o armazenamento de uma variedade de informações e acessá-las de muitos aplicativos e ambientes.

## Fundamentos
O memcached é um projeto de software livre projetado para fazer uso da RAM sobressalente em muitos servidores para agir como um cache de memória para informações acessadas com frequência. O elemento chave é o uso da palavra cache: o memcached fornece armazenamento temporário, em memória, das informações que podem ser carregadas de outro local.
Por exemplo, considere uma aplicação típica baseada em web. Mesmo um web site servido dinamicamente provavelmente tem alguns componentes de informações constantes durante a vida da página. Dentro de um blog, é improvável que a lista de categorias para os tópicos individuais do blog altere regularmente entre visualizações de páginas. Carregar estas informações toda vez durante uma consulta ao banco de dados é comparativamente caro, especialmente quando os dados não alteraram. É possível ver na Figura 1 alguns dos fragmentos de página dentro de um blog que poderiam ser armazenadas em cache.

![Figura 1](https://www.ibm.com/developerworks/br/opensource/library/os-memcached/image001.gif)

É possível identificar 10-20 consultas ao banco de dados e formatações que ocorrem somente para exibir o conteúdo da página principal. Repita isto para centenas, ou mesmo milhares de visualizações de páginas a cada dia, e seus servidores e aplicativos estarão executando muito mais consultas do que o necessário para exibir o conteúdo da página.
Usando o memcached, é possível armazenar as informações formatadas carregadas do banco de dados em um formulário pronto para ser usado diretamente na página web. E como as informações estão sendo carregadas da RAM, e não do disco por meio de um banco de dados e outros processamentos, o acesso às informações é quase instantâneo.

O memcached é um cache para armazenar informações usadas frequentemente para evitar o carregamento e o processamento de informações de origens mais lentas, como discos ou um banco de dados.
A interface para o memcached é fornecida por meio de uma conexão à rede. Isto significa que é possível compartilhar um único servidor do memcached (ou vários servidores, como será demonstrado posteriormente neste artigo) com vários clientes. A interface de rede é rápida e, para melhorar o desempenho, o servidor deliberadamente não suporta autenticação ou comunicação segura. Mas isto não deveria limitar as opções de implementação. O servidor do memcached deverá existir na parte interna de sua rede. A praticidade da interface de rede e a facilidade com que é possível implementar várias instâncias do memcached permitem o uso de RAM sobressalente em várias máquinas e aumentar o tamanho geral de seu cache.
O método de armazenamento com o memcached é um par simples de palavra-chave/valor, similar ao hash ou matriz associativa disponível em várias linguagens. As informações são armazenadas no memcached fornecendo a chave e o valor, e recuperadas solicitando as informações pela chave especificada.
As informações são retidas no cache indefinidamente, a não ser que um dos seguintes ocorra:
A memória alocada para o cache foi esgotada— Neste caso, o memcached usa o método least-recently used (LRU) para remover itens do cache. Itens que não tenham sido usados recentemente são excluídos do cache, primeiro as com acesso mais antigo.
O item é especificamente excluído— É sempre possível excluir um item do cache.
O item expirou — Itens individuais podem ter uma expiração para permitir que sejam retirados do cache quando as informações armazenadas com relação à chave são provavelmente muito antigas.
Essas situações podem ser usadas em combinação com a lógica de seu aplicativo para garantir que as informações no cache estejam atualizadas. Com isso em mente, vamos examinar o quão melhor é usar o memcached em seus aplicativos.

## Carregando informações para exibição ao usar o memcached

![Figura 2](https://www.ibm.com/developerworks/br/opensource/library/os-memcached/image003.gif)

O carregamento dos dados, então, se torna um processo de no máximo três estágios, carregando os dados do cache ou do banco de dados e armazenando no cache, se adequado.
Na primeira vez que este processo ocorre, os dados serão carregados do banco de dados ou outra origem, como normalmente, e, a seguir, armazenados no memcached. Da próxima vez que as informações forem armazenadas, elas serão retiradas do memcached, em vez de serem carregadas do banco de dados, economizando tempo e ciclos de CPU.
O outro lado da equação é garantir que, se as informações que possam ser armazenadas dentro do memcached forem alteradas, a versão do memcached é atualizada ao mesmo tempo em que são atualizadas as informações no backend. Isto modifica a sequência típica daquela mostrada na Figura 3 para a ligeira modificação na Figura 4.

![Figura 3](https://www.ibm.com/developerworks/br/opensource/library/os-memcached/image004.gif)

A Figura 4 mostra a sequência modificada usando o memcached

![Figura 3](https://www.ibm.com/developerworks/br/opensource/library/os-memcached/image005.gif)

Por exemplo, usando o blog como base, quando o sistema do blog atualiza a lista de categorias no banco de dados, a atualização deverá seguir esta sequência:
Atualizar a lista de categorias no banco de dados
Formatar as informações
Armazenar as informações no memcached
Retornar as informações para o cliente
Operações de armazenamento dentro do memcached são atômicas, portanto as informações serão atualizadas sem que os clientes recebam somente dados parciais; eles receberão a versão antiga ou a versão nova.
Para a maioria dos aplicativos, estas são as duas únicas operações com as quais você precisa se preocupar. Quando você acessar dados que as pessoas usam, eles são automaticamente adicionados ao cache e alterações àqueles dados são automaticamente atualizadas no cache.

fonte: http://www.ibm.com/developerworks/br/opensource/library/os-memcached/

#### Observação
O exemplo de demosntração do memcached implementado neste repositório não é o mesmo da introdução onde o exemplo usado é o mais simples para tentar apresentar seus conceitos, uso e vantangens.
