<?php
/*
Plugin Name: List All Posts
Plugin URI:  https://sandbox.maridoedani.com/plugin-listar-todos-os-posts/
Description: Plugin para listar todos os posts de blogs da rede de blogs
Version: 1.0
Author: Marco Aurélio Lima Fernandes
Author URI: https://sandbox.maridoedani.com
License: Marido e Dani V1
*/
add_action('widgets_init', create_function('', 'return register_widget("ListAllPosts");'));
class ListAllPosts extends WP_Widget {  
    public function ListAllPosts(){
        parent::WP_Widget(false, $name = 'List All Posts');
    }
    /**
    * Exibição final do Widget (já no sidebar)
    *
    * @param array $argumentos Argumentos passados para o widget
    * @param array $instancia Instância do widget
    */
    public function widget($argumentos, $instancia) {
        global $wpdb;
	$idBlog = get_current_blog_id();
        $args = array(
            'network_id' => 1,
            'public'     => null,
            'archived'   => null,
            'mature'     => null,
            'spam'       => null,
            'deleted'    => null,
            'limit'      => 100,
            'offset'     => 0,
        );
        echo $argumentos['before_widget'];
        echo $argumentos['before_title'] . (($instancia['title_widget'])?$instancia['title_widget'] : _e('Lista de blogs')) . $argumentos['after_title'];
        echo '<dl class="'.$instancia['class_widtget'].'">';
        
        $blog_list = wp_get_sites( $args );
        $arrPost = array();
        foreach ($blog_list as $blog){
            

            switch_to_blog($blog['blog_id']);

            $args = array( 'posts_per_page' => $instancia['qtd_post'],'order'=> 'DESC', 'orderby' => 'date','post_status' => 'publish');
            $posts  = get_posts($args);

            foreach($posts as $post){
                $arrPost[] = array('post' =>$post->ID,'blog'=>$blog['blog_id']);
            }
                        
        }
        shuffle($arrPost);
        foreach($arrPost as $post){
            switch_to_blog($post['blog']);
            echo '<div style="margin-bottom:10px;"><p>'. get_the_post_thumbnail( $post['post'], 'medium').'</p>';
            echo '<h4 class="post-title"><a rel="nofollow" href="'.get_permalink( $post['post']).'" title="'.get_the_title($post['post']).'">'.get_the_title($post['post']).'</a></h4></div>';
            
        }
        
        
        echo '</dl>';
        echo $argumentos['after_widget'];
        switch_to_blog($idBlog );
        //restore_current_blog();
    }
    public function update($nova_instancia, $instancia_antiga) {            
        $instancia = array_merge($instancia_antiga, $nova_instancia);
     
        return $instancia;
    }
    public function form($instancia) {  
        $widget['title_widget'] = $instancia['title_widget'];
        $widget['qtd_post'] = $instancia['qtd_post'];
         ?>
<p><label for="<?php echo $this->get_field_id('title_widget'); ?>"><strong><?php _e('Título'); ?>:</strong></label><br /><input id="<?php echo $this->get_field_id('title_widget'); ?>" name="<?php echo $this->get_field_name('title_widget'); ?>" type="text" value="<?php echo $widget['title_widget'] ?>" /></p>
<p><label for="<?php echo $this->get_field_id('qtd_post'); ?>"><strong><?php _e('Quantidade de post para cada blog'); ?>:</strong></label> <br /><input id="<?php echo $this->get_field_id('qtd_post'); ?>" name="<?php echo $this->get_field_name('qtd_post'); ?>" type="text" value="<?php echo $widget['qtd_post'] ?>" /></p>
        <?php   



    }
}