<?php

class Threesixty_Post_List_Renderer {
    
    public static function render_post_content($allFieldsObject) {
        
        if($allFieldsObject == null) {
            return;
        }
        
        $output = '';
        
        if ($allFieldsObject != null) {
            foreach ($allFieldsObject as $key => $value) {

                if ($value['value']) {

                    $field_value = $value['value'];
                    $field_type = $value['type'];

                    if ($field_type == 'url') {
                        if (strpos($field_value, 'facebook')) {
                            $output .= self::render_link_field_content($field_value, '<span class="dashicons dashicons-facebook"></span>', 'icon-link facebook-link-icon');
                        } else if (strpos($field_value, 'youtube')) {
                            $output .= self::render_link_field_content($field_value, '<span class="dashicons dashicons-youtube"></span>', 'icon-link youtube-link-icon');
                        } else if (strpos($field_value, 'instagram')) {
                            $output .= self::render_link_field_content($field_value, '<span class="dashicons dashicons-instagram"></span>', 'icon-link instagram-link-icon');
                        } else if ($key == 'virtual_tour_link') {
                            $output .= self::render_link_field_content($field_value, '<span class="dashicons dashicons-video-alt"></span>', 'icon-link virtualtour-link-icon');
                        } 
                        else {
                            $output .= self::render_link_field_content($field_value, null, 'icon-link instagram-link-icon');
                        }
                    }

                    if ($field_type == 'text') {
                        $output .= self::render_text_field_content($field_value, '<p>', 'my-text');
                    }
                }
            }
        }
        
        return $output;
    }
    
    /**
     * Render post title
     *
     * @since 1.0.0
     * @param string $title - post title text
     * @param string $wrapper_element - use html element for wrap title e.g. '<div>, <h1> ...'
     * @param string $title_link
     * @param string $custom_classes
     */
    public static function render_post_title($title, $wrapper_element = null, $title_link = null, $custom_classes = null) {
        
        $heading_levels = array(
            '<h1>' => array('start_tag' => '<h1 class="'. $custom_classes .'">', 'end_tag' => '</h1>'),
            '<h2>' => array('start_tag' => '<h2 class="'. $custom_classes .'">', 'end_tag' => '</h2>'),
            '<h3>' => array('start_tag' => '<h3 class="'. $custom_classes .'">', 'end_tag' => '</h3>'),
            '<h4>' => array('start_tag' => '<h4 class="'. $custom_classes .'">', 'end_tag' => '</h4>'),
            '<h5>' => array('start_tag' => '<h5 class="'. $custom_classes .'">', 'end_tag' => '</h5>'),
            '<h6>' => array('start_tag' => '<h6 class="'. $custom_classes .'">', 'end_tag' => '</h6>'),
            '<div>' => array('start_tag' => '<div class="'. $custom_classes .'">', 'end_tag' => '</div>'),
            '<p>' => array('start_tag' => '<p class="'. $custom_classes .'">', 'end_tag' => '</p>'),
            '<span>' => array('start_tag' => '<span class="'. $custom_classes .'">', 'end_tag' => '</span>'),
        );
        
        $output = '';
        
        $post_title = $title;
        
        if($wrapper_element != null) {
            if(array_key_exists($wrapper_element, $heading_levels)) {
            
                $start_tag = $heading_levels[$wrapper_element]['start_tag'];
                $end_tag = $heading_levels[$wrapper_element]['end_tag'];            

                if($title_link != null) {                    
                    $post_title = $start_tag . '<a href="' . $title_link . '">'. $post_title .'</a>' . $end_tag;
                } else {
                    $post_title = $start_tag . $title . $end_tag;
                }
            }
            
        } else {
            
            if($title_link != null) {                    
                $post_title = '<a href="' . $title_link . '">'. $post_title .'</a>';
            } 
            
        }
        
            
        $output .= $post_title;
        
        return $output;
    }

    public static function render_link_field_content ($link_url, $link_text = null, $link_class = null) {
        $output = '';
            if ($link_text != null) {
                $output .= '<a class="' . $link_class . '" href="' . $link_url . '">' . $link_text . '</a>';
            } else {
                $output .= '<a class="' . $link_class . '" href="' . $link_url . '">' . $link_url . '</a>';
            }
        return $output;
    }


    public static function render_text_field_content($text_value, $text_wrapper_tag = null, $text_class = null) {
    
        $output = '';
        
        if($text_wrapper_tag != null && $text_wrapper_tag == '<p>') {
            $output .= '<p class="'. $text_class .'">' . $text_value . '</p>';
        }
        
        if($text_wrapper_tag != null && $text_wrapper_tag == '<div>') {
            $output .= '<div class="'. $text_class .'">' . $text_value . '</div>';
        }
        
        return $output;
    }
    
    public static function render_post_image($image_url, $alt_text = '', $image_link = null) {
        $output = '';
        if($image_link != null) {
            $output .= '<a href="'.$image_link.'">';
            $output .= '<img src="'. $image_url .'" class="card-img-top" alt="'. $alt_text .'">';
            $output .= '</a>';            
        } else {
            $output .= '<img src="'. $image_url .'" class="card-img-top" alt="'. $alt_text .'">';       
        }
        
        return $output;
    }
    
}