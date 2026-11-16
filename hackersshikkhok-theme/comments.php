<?php
/**
 * Master Cyberpunk Comments & Discussion Board Template - HackersShikkhok
 * Brand: HackersShikkhok (https://hackersshikkhok.com)
 */

declare(strict_types=1);

if ( post_password_required() ) {
    return;
}

$comm_count = get_comments_number();
?>

<section id="comments" class="hs-comments-area" style="margin-top:48px; padding-top:36px; border-top:1px solid rgba(0,245,212,0.25);">

    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:24px; flex-wrap:wrap; gap:12px;">
        <div style="display:flex; align-items:center; gap:10px;">
            <span style="font-size:22px;">💬</span>
            <h2 style="font-size:1.35rem; font-weight:800; color:#ffffff; margin:0;">
                Discussion &amp; Operative Feedback
            </h2>
            <span style="background:rgba(0,245,212,0.12); color:#00f5d4; border:1px solid rgba(0,245,212,0.3); font-size:12px; font-weight:800; padding:2px 10px; border-radius:999px; font-family:var(--font-mono);">
                <?php echo esc_html( number_format( $comm_count ) ); ?>
            </span>
        </div>
        <div style="font-size:12px; color:#94a3b8; font-family:var(--font-mono);">
            Community Guidelines Apply
        </div>
    </div>

    <!-- 1. Custom Cyber Comment Form -->
    <?php if ( comments_open() ) : ?>
        <div class="hs-comment-form-box" style="background:#070b16; border:1px solid rgba(255,255,255,0.08); border-radius:18px; padding:24px; margin-bottom:36px;">
            <?php
            $commenter = wp_get_current_commenter();
            $user_id   = get_current_user_id();

            comment_form( array(
                'title_reply'          => is_user_logged_in() ? 'Leave a Comment' : 'Join the Discussion',
                'title_reply_to'       => 'Reply to %s',
                'cancel_reply_link'    => 'Cancel Reply ✕',
                'title_reply_before'   => '<h3 style="color:#00f5d4; font-size:1.15rem; font-weight:800; margin:0 0 14px;">',
                'title_reply_after'    => '</h3>',
                'comment_notes_before' => '<p style="color:#94a3b8; font-size:13px; margin:0 0 16px;">Tip: Wrap code in backticks (`code`) for clean technical formatting.</p>',
                'comment_field'        => '<div style="margin-bottom:16px;">
                    <label for="comment" style="display:block; color:#cbd5e1; font-size:12px; font-weight:700; margin-bottom:6px;">Your Technical Comment / Question:</label>
                    <textarea id="comment" name="comment" cols="45" rows="4" required placeholder="Share your insight, questions, or solution..." style="width:100%; background:#050811; border:1px solid rgba(0,245,212,0.25); border-radius:12px; color:#ffffff; padding:12px 16px; font-size:14px; font-family:inherit; resize:vertical; box-sizing:border-box;"></textarea>
                </div>',
                'fields'               => array(
                    'author' => '<div style="margin-bottom:14px;">
                        <label for="author" style="display:block; color:#cbd5e1; font-size:12px; font-weight:700; margin-bottom:4px;">Display Name *</label>
                        <input id="author" name="author" type="text" value="' . esc_attr( $commenter['comment_author'] ) . '" required placeholder="Your name or handle" style="width:100%; background:#050811; border:1px solid rgba(255,255,255,0.1); border-radius:10px; color:#ffffff; padding:10px 14px; font-size:13px; box-sizing:border-box;" />
                    </div>',
                    'email'  => '<div style="margin-bottom:14px;">
                        <label for="email" style="display:block; color:#cbd5e1; font-size:12px; font-weight:700; margin-bottom:4px;">Email Address * (Will not be published)</label>
                        <input id="email" name="email" type="email" value="' . esc_attr( $commenter['comment_author_email'] ) . '" required placeholder="you@domain.com" style="width:100%; background:#050811; border:1px solid rgba(255,255,255,0.1); border-radius:10px; color:#ffffff; padding:10px 14px; font-size:13px; box-sizing:border-box;" />
                    </div>',
                ),
                'class_submit'         => 'hs-comment-submit-btn',
                'submit_button'        => '<button type="submit" id="%2$s" class="%3$s" style="padding:10px 24px; background:#00f5d4; color:#050811; font-weight:800; font-size:13px; border:none; border-radius:10px; cursor:pointer; box-shadow:0 0 16px rgba(0,245,212,0.35);">%4$s ➔</button>',
                'label_submit'         => 'Post Comment',
            ) );
            ?>
        </div>
    <?php else : ?>
        <p style="color:#94a3b8; font-size:13px; padding:16px; background:#070b16; border-radius:10px;">
            🔒 Comments on this publication are currently closed.
        </p>
    <?php endif; ?>

    <!-- 2. Threaded Comments List -->
    <?php if ( have_comments() ) : ?>
        <div class="hs-comments-list" style="display:flex; flex-direction:column; gap:16px;">
            <?php
            wp_list_comments( array(
                'style'       => 'div',
                'short_ping'  => true,
                'avatar_size' => 44,
                'callback'    => function( $comment, $args, $depth ) {
                    $tag = ( 'div' === $args['style'] ) ? 'div' : 'li';
                    $author_name = get_comment_author( $comment );
                    $comment_date = get_comment_date( 'M d, Y · H:i', $comment );
                    ?>
                    <<?php echo $tag; ?> id="comment-<?php comment_ID(); ?>" <?php comment_class( 'hs-comment-row', $comment ); ?> style="background:#070b16; border:1px solid rgba(255,255,255,0.06); border-radius:16px; padding:20px; margin-left:<?php echo esc_attr( (string) ( ( $depth - 1 ) * 24 ) ); ?>px; margin-bottom:12px;">
                        <div style="display:flex; justify-content:space-between; align-items:flex-start; margin-bottom:12px; gap:12px;">
                            <div style="display:flex; align-items:center; gap:12px;">
                                <div style="flex-shrink:0;">
                                    <?php echo get_avatar( $comment, 40, '', $author_name, array( 'class' => 'hs-comment-avatar', 'style' => 'width:38px; height:38px; border-radius:50%; border:1px solid #00f5d4; object-fit:cover;' ) ); ?>
                                </div>
                                <div>
                                    <div style="color:#ffffff; font-weight:800; font-size:14px;">
                                        <?php echo esc_html( $author_name ); ?>
                                    </div>
                                    <div style="color:#64748b; font-size:11px; font-family:var(--font-mono); margin-top:2px;">
                                        <?php echo esc_html( $comment_date ); ?>
                                    </div>
                                </div>
                            </div>

                            <div style="font-size:12px;">
                                <?php
                                comment_reply_link( array_merge( $args, array(
                                    'depth'     => $depth,
                                    'max_depth' => $args['max_depth'],
                                    'reply_text'=> '↩ Reply',
                                ) ) );
                                ?>
                            </div>
                        </div>

                        <div class="hs-comment-text" style="color:#cbd5e1; font-size:14px; line-height:1.7;">
                            <?php if ( '0' === $comment->comment_approved ) : ?>
                                <p style="color:#fbbf24; font-size:12px; font-style:italic;">Your technical comment is awaiting operative moderation.</p>
                            <?php endif; ?>
                            <?php comment_text(); ?>
                        </div>
                    </<?php echo $tag; ?>>
                    <?php
                },
            ) );
            ?>
        </div>

        <?php the_comments_navigation( array(
            'prev_text' => '← Older Comments',
            'next_text' => 'Newer Comments →',
        ) ); ?>

    <?php else : ?>
        <?php if ( comments_open() ) : ?>
            <div style="padding:28px; background:#070b16; border:1px dashed rgba(255,255,255,0.08); border-radius:14px; text-align:center; color:#94a3b8; font-size:14px;">
                Be the first to share an analysis or question on this topic!
            </div>
        <?php endif; ?>
    <?php endif; ?>

</section>
