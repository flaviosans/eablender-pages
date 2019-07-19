<?php

/**
 * Plugin Name: EABlender - Pages
 * Description: Páginas de profissionais - A ponte entre o seu blog e o Entenda Antes!
 * Version: 1.0.0
 * Author: Flávio Santos, Gleydson Parpinelli, Jonas Gabriel
 */

class EABlender_Pages {

    private $api;

	function __construct(){
        $this->eablender_pages_init();

		add_action('init',              array($this, 'eablender_pages_rewrite_rules' ));
		add_filter('query_vars',        array($this, 'eablender_pages_add_querystring_vars' ));
		add_filter('the_content',       array($this, 'eablender_generate_pages' ));
	}

	private function eablender_pages_init(){
		if ( class_exists( 'EABlender_API' ) ) {
			$this->api = EABlender_API::get_instance();
		} else {
			add_action( 'admin_notices', array($this, function(){
            ?>
                <div class="notice notice-warning">
                    <p><?php _e( 'EABlender Pages: EABlender API parece não estar presente' ); ?></p>
                </div>
            <?php
			}));
		}
    }

	/**
	 * Filtra uma postagem, substituindo as variáveis por atributos da cidade
	 *
	 * @param $content
	 *
	 * @return mixed $content
	 */
	public function eablender_generate_pages($content){

		if(get_query_var('eablender_pages_user_app')){

			$response = $this->api->get_user_app(get_query_var('eablender_pages_user_app'));

			if($response->status_code = 200){

			    $content = $this->eablender_pages_replace_vars($response->content, $content);
            }

			return $content;
        }
    }

	/**
	 * Adiciona as urls personalizadas das páginas do APP
	 */
	function eablender_pages_rewrite_rules() {

		add_rewrite_rule(
		        '^site\/(.*)',
                'index.php?page_id=19&eablender_pages_user_app=$matches[1]',
                'top'
        );

		flush_rewrite_rules();
	}

	/**
     * Adiciona as querystrings necessárias para a página
     *
	 * @param $vars
	 *
	 * @return array
	 */
	public function eablender_pages_add_querystring_vars($vars){
		$vars[] = 'eablender_pages_user_app';
		return $vars;
    }

    private function eablender_pages_replace_vars($obj, $post_content){

	    $var_name  = array(
            '%ID%',
            '%NAME%',
            '%USERNAME%',
            '%EMAIL%',
            '%PHONE%',
            '%DESCRIPTION%'
        );

        $var_value = array(
            $obj->id,
            $obj->name,
            $obj->username,
            $obj->email,
            $obj->phone,
            $obj->userAppDetails->description
        );

        return str_replace($var_name, $var_value, $post_content);
    }
}

$eablender_pages = new EABlender_Pages;