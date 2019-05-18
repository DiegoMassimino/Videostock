<?php
/**
 * As configurações básicas do WordPress
 *
 * O script de criação wp-config.php usa esse arquivo durante a instalação.
 * Você não precisa usar o site, você pode copiar este arquivo
 * para "wp-config.php" e preencher os valores.
 *
 * Este arquivo contém as seguintes configurações:
 *
 * * Configurações do MySQL
 * * Chaves secretas
 * * Prefixo do banco de dados
 * * ABSPATH
 *
 * @link https://codex.wordpress.org/pt-br:Editando_wp-config.php
 *
 * @package WordPress
 */

// ** Configurações do MySQL - Você pode pegar estas informações com o serviço de hospedagem ** //
/** O nome do banco de dados do WordPress */
define( 'DB_NAME', 'videos80_videostock' );

/** Usuário do banco de dados MySQL */
define( 'DB_USER', 'videos80_video10' );

/** Senha do banco de dados MySQL */
define( 'DB_PASSWORD', 'Doubleface2015' );

/** Nome do host do MySQL */
define( 'DB_HOST', 'localhost' );

/** Charset do banco de dados a ser usado na criação das tabelas. */
define( 'DB_CHARSET', 'utf8mb4' );

/** O tipo de Collate do banco de dados. Não altere isso se tiver dúvidas. */
define('DB_COLLATE', '');

/**#@+
 * Chaves únicas de autenticação e salts.
 *
 * Altere cada chave para um frase única!
 * Você pode gerá-las
 * usando o {@link https://api.wordpress.org/secret-key/1.1/salt/ WordPress.org
 * secret-key service}
 * Você pode alterá-las a qualquer momento para invalidar quaisquer
 * cookies existentes. Isto irá forçar todos os
 * usuários a fazerem login novamente.
 *
 * @since 2.6.0
 */
define( 'AUTH_KEY',         '@bQ^O4YG6[eYV:6uoN>fpm#$7_|72.dsV&vR^r{Htvvz8,A>C6kXvN,K`>BtH@RR' );
define( 'SECURE_AUTH_KEY',  'W24o)c[)5GgHIeFt_*IRx9zCqHB`;v@9O$~z^{IAimn :;Egu[AZwwc{w:_?fM=y' );
define( 'LOGGED_IN_KEY',    'BXc5{U]9FWb;pz=M`mGw-f!d{4[SqUuHQ;-5QN=,Rffl=~H[z3KEf7Ptf7Zw &H;' );
define( 'NONCE_KEY',        ',DGzX+`>sDO8*6!u@~*ujG6L#mXjCFO-G!M5*Xop9>Itw7f,#ByuY,>DUSfH~#Oj' );
define( 'AUTH_SALT',        ').@z~q$XBZd~O%YIy;eIa!mBeebwl+In?EIXci }>8W| Ht9J9DgJ2Zcx2Ei PMN' );
define( 'SECURE_AUTH_SALT', 'aNfH$,ld:-s&-gsqL!Sy(:4gTacFX`63x&j;tGOfvp.(R3w@k8t9yKFso$}(:_WC' );
define( 'LOGGED_IN_SALT',   '*;sLxl4ZF=aNw>J8F}JM8n59bq9ZQou^e(s]Qr<P1#My ;OD/Yj3$hCo%1jpr5@C' );
define( 'NONCE_SALT',       'ZL-k)hJK??U3iSQ2VI/fHH[gB`p%H7/DW_C36ej[7?R!7AYk+!4h?tfdk]tLn$Q$' );

/**#@-*/

/**
 * Prefixo da tabela do banco de dados do WordPress.
 *
 * Você pode ter várias instalações em um único banco de dados se você der
 * um prefixo único para cada um. Somente números, letras e sublinhados!
 */
$table_prefix = 'wp_';

/**
 * Para desenvolvedores: Modo de debug do WordPress.
 *
 * Altere isto para true para ativar a exibição de avisos
 * durante o desenvolvimento. É altamente recomendável que os
 * desenvolvedores de plugins e temas usem o WP_DEBUG
 * em seus ambientes de desenvolvimento.
 *
 * Para informações sobre outras constantes que podem ser utilizadas
 * para depuração, visite o Codex.
 *
 * @link https://codex.wordpress.org/pt-br:Depura%C3%A7%C3%A3o_no_WordPress
 */
define('WP_DEBUG', false);

/* Isto é tudo, pode parar de editar! :) */

/** Caminho absoluto para o diretório WordPress. */
if ( !defined('ABSPATH') )
	define('ABSPATH', dirname(__FILE__) . '/');

/** Configura as variáveis e arquivos do WordPress. */
require_once(ABSPATH . 'wp-settings.php');
