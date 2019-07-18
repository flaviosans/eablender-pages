<?php

/**
 * Plugin Name: EABlender - Pages
 * Description: Páginas de profissionais - A ponte entre o seu blog e o Entenda Antes!
 * Version: 1.0.0
 * Author: Flávio Santos, Gleydson Parpinelli, Jonas Gabriel
 */

class EABlender_Pages {
	function __construct(){
        $this->init();

//		add_filter( 'page_template',    array($this, 'eablender_pages_load_custom_html' ) );
		add_action('template_redirect', array($this, 'eablender_pages_show_page'));
		add_filter('the_content',       array($this, 'eablender_pages_concatenate'));
		add_action('init',              array($this, 'eablender_pages_rewrite_rules' ));
	}

	private function init(){
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

    public function eablender_pages_show_page(){
	    if(strpos($_SERVER['REQUEST_URI'], 'encontre')){
	        $path = explode("-", $_SERVER['REQUEST_URI']);
            $professional = null;
            if(!is_404()) {
                return;
            }
        }

	    return;
    }

	/**
     * Carrega uma página customizada, baseada num Slug
     *
	 * @param $page_template
	 *
	 * @return string
	 */
	public function eablender_pages_load_custom_html( $page_template ){
		if ( is_page( 'my-custom-page-slug' ) ) {
			$page_template = plugin_dir_path(__FILE__) . 'page-professional.php';
		}
		return $page_template;
	}

	/**
	 * Filtra uma postagem, substituindo as variáveis por atributos da cidade
	 *
	 * @param $content
	 *
	 * @return mixed $content
	 */
	public function eablender_pages_concatenate($content){
		$uri = $_SERVER['REQUEST_URI'];
		$city = "Maringá";

	    return str_replace('%CIDADE%', $city, $content);
    }

	function eablender_pages_rewrite_rules() {
		$page_id = 9;
		add_rewrite_rule('^encontre-[oa]s-melhores-arquitet[oa]s-em.*-[a-z]{2}', 'index.php?page_id=' . $page_id, 'top');
	}
}

$eablender_pages = new EABlender_Pages;