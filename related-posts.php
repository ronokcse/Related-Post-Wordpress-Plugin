<?php

/**
 * Plugin Name: Related Posts
 * Description: This Plugin shows the related category post of users. 
 * Version: 1.0.0
 * Author: Mamuduzzaman Ronok
 * Author URI: http://google.com
 * Plugin URI: http://google.com
 */

 if(!defined('ABSPATH')) exit;

 class RelatedPost{
    public function __construct()
    {
        add_action('init',array($this,'init'));
    }

    public function init(){
        // This Hook is use to manupulate the content of post
        add_filter( 'the_content', array($this,'rp_related_post') );
        //load the boostsrap scripts using wp_enqueue_scripts hook
        add_action('wp_enqueue_scripts', array($this, 'rp_enqueue_bootstrap'));
    }
    /**
     * Enqueue Bootstrap CSS
     */
    public function rp_enqueue_bootstrap() {
        // Enqueue Bootstrap CSS from CDN
        wp_enqueue_style('bootstrap', 'https://cdn.jsdelivr.net/npm/bootstrap@4.4.1/dist/css/bootstrap.min.css', array(), '5.0.0');
    }

    /**
     * Function the show the related Category post under the post content
     */
    public function rp_related_post($content){
        $current_post_id = get_the_ID(); // get the id of current post
        $current_post_categories = get_the_category($current_post_id);// get the category list of current post
        $category_ids = array(); // Array to store category IDs
        if(!empty($current_post_categories)){ // To check if the category exist or not
        // Push  category IDs
            foreach ($current_post_categories as $category) {
                $category_ids[] = $category->term_id;
            }
        }
        ob_start();
        $args = array(
            'post__not_in' => array($current_post_id),//Remove the existing post from the list
            'post_type' => 'post',
            'post_status' => 'publish', //Only show to publish post list
            'posts_per_page' => 5, // Show 5 post list 
            'orderby' => 'rand', // show random order
            'category__in' => $category_ids // related category

        );
        $query = new WP_Query($args); // store the WP_query Class object  
        if($query->have_posts())  // check if there is any post 
        {

            $content .= '<h1>Related Posts</h1>'; 
            $content .= '<ul class="list-group">';
            
            while ($query->have_posts()) {
                $query->the_post();
                $title = esc_html(get_the_title()); //to escape the post title
                $permalink = esc_url(get_permalink());//to escape the URL
                $content .= '<li class="list-group-item">';
                $content .= '<a href="' . $permalink . '">' . $title . '</a>';
                $content .= '</li>';
            }
            
            $content .= '</ul>'; 
            
            wp_reset_postdata(); // Reset post data 

            return $content; // Return the final content
        }
        else{ //no related post there
            $content = $content. '<h1>Related Post </h1>';
            $content = $content.'<p>There Is No Related Post There</p>';
            return $content;  //return the content
        }
    }
 }
 new RelatedPost();
