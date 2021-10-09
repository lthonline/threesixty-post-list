jQuery.noConflict();
jQuery( document ).ready(function($) {
    
    var ajaxUrl = location.protocol + '//' + location.host + '/wp-admin/admin-ajax.php';
    
    var $loader = $('.list-loader');
    $loader.hide();
    
    $('.tax-nav-link').on('click', function(e) {
        var $this = $(this);
                
        $('.tax-nav-item').removeClass('active');
        $this.parent().addClass('active');
        
        $loader.show();
        
        var data = {
            action: 'filter_posts',
            taxonomy: $this.data('slug'),
            post_type: $this.data('post_type'),
            taxonomy_type: $this.data('taxonomy_type')
        }
        
        executeAjaxFilter(ajaxUrl, data, $loader);
        
    });
    
    $('#CategoryFilterOptions').on('change', function(e) {
        e.preventDefault();
        var $this = $(this);
        var data = {
            action: 'filter_posts',
            taxonomy: $this.find(':selected').data('slug'),
            post_type: $this.find(':selected').data('post_type'),
            taxonomy_type: $this.find(':selected').data('taxonomy_type')
        }
        
        executeAjaxFilter(ajaxUrl, data, $loader)
    })
});

function executeAjaxFilter(ajaxUrl, data, $loader) {
    
    if(data == null || data == '') {
        return
    }
        
    jQuery.ajax({
        type: 'POST',
        url: ajaxUrl,
        dataType: 'html',
        data: data,
        success: function (response) {
            jQuery('.portfolio-grid').html(response);
            $loader.hide();
        },
        error: function (jqXHR, textStatus, errorThrown) {
            jQuery('.portfolio-grid').html('<p>Portfolio for this category not found. Please try again later.</p>');
        }
    });
}