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
        
        $.ajax({
            type: 'POST',
            url: ajaxUrl,
            dataType: 'html',
            data: {
                action: 'filter_posts',
                taxonomy: $this.data('slug'),
                post_type: $this.data('post_type'),
                taxonomy_type: $this.data('taxonomy_type')
            },
            success: function(response) {
                $('.portfolio-grid').html(response);
                $loader.hide();
            },
            error: function (jqXHR, textStatus, errorThrown) {
                console.log(errorThrown);
            }
        });
    });
});