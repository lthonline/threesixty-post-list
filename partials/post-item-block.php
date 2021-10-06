<?php
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */



?>
<div class="col-md-4 grid-col">
    <div class="card">
        <?php 
            $allFieldsObject = get_field_objects(get_the_ID());
            
            $post_title = get_the_title(get_the_ID());
            
            $tour_field_name = 'virtual_tour_link';
            $virtual_tour_url = null;
            if($allFieldsObject != null && array_key_exists($tour_field_name, $allFieldsObject)) {
                $virtual_tour_url = $allFieldsObject[$tour_field_name]['value'];
            }            
            $thumbnail_url = get_the_post_thumbnail_url(get_the_ID(),'full');
            echo Threesixty_Post_List_Renderer::render_post_image($thumbnail_url, $post_title, $virtual_tour_url, true);
        ?>
        
        <div class="card-body text-center">
            <?php echo Threesixty_Post_List_Renderer::render_post_title($post_title, '<h2>', $virtual_tour_url, 'card-title', true); ?>
            <?php  echo Threesixty_Post_List_Renderer::render_post_content($allFieldsObject); ?>
        </div>
    </div>
</div>
