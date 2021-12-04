<?php
add_filter( 'the_content', 'like_it_button_html', 99 );
 
function like_it_button_html( $content ) {
    $like_text = '';
    if ( is_single() ) {
        $nonce = wp_create_nonce( 'pt_like_it_nonce' );
        $link = admin_url('admin-ajax.php?action=pt_like_it&post_id='.$post->ID.'&nonce='.$nonce);
        $likes = get_post_meta( get_the_ID(), '_pt_likes', true );
        $likes = ( empty( $likes ) ) ? 0 : $likes;
        $like_text = '
                    <div class="pt-like-it">
                        <a class="like-button" href="'.$link.'" data-id="' . get_the_ID() . '" data-nonce="' . $nonce . '">' . 
                        __( 'Like it' ) .
                        '</a>
                        <span id="like-count-'.get_the_ID().'" class="like-count">' . $likes . '</span>
                    </div>';
    }
    return $content . $like_text;
}