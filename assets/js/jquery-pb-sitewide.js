/**
 * Add a negative letter spacing to Quick User Manager email customizer menus.
 */

jQuery( document ).ready(function(){
    jQuery('li a[href$="admin-email-customizer"]').css("letter-spacing", "-0.7px");
    jQuery('li a[href$="user-email-customizer"]').css("letter-spacing", "-0.7px");
});

/*
* Set the width of the shortcode input based on an element that
* has the width of its contents
*/
function setShortcodeInputWidth( $inputField ) {
    var tempSpan = document.createElement('span');
    tempSpan.className = "qum-shortcode-temp";
    tempSpan.innerHTML = $inputField.val();
    document.body.appendChild(tempSpan);
    var tempWidth = tempSpan.scrollWidth;
    document.body.removeChild(tempSpan);

    $inputField.outerWidth( tempWidth );
}

jQuery( document ).ready( function() {

    jQuery('.qum-shortcode.input').each( function() {
        setShortcodeInputWidth( jQuery(this) );
    });

    jQuery('.qum-shortcode.textarea').each( function() {
        jQuery(this).outerHeight( jQuery(this)[0].scrollHeight + parseInt( jQuery(this).css('border-top-width') ) * 2 );
    });

    jQuery('.qum-shortcode').click( function() {
        this.select();
    });
});


/* make sure that we don;t leave the page without having a title in the Post Title field, otherwise we loose data */
jQuery( function(){
    if( jQuery( 'body').hasClass('post-new-php') ){

        if( jQuery( 'body').hasClass('post-type-qum-rf-cpt') || jQuery( 'body').hasClass('post-type-qum-epf-cpt') || jQuery( 'body').hasClass('post-type-qum-ul-cpt') ){

            if( jQuery('#title').val() == '' ){
                jQuery(window).bind('beforeunload',function() {
                    return "This page is asking you to confirm that you want to leave - data you have entered may not be saved";
                });
            }

            /* remove beforeunload event when entering a title or pressing the puclish button */
            jQuery( '#title').keypress(function() {
                jQuery(window).unbind('beforeunload');
            });
            jQuery( '#publish').click( function() {
                jQuery(window).unbind('beforeunload');
            });
        }
    }
});


/* show hide fields based on selected options */
jQuery( function(){
    jQuery( '#qum-rf-settings-args').on('change', '#redirect', function(){
        if( jQuery(this).val() == 'Yes' ){
            jQuery( '.row-url, .row-display-messages', jQuery(this).parent().parent().parent()).show();
        }
        else{
            jQuery( '.row-url, .row-display-messages', jQuery(this).parent().parent().parent()).hide();
        }
    });

    jQuery( '#qum-epf-settings-args').on('change', '#redirect', function(){
        if( jQuery(this).val() == 'Yes' ){
            jQuery( '.row-url, .row-display-messages', jQuery(this).parent().parent().parent()).show();
        }
        else{
            jQuery( '.row-url, .row-display-messages', jQuery(this).parent().parent().parent()).hide();
        }
    });


    jQuery( '#qum-ul-settings-args').on('click', '#visible-only-to-logged-in-users_yes', function(){
        jQuery( '.row-visible-to-following-roles', jQuery(this).parent().parent().parent().parent().parent().parent()).toggle();
    });
});

/*
* Dialog boxes throughout Quick User Manager
*/
jQuery( function() {
    jQuery(document).ready( function() {
        jQuery('.qum-modal-box').dialog({
            autoOpen: false,
            modal: true,
            draggable: false,
            minWidth: 450,
            minHeight: 450
        });

        jQuery('.qum-open-modal-box').click( function(e) {
            e.preventDefault();
            jQuery('#' + jQuery(this).attr('href')).dialog('open');
        });
    });
});
