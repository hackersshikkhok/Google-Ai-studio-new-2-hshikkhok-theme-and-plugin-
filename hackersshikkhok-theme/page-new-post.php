<?php
/**
 * Template Name: WordPress Admin Classic Editor - Add New Post
 * Description: Production Content Editor wired to WordPress REST API & Media Library
 * Package: HackersShikkhok Theme
 * Brand: HackersShikkhok (https://hackersshikkhok.com)
 */

declare(strict_types=1);

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

get_header();

$current_user_id = get_current_user_id();
$is_logged_in    = is_user_logged_in();
$rest_nonce      = wp_create_nonce( 'wp_rest' );
$edit_post_id    = isset( $_GET['edit'] ) ? (int) $_GET['edit'] : 0;
$editing_post    = null;

if ( $edit_post_id > 0 && $is_logged_in ) {
    $candidate = get_post( $edit_post_id );
    if ( $candidate && ( (int) $candidate->post_author === $current_user_id || current_user_can( 'edit_others_posts' ) ) ) {
        $editing_post = $candidate;
    } else {
        $edit_post_id = 0;
    }
}

$init_title   = $editing_post ? $editing_post->post_title : '';
$init_slug    = $editing_post ? $editing_post->post_name : '';
$init_content = $editing_post ? $editing_post->post_content : '';
$init_excerpt = $editing_post ? $editing_post->post_excerpt : '';
$init_type    = $editing_post ? $editing_post->post_type : 'tutorials';
$init_status  = $editing_post ? $editing_post->post_status : 'draft';
$init_format  = $editing_post ? ( (string) get_post_meta( $edit_post_id, '_hs_post_format', true ) ?: 'standard' ) : 'standard';
$init_kw      = $editing_post ? (string) get_post_meta( $edit_post_id, '_hs_focus_keyword', true ) : '';
$init_thumb_id = $editing_post ? (int) get_post_thumbnail_id( $edit_post_id ) : 0;
$init_thumb_url = $init_thumb_id > 0 ? (string) wp_get_attachment_url( $init_thumb_id ) : '';
$init_cat_ids = $editing_post ? wp_get_post_categories( $edit_post_id ) : array();
$init_tags    = $editing_post ? wp_get_post_tags( $edit_post_id, array( 'fields' => 'names' ) ) : array();

$wp_categories = get_categories( array( 'hide_empty' => false, 'orderby' => 'name', 'order' => 'ASC' ) );
$fallback_cats = array(
    array( 'id' => 'cybersecurity', 'name' => 'Cyber Security & Ethical Hacking' ),
    array( 'id' => 'web-dev',       'name' => 'Full-Stack Web Development' ),
    array( 'id' => 'programming',   'name' => 'Programming & Scripts' ),
    array( 'id' => 'ai-ml',         'name' => 'AI & Machine Learning' ),
    array( 'id' => 'device-reviews','name' => 'Gadget & Hardware Reviews' ),
    array( 'id' => 'tech-news',     'name' => 'Global Technology News' ),
);
?>

<style>
.hs-editor-grid {
    display: grid;
    grid-template-columns: 1fr 340px;
    gap: 24px;
    align-items: start;
}
.hs-editor-meta-2col {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 14px;
    font-size: 12px;
}
.hs-mce-btn {
    background: #101827;
    border: 1px solid rgba(255,255,255,0.12);
    color: #E8EEF7;
    font-size: 12px;
    padding: 5px 9px;
    border-radius: 5px;
    cursor: pointer;
}
.hs-mce-btn:hover {
    border-color: #20DCC5;
    color: #20DCC5;
}
.hs-publish-btn {
    background: #20DCC5;
    color: #080D17;
    border: none;
    font-weight: 800;
    font-size: 13px;
    padding: 9px 18px;
    border-radius: 6px;
    cursor: pointer;
}
.hs-publish-btn:hover {
    filter: brightness(1.08);
}
@media (max-width: 900px) {
    .hs-editor-grid {
        grid-template-columns: 1fr;
    }
    .hs-editor-meta-2col {
        grid-template-columns: 1fr;
    }
}
</style>

<div class="hs-wp-classic-editor-wrapper" style="background:#080D17; color:#E8EEF7; min-height:100vh; padding:24px 16px 90px;">
    <div style="max-width:1380px; margin:0 auto;">

        <!-- Header Strip -->
        <div style="display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:12px; margin-bottom:16px; border-bottom:1px solid rgba(255,255,255,0.08); padding-bottom:12px;">
            <div style="display:flex; align-items:center; gap:12px; flex-wrap:wrap;">
                <h1 style="font-size:22px; font-weight:800; color:#E8EEF7; margin:0; display:flex; align-items:center; gap:8px;">
                    <span style="color:#20DCC5;">✍️</span> <?php echo $edit_post_id > 0 ? 'Edit Article' : 'Add New Post'; ?>
                </h1>
                <span style="font-size:11px; background:rgba(32,220,197,0.12); color:#20DCC5; border:1px solid rgba(32,220,197,0.3); padding:2px 8px; border-radius:4px; font-family:var(--font-mono);">
                    Creator Studio Editor
                </span>
            </div>
            <div style="display:flex; align-items:center; gap:10px; flex-wrap:wrap;">
                <a href="<?php echo esc_url( home_url( '/dashboard/?tab=posts' ) ); ?>" style="font-size:12px; color:#E8EEF7; text-decoration:none; padding:6px 12px; background:#101827; border-radius:6px; border:1px solid rgba(255,255,255,0.12);">
                    ← Return to Dashboard
                </a>
                <span style="font-size:12px; color:#10b981; font-weight:700;">
                    Reward: +5.00 BDT &amp; +100 XP
                </span>
            </div>
        </div>

        <?php if ( ! $is_logged_in ) : ?>
            <div style="background:#101827; border:1px solid rgba(234,191,87,0.4); border-radius:12px; padding:18px 20px; margin-bottom:20px; display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:12px;">
                <div>
                    <strong style="color:#EABF57; font-size:14px;">Sign in required to publish</strong>
                    <p style="color:#929FB2; font-size:13px; margin:4px 0 0;">Please sign in or register an account to save drafts, upload images, and earn publishing rewards.</p>
                </div>
                <a href="<?php echo esc_url( wp_login_url( home_url( '/new-post/' ) ) ); ?>" style="padding:9px 18px; background:#20DCC5; color:#080D17; font-weight:800; border-radius:8px; text-decoration:none; font-size:13px;">Sign In to Write ➔</a>
            </div>
        <?php endif; ?>

        <!-- Alert Notice Box -->
        <div id="hs-editor-alert" style="display:none; padding:12px 16px; border-radius:8px; margin-bottom:18px; font-size:13px; font-weight:600;"></div>

        <!-- Two Column Post Screen -->
        <div class="hs-editor-grid">

            <!-- LEFT MAIN COLUMN -->
            <div class="hs-editor-main-col" style="display:flex; flex-direction:column; gap:20px; min-width:0;">

                <!-- Post Title Input -->
                <div style="background:#101827; border:1px solid rgba(255,255,255,0.12); border-radius:10px; padding:14px;">
                    <input type="text" id="hs-classic-title" value="<?php echo esc_attr( $init_title ); ?>" placeholder="Enter post title here..."
                           style="width:100%; box-sizing:border-box; background:#080D17; border:1px solid rgba(32,220,197,0.35); color:#E8EEF7; font-size:18px; font-weight:700; padding:12px 16px; border-radius:8px; outline:none;" />
                    <div style="font-size:11px; color:#929FB2; margin-top:8px; font-family:var(--font-mono); display:flex; align-items:center; gap:6px; flex-wrap:wrap;">
                        <span>Slug:</span>
                        <span style="color:#20DCC5;"><?php echo esc_url( home_url( '/' ) ); ?></span>
                        <input type="text" id="hs-classic-slug" value="<?php echo esc_attr( $init_slug ); ?>" placeholder="post-slug" style="background:#080D17; border:1px solid rgba(255,255,255,0.12); color:#38bdf8; font-size:11px; padding:3px 8px; border-radius:4px; min-width:160px;" />
                    </div>
                </div>

                <!-- Add Media & Editor Controls -->
                <div style="background:#101827; border:1px solid rgba(255,255,255,0.12); border-radius:10px; overflow:hidden;">

                    <div style="display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:8px; padding:10px 14px; background:#172435; border-bottom:1px solid rgba(255,255,255,0.08);">
                        <div style="display:flex; align-items:center; gap:8px;">
                            <input type="file" id="hs-inline-media-file" accept="image/*" style="display:none;" />
                            <button type="button" id="hs-btn-add-media" style="background:#080D17; border:1px solid rgba(255,255,255,0.18); color:#E8EEF7; font-size:12px; font-weight:700; padding:6px 14px; border-radius:6px; display:inline-flex; align-items:center; gap:6px; cursor:pointer;">
                                <span>📷</span>
                                <span id="hs-media-btn-label">Add Media</span>
                            </button>
                        </div>
                        <div style="display:flex; gap:4px;">
                            <button type="button" class="hs-editor-mode-tab active" data-mode="visual" style="background:#080D17; border:1px solid rgba(32,220,197,0.4); color:#20DCC5; font-size:12px; font-weight:700; padding:6px 14px; border-radius:6px; cursor:pointer;">
                                Visual
                            </button>
                            <button type="button" class="hs-editor-mode-tab" data-mode="text" style="background:#101827; border:1px solid rgba(255,255,255,0.12); color:#929FB2; font-size:12px; font-weight:700; padding:6px 14px; border-radius:6px; cursor:pointer;">
                                Text (HTML)
                            </button>
                        </div>
                    </div>

                    <!-- Formatting Toolbar Row 1 -->
                    <div style="display:flex; flex-wrap:wrap; gap:4px; padding:8px 12px; background:#0d1524; border-bottom:1px solid rgba(255,255,255,0.06);">
                        <button type="button" class="hs-mce-btn" data-cmd="bold" title="Bold"><b>B</b></button>
                        <button type="button" class="hs-mce-btn" data-cmd="italic" title="Italic"><i>I</i></button>
                        <button type="button" class="hs-mce-btn" data-cmd="underline" title="Underline"><u>U</u></button>
                        <button type="button" class="hs-mce-btn" data-cmd="strikeThrough" title="Strikethrough"><s>S</s></button>
                        <button type="button" class="hs-mce-btn" data-cmd="insertUnorderedList" title="Bulleted list">• List</button>
                        <button type="button" class="hs-mce-btn" data-cmd="insertOrderedList" title="Numbered list">1. List</button>
                        <button type="button" class="hs-mce-btn" data-cmd="formatBlock" data-val="blockquote" title="Blockquote">❝ Quote</button>
                        <button type="button" class="hs-mce-btn" data-cmd="justifyLeft" title="Align left">Left</button>
                        <button type="button" class="hs-mce-btn" data-cmd="justifyCenter" title="Align center">Center</button>
                        <button type="button" class="hs-mce-btn" id="hs-cmd-link" title="Insert link">🔗 Link</button>
                        <button type="button" class="hs-mce-btn" data-cmd="unlink" title="Remove link">Unlink</button>
                        <button type="button" class="hs-mce-btn" id="hs-cmd-code" title="Insert Code Block">&lt;/&gt; Code</button>
                        <select id="hs-format-dropdown" style="background:#101827; border:1px solid rgba(255,255,255,0.15); color:#E8EEF7; font-size:11px; padding:4px 8px; border-radius:4px;">
                            <option value="p">Paragraph</option>
                            <option value="h2">Heading 2</option>
                            <option value="h3">Heading 3</option>
                            <option value="h4">Heading 4</option>
                            <option value="pre">Preformatted Code</option>
                        </select>
                        <button type="button" class="hs-mce-btn" data-cmd="undo" title="Undo">↩</button>
                        <button type="button" class="hs-mce-btn" data-cmd="redo" title="Redo">↪</button>
                    </div>

                    <!-- Editable Visual Area -->
                    <div id="hs-visual-canvas" contenteditable="true"
                         style="min-height:360px; padding:18px; background:#080D17; color:#E8EEF7; font-size:14px; line-height:1.75; outline:none; border:none; overflow-y:auto;">
                        <?php echo $init_content ? wp_kses_post( $init_content ) : '<p>Start writing your technical tutorial, cybersecurity research, or engineering documentation...</p>'; ?>
                    </div>

                    <!-- Raw Text / HTML Canvas -->
                    <textarea id="hs-raw-canvas" style="display:none; width:100%; box-sizing:border-box; min-height:360px; padding:18px; background:#050811; color:#20DCC5; font-family:var(--font-mono); font-size:13px; line-height:1.6; border:none; outline:none; resize:vertical;"></textarea>

                    <!-- Status Bar -->
                    <div style="display:flex; justify-content:space-between; align-items:center; padding:8px 14px; background:#172435; border-top:1px solid rgba(255,255,255,0.08); font-size:11px; color:#929FB2; font-family:var(--font-mono);">
                        <span id="hs-word-count">Word count: 0</span>
                        <span>Autosave ready</span>
                    </div>
                </div>

                <!-- Excerpt Meta Box -->
                <div style="background:#101827; border:1px solid rgba(255,255,255,0.12); border-radius:10px; padding:16px;">
                    <h3 style="font-size:13px; font-weight:700; color:#E8EEF7; margin:0 0 8px; text-transform:uppercase; letter-spacing:0.5px;">
                        Post Excerpt
                    </h3>
                    <textarea id="hs-classic-excerpt" rows="3" placeholder="Write a short summary (1-2 sentences) of this article for search snippets and feeds..."
                              style="width:100%; box-sizing:border-box; background:#080D17; border:1px solid rgba(255,255,255,0.12); color:#E8EEF7; padding:10px; border-radius:6px; font-size:13px;"><?php echo esc_textarea( $init_excerpt ); ?></textarea>
                </div>

                <!-- SEO & Post Type Meta Box -->
                <div style="background:#101827; border:1px solid rgba(32,220,197,0.3); border-radius:10px; padding:16px;">
                    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:12px;">
                        <h3 style="font-size:13px; font-weight:700; color:#20DCC5; margin:0; text-transform:uppercase;">
                            🛡️ SEO &amp; Classification
                        </h3>
                    </div>
                    <div class="hs-editor-meta-2col">
                        <div>
                            <label style="display:block; color:#929FB2; margin-bottom:4px; font-weight:600;">Focus Keyword:</label>
                            <input type="text" id="hs-meta-keyword" value="<?php echo esc_attr( $init_kw ); ?>" placeholder="e.g. linux hardening, owasp" style="width:100%; box-sizing:border-box; background:#080D17; border:1px solid rgba(255,255,255,0.12); color:#E8EEF7; padding:8px 10px; border-radius:6px;" />
                        </div>
                        <div>
                            <label style="display:block; color:#929FB2; margin-bottom:4px; font-weight:600;">Post Type:</label>
                            <select id="hs-meta-type" style="width:100%; box-sizing:border-box; background:#080D17; border:1px solid rgba(255,255,255,0.12); color:#E8EEF7; padding:8px 10px; border-radius:6px;">
                                <option value="tutorials" <?php selected( $init_type, 'tutorials' ); ?>>Cyber Security Tutorial</option>
                                <option value="post" <?php selected( $init_type, 'post' ); ?>>Standard Article</option>
                                <option value="code" <?php selected( $init_type, 'code' ); ?>>Code Library &amp; Script</option>
                                <option value="device_review" <?php selected( $init_type, 'device_review' ); ?>>Device Review</option>
                                <option value="device_price" <?php selected( $init_type, 'device_price' ); ?>>Device Price</option>
                                <option value="tech_news" <?php selected( $init_type, 'tech_news' ); ?>>Tech News</option>
                            </select>
                        </div>
                    </div>
                </div>

            </div>

            <!-- RIGHT SIDEBAR -->
            <div class="hs-editor-sidebar-col" style="display:flex; flex-direction:column; gap:18px; min-width:0;">

                <!-- 1. PUBLISH META BOX -->
                <div style="background:#101827; border:1px solid rgba(255,255,255,0.12); border-radius:10px; overflow:hidden;">
                    <div style="padding:10px 14px; background:#172435; border-bottom:1px solid rgba(255,255,255,0.08); font-size:13px; font-weight:700; color:#E8EEF7;">
                        Publish
                    </div>
                    <div style="padding:14px; font-size:12px; color:#cbd5e1; display:flex; flex-direction:column; gap:10px;">
                        <div style="display:flex; justify-content:space-between; gap:10px;">
                            <button type="button" id="hs-btn-save-draft" style="flex:1; background:#080D17; border:1px solid rgba(255,255,255,0.15); color:#E8EEF7; font-size:12px; font-weight:600; padding:8px; border-radius:6px; cursor:pointer;">
                                Save Draft
                            </button>
                            <button type="button" id="hs-btn-preview" style="flex:1; background:#080D17; border:1px solid rgba(255,255,255,0.15); color:#20DCC5; font-size:12px; font-weight:600; padding:8px; border-radius:6px; cursor:pointer;">
                                Preview ↗
                            </button>
                        </div>
                        <div style="border-top:1px solid rgba(255,255,255,0.06); padding-top:10px; display:flex; flex-direction:column; gap:6px;">
                            <div>Status: <strong id="hs-status-label" style="color:#EABF57;"><?php echo esc_html( ucfirst( $init_status ) ); ?></strong></div>
                            <div>Visibility: <strong style="color:#10b981;">Public</strong></div>
                        </div>
                    </div>
                    <div style="padding:12px 14px; background:#172435; border-top:1px solid rgba(255,255,255,0.08); display:flex; justify-content:space-between; align-items:center;">
                        <button type="button" id="hs-btn-trash" style="background:none; border:none; color:#ef4444; font-size:12px; cursor:pointer; padding:0;">Move to Trash</button>
                        <button type="button" id="hs-btn-publish" class="hs-publish-btn">
                            <?php echo $edit_post_id > 0 ? 'Update Post' : 'Publish'; ?>
                        </button>
                    </div>
                </div>

                <!-- 2. FORMAT META BOX -->
                <div style="background:#101827; border:1px solid rgba(255,255,255,0.12); border-radius:10px; overflow:hidden;">
                    <div style="padding:10px 14px; background:#172435; border-bottom:1px solid rgba(255,255,255,0.08); font-size:13px; font-weight:700; color:#E8EEF7;">
                        Format
                    </div>
                    <div style="padding:14px; font-size:12px; display:flex; flex-direction:column; gap:8px;">
                        <label style="display:flex; align-items:center; gap:8px; cursor:pointer;">
                            <input type="radio" name="hs_format" value="standard" <?php checked( $init_format, 'standard' ); ?> /> Standard
                        </label>
                        <label style="display:flex; align-items:center; gap:8px; cursor:pointer;">
                            <input type="radio" name="hs_format" value="code" <?php checked( $init_format, 'code' ); ?> /> Code &amp; Script
                        </label>
                        <label style="display:flex; align-items:center; gap:8px; cursor:pointer;">
                            <input type="radio" name="hs_format" value="video" <?php checked( $init_format, 'video' ); ?> /> Video Walkthrough
                        </label>
                    </div>
                </div>

                <!-- 3. CATEGORIES META BOX -->
                <div style="background:#101827; border:1px solid rgba(255,255,255,0.12); border-radius:10px; overflow:hidden;">
                    <div style="padding:10px 14px; background:#172435; border-bottom:1px solid rgba(255,255,255,0.08); font-size:13px; font-weight:700; color:#E8EEF7;">
                        Categories
                    </div>
                    <div id="hs-categories-checklist" style="padding:14px; max-height:200px; overflow-y:auto; font-size:12px; display:flex; flex-direction:column; gap:8px;">
                        <?php if ( ! empty( $wp_categories ) ) : ?>
                            <?php foreach ( $wp_categories as $idx => $wc ) :
                                $is_checked = in_array( (int) $wc->term_id, $init_cat_ids, true ) || ( empty( $init_cat_ids ) && 0 === $idx );
                            ?>
                                <label style="display:flex; align-items:center; gap:8px; cursor:pointer;">
                                    <input type="checkbox" name="hs_cats[]" value="<?php echo (int) $wc->term_id; ?>" <?php checked( $is_checked ); ?> />
                                    <span><?php echo esc_html( $wc->name ); ?></span>
                                </label>
                            <?php endforeach; ?>
                        <?php else : ?>
                            <?php foreach ( $fallback_cats as $idx => $cat ) : ?>
                                <label style="display:flex; align-items:center; gap:8px; cursor:pointer;">
                                    <input type="checkbox" name="hs_cats[]" value="<?php echo esc_attr( $cat['id'] ); ?>" <?php echo ( 0 === $idx ) ? 'checked' : ''; ?> />
                                    <span><?php echo esc_html( $cat['name'] ); ?></span>
                                </label>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                    <div style="padding:10px 14px; background:#172435; border-top:1px solid rgba(255,255,255,0.08);">
                        <button type="button" id="hs-add-cat-toggle" style="background:none; border:none; color:#20DCC5; font-size:12px; cursor:pointer; padding:0;">+ Add New Category</button>
                        <div id="hs-new-cat-row" style="display:none; margin-top:8px; gap:6px;">
                            <input type="text" id="hs-new-cat-input" placeholder="Category name..." style="flex:1; background:#080D17; border:1px solid rgba(255,255,255,0.15); color:#E8EEF7; padding:5px 8px; border-radius:4px; font-size:12px;" />
                            <button type="button" id="hs-new-cat-add-btn" style="background:#20DCC5; color:#080D17; border:none; padding:5px 10px; border-radius:4px; font-weight:700; font-size:11px; cursor:pointer;">Add</button>
                        </div>
                    </div>
                </div>

                <!-- 4. TAGS META BOX -->
                <div style="background:#101827; border:1px solid rgba(255,255,255,0.12); border-radius:10px; overflow:hidden;">
                    <div style="padding:10px 14px; background:#172435; border-bottom:1px solid rgba(255,255,255,0.08); font-size:13px; font-weight:700; color:#E8EEF7;">
                        Tags
                    </div>
                    <div style="padding:14px; font-size:12px;">
                        <div style="display:flex; gap:6px; margin-bottom:8px;">
                            <input type="text" id="hs-tag-input" placeholder="e.g. linux, bash, python" style="flex:1; min-width:0; background:#080D17; border:1px solid rgba(255,255,255,0.15); color:#E8EEF7; padding:6px 10px; border-radius:4px; font-size:12px;" />
                            <button type="button" id="hs-tag-add-btn" style="background:#080D17; border:1px solid rgba(255,255,255,0.15); color:#E8EEF7; padding:6px 12px; border-radius:4px; font-weight:600; cursor:pointer;">
                                Add
                            </button>
                        </div>
                        <p style="color:#929FB2; font-size:11px; margin:0 0 8px;">Separate tags with commas or press Enter.</p>
                        <div id="hs-tags-list" style="display:flex; flex-wrap:wrap; gap:6px;">
                            <?php foreach ( $init_tags as $tg ) : ?>
                                <span class="hs-tag-chip" data-tag="<?php echo esc_attr( $tg ); ?>" style="background:#080D17; border:1px solid rgba(32,220,197,0.35); color:#20DCC5; padding:3px 8px; border-radius:5px; font-size:11px; display:inline-flex; align-items:center; gap:5px;">
                                    <span><?php echo esc_html( $tg ); ?></span>
                                    <button type="button" class="hs-tag-remove" style="background:none; border:none; color:#929FB2; cursor:pointer; padding:0; font-size:11px;">✕</button>
                                </span>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>

                <!-- 5. FEATURED IMAGE META BOX -->
                <div style="background:#101827; border:1px solid rgba(255,255,255,0.12); border-radius:10px; overflow:hidden;">
                    <div style="padding:10px 14px; background:#172435; border-bottom:1px solid rgba(255,255,255,0.08); font-size:13px; font-weight:700; color:#E8EEF7;">
                        Featured Image
                    </div>
                    <div style="padding:14px; font-size:12px; text-align:center;">
                        <input type="file" id="hs-featured-file-input" accept="image/*" style="display:none;" />
                        <input type="hidden" id="hs-featured-image-id" value="<?php echo (int) $init_thumb_id; ?>" />
                        <div id="hs-featured-preview" style="border:2px dashed rgba(255,255,255,0.15); border-radius:8px; padding:18px 12px; cursor:pointer; background:#080D17;">
                            <?php if ( $init_thumb_url ) : ?>
                                <img src="<?php echo esc_url( $init_thumb_url ); ?>" alt="Featured preview" style="max-width:100%; max-height:180px; border-radius:6px; object-fit:cover;" />
                                <p style="color:#20DCC5; font-size:11px; margin:8px 0 0;">Click to replace featured image</p>
                            <?php else : ?>
                                <span style="font-size:28px; display:block; margin-bottom:6px;">🖼️</span>
                                <span style="color:#20DCC5; font-weight:600; text-decoration:underline;">Set featured image</span>
                                <p style="color:#929FB2; font-size:11px; margin:4px 0 0;">Upload JPG, PNG, or WebP (max 5MB)</p>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

            </div>

        </div>

    </div>
</div>

<!-- Preview Modal -->
<div id="hs-preview-modal" style="display:none; position:fixed; inset:0; background:rgba(8,13,23,0.9); z-index:10000; overflow-y:auto; padding:32px 16px;">
    <div style="max-width:860px; margin:0 auto; background:#101827; border:1px solid rgba(32,220,197,0.4); border-radius:16px; padding:26px; color:#E8EEF7;">
        <div style="display:flex; justify-content:space-between; align-items:center; border-bottom:1px solid rgba(255,255,255,0.08); padding-bottom:12px; margin-bottom:18px;">
            <span style="font-size:12px; font-weight:800; color:#20DCC5; text-transform:uppercase;">Live Article Preview</span>
            <button type="button" id="hs-preview-close" style="background:#172435; border:1px solid rgba(255,255,255,0.15); color:#E8EEF7; padding:5px 12px; border-radius:6px; cursor:pointer;">✕ Close</button>
        </div>
        <h2 id="hs-preview-title" style="font-size:1.8rem; font-weight:900; color:#E8EEF7; margin:0 0 14px;"></h2>
        <div id="hs-preview-body" style="line-height:1.8; color:#cbd5e1; font-size:15px;"></div>
    </div>
</div>

<script>
(function() {
    var restNonce   = '<?php echo esc_js( $rest_nonce ); ?>';
    var currentPid  = <?php echo (int) $edit_post_id; ?>;
    var visualCanvas = document.getElementById('hs-visual-canvas');
    var rawCanvas    = document.getElementById('hs-raw-canvas');
    var modeTabs     = document.querySelectorAll('.hs-editor-mode-tab');
    var wordCountEl  = document.getElementById('hs-word-count');
    var alertBox     = document.getElementById('hs-editor-alert');
    var statusLabel  = document.getElementById('hs-status-label');
    var titleInput   = document.getElementById('hs-classic-title');
    var slugInput    = document.getElementById('hs-classic-slug');

    function showNotice(msg, isErr) {
        if (!alertBox) return;
        alertBox.style.display = 'block';
        alertBox.style.background = isErr ? 'rgba(239,68,68,0.14)' : 'rgba(32,220,197,0.14)';
        alertBox.style.border = '1px solid ' + (isErr ? '#ef4444' : '#20DCC5');
        alertBox.style.color = isErr ? '#fca5a5' : '#20DCC5';
        alertBox.textContent = msg;
        window.scrollTo({ top: 0, behavior: 'smooth' });
    }

    // Mode Switching
    modeTabs.forEach(function(tab) {
        tab.addEventListener('click', function() {
            var mode = this.getAttribute('data-mode');
            modeTabs.forEach(function(t) {
                t.style.background = '#101827';
                t.style.borderColor = 'rgba(255,255,255,0.12)';
                t.style.color = '#929FB2';
            });
            this.style.background = '#080D17';
            this.style.borderColor = 'rgba(32,220,197,0.4)';
            this.style.color = '#20DCC5';

            if (mode === 'text') {
                rawCanvas.value = visualCanvas.innerHTML;
                visualCanvas.style.display = 'none';
                rawCanvas.style.display = 'block';
            } else {
                visualCanvas.innerHTML = rawCanvas.value;
                rawCanvas.style.display = 'none';
                visualCanvas.style.display = 'block';
            }
        });
    });

    function updateWordCount() {
        var text = visualCanvas.innerText || visualCanvas.textContent || '';
        var words = text.trim().split(/\s+/).filter(function(w) { return w.length > 0; });
        if (wordCountEl) wordCountEl.textContent = 'Word count: ' + words.length;
    }
    visualCanvas.addEventListener('input', updateWordCount);
    updateWordCount();

    document.querySelectorAll('.hs-mce-btn').forEach(function(btn) {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            var cmd = this.getAttribute('data-cmd');
            var val = this.getAttribute('data-val') || null;
            if (cmd) {
                document.execCommand(cmd, false, val);
                visualCanvas.focus();
            }
        });
    });

    var formatDropdown = document.getElementById('hs-format-dropdown');
    if (formatDropdown) {
        formatDropdown.addEventListener('change', function() {
            document.execCommand('formatBlock', false, this.value);
            visualCanvas.focus();
        });
    }

    var linkBtn = document.getElementById('hs-cmd-link');
    if (linkBtn) {
        linkBtn.addEventListener('click', function() {
            var url = window.prompt('Enter URL:', 'https://');
            if (url) document.execCommand('createLink', false, url);
        });
    }

    var codeBtn = document.getElementById('hs-cmd-code');
    if (codeBtn) {
        codeBtn.addEventListener('click', function() {
            var code = window.prompt('Enter code / script block:', 'sudo apt update && sudo apt upgrade -y');
            if (code) {
                var safe = code.replace(/</g, '&lt;').replace(/>/g, '&gt;');
                document.execCommand('insertHTML', false, '<pre style="background:#050811; border:1px solid rgba(32,220,197,0.3); padding:10px; border-radius:6px; color:#20DCC5; font-family:monospace;">' + safe + '</pre><p></p>');
            }
        });
    }

    if (titleInput && slugInput) {
        titleInput.addEventListener('input', function() {
            if (!currentPid) {
                slugInput.value = this.value.toLowerCase().replace(/[^a-z0-9]+/g, '-').replace(/(^-|-$)+/g, '');
            }
        });
    }

    // Tags Management
    var tagInput = document.getElementById('hs-tag-input');
    var tagAddBtn = document.getElementById('hs-tag-add-btn');
    var tagsList = document.getElementById('hs-tags-list');

    function addTagsFromString(str) {
        if (!tagsList || !str) return;
        str.split(',').map(function(s) { return s.trim(); }).filter(Boolean).forEach(function(tag) {
            if (tagsList.querySelector('[data-tag="' + CSS.escape(tag) + '"]')) return;
            var chip = document.createElement('span');
            chip.className = 'hs-tag-chip';
            chip.setAttribute('data-tag', tag);
            chip.style.cssText = 'background:#080D17; border:1px solid rgba(32,220,197,0.35); color:#20DCC5; padding:3px 8px; border-radius:5px; font-size:11px; display:inline-flex; align-items:center; gap:5px;';
            var lbl = document.createElement('span');
            lbl.textContent = tag;
            var rm = document.createElement('button');
            rm.type = 'button';
            rm.className = 'hs-tag-remove';
            rm.textContent = '✕';
            rm.style.cssText = 'background:none; border:none; color:#929FB2; cursor:pointer; padding:0; font-size:11px;';
            chip.appendChild(lbl);
            chip.appendChild(rm);
            tagsList.appendChild(chip);
        });
    }

    if (tagAddBtn && tagInput) {
        tagAddBtn.addEventListener('click', function() {
            addTagsFromString(tagInput.value);
            tagInput.value = '';
        });
        tagInput.addEventListener('keydown', function(e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                addTagsFromString(tagInput.value);
                tagInput.value = '';
            }
        });
    }
    if (tagsList) {
        tagsList.addEventListener('click', function(e) {
            if (e.target.classList.contains('hs-tag-remove')) {
                var chip = e.target.closest('.hs-tag-chip');
                if (chip) chip.remove();
            }
        });
    }

    // Inline Category Addition
    var addCatToggle = document.getElementById('hs-add-cat-toggle');
    var newCatRow    = document.getElementById('hs-new-cat-row');
    var newCatInput  = document.getElementById('hs-new-cat-input');
    var newCatBtn    = document.getElementById('hs-new-cat-add-btn');
    var catChecklist = document.getElementById('hs-categories-checklist');
    if (addCatToggle && newCatRow) {
        addCatToggle.addEventListener('click', function() {
            newCatRow.style.display = newCatRow.style.display === 'flex' ? 'none' : 'flex';
        });
    }
    if (newCatBtn && newCatInput && catChecklist) {
        newCatBtn.addEventListener('click', function() {
            var val = newCatInput.value.trim();
            if (!val) return;
            var lbl = document.createElement('label');
            lbl.style.cssText = 'display:flex; align-items:center; gap:8px; cursor:pointer;';
            var cb = document.createElement('input');
            cb.type = 'checkbox';
            cb.name = 'hs_cats[]';
            cb.value = val;
            cb.checked = true;
            var span = document.createElement('span');
            span.textContent = val;
            lbl.appendChild(cb);
            lbl.appendChild(span);
            catChecklist.appendChild(lbl);
            newCatInput.value = '';
        });
    }

    // Real Media Upload (Inline & Featured Image)
    function uploadImageFile(file, cb) {
        var fd = new FormData();
        fd.append('file', file);
        fetch('/wp-json/hackersshikkhok/v1/user/media/upload', {
            method: 'POST',
            headers: { 'X-WP-Nonce': restNonce },
            body: fd
        })
        .then(function(r) { return r.json(); })
        .then(cb)
        .catch(function() { showNotice('Upload error. Make sure you are signed in.', true); });
    }

    var addMediaBtn = document.getElementById('hs-btn-add-media');
    var mediaFileInput = document.getElementById('hs-inline-media-file');
    var mediaLabel = document.getElementById('hs-media-btn-label');
    if (addMediaBtn && mediaFileInput) {
        addMediaBtn.addEventListener('click', function() { mediaFileInput.click(); });
        mediaFileInput.addEventListener('change', function() {
            var f = this.files && this.files[0];
            if (!f) return;
            if (mediaLabel) mediaLabel.textContent = 'Uploading...';
            uploadImageFile(f, function(res) {
                if (mediaLabel) mediaLabel.textContent = 'Add Media';
                if (res && res.success && res.url) {
                    visualCanvas.focus();
                    document.execCommand('insertHTML', false, '<p><img src="' + res.url + '" alt="" style="max-width:100%; border-radius:8px;" /></p>');
                    showNotice('✓ Image uploaded and inserted into article.', false);
                } else {
                    showNotice((res && res.message) ? res.message : 'Image upload failed.', true);
                }
            });
        });
    }

    var featPreview = document.getElementById('hs-featured-preview');
    var featInput   = document.getElementById('hs-featured-file-input');
    var featIdEl    = document.getElementById('hs-featured-image-id');
    if (featPreview && featInput) {
        featPreview.addEventListener('click', function() { featInput.click(); });
        featInput.addEventListener('change', function() {
            var f = this.files && this.files[0];
            if (!f) return;
            featPreview.innerHTML = '<span style="color:#20DCC5;">Uploading image...</span>';
            uploadImageFile(f, function(res) {
                if (res && res.success && res.attachment_id) {
                    featIdEl.value = res.attachment_id;
                    featPreview.innerHTML = '<img src="' + res.url + '" alt="Featured" style="max-width:100%; max-height:180px; border-radius:6px; object-fit:cover;" /><p style="color:#20DCC5; font-size:11px; margin:8px 0 0;">Click to change</p>';
                } else {
                    featPreview.innerHTML = '<span style="color:#ef4444;">Upload failed. Sign in & try again.</span>';
                }
            });
        });
    }

    // Preview Modal
    var previewBtn   = document.getElementById('hs-btn-preview');
    var previewModal = document.getElementById('hs-preview-modal');
    var previewClose = document.getElementById('hs-preview-close');
    if (previewBtn && previewModal) {
        previewBtn.addEventListener('click', function() {
            var content = (visualCanvas.style.display !== 'none') ? visualCanvas.innerHTML : rawCanvas.value;
            document.getElementById('hs-preview-title').textContent = titleInput.value || '(Untitled Article)';
            document.getElementById('hs-preview-body').innerHTML = content;
            previewModal.style.display = 'block';
        });
        if (previewClose) {
            previewClose.addEventListener('click', function() { previewModal.style.display = 'none'; });
        }
    }

    // Real Save / Publish / Trash via REST
    var publishBtn   = document.getElementById('hs-btn-publish');
    var saveDraftBtn = document.getElementById('hs-btn-save-draft');
    var trashBtn     = document.getElementById('hs-btn-trash');

    function savePost(status) {
        var title   = (titleInput || {}).value || '';
        var slug    = (slugInput || {}).value || '';
        var content = (visualCanvas.style.display !== 'none') ? visualCanvas.innerHTML : rawCanvas.value;
        var excerpt = (document.getElementById('hs-classic-excerpt') || {}).value || '';
        var type    = (document.getElementById('hs-meta-type') || {}).value || 'tutorials';
        var kw      = (document.getElementById('hs-meta-keyword') || {}).value || '';
        var thumbId = parseInt((featIdEl || {}).value || '0', 10) || 0;
        var fmtEl   = document.querySelector('input[name="hs_format"]:checked');
        var format  = fmtEl ? fmtEl.value : 'standard';
        var cats    = Array.from(document.querySelectorAll('input[name="hs_cats[]"]:checked')).map(function(cb) { return cb.value; });
        var tags    = Array.from(document.querySelectorAll('#hs-tags-list .hs-tag-chip')).map(function(c) { return c.getAttribute('data-tag'); }).join(',');

        if (status !== 'trash' && !title.trim()) {
            showNotice('Please enter a post title before saving.', true);
            return;
        }

        if (publishBtn) publishBtn.disabled = true;
        if (saveDraftBtn) saveDraftBtn.disabled = true;

        fetch('/wp-json/hackersshikkhok/v1/user/post/create', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-WP-Nonce': restNonce
            },
            body: JSON.stringify({
                post_id: currentPid,
                title: title,
                slug: slug,
                content: content,
                excerpt: excerpt,
                type: type,
                status: status,
                tags: tags,
                categories: cats,
                format: format,
                focus_keyword: kw,
                featured_image_id: thumbId
            })
        })
        .then(function(r) { return r.json(); })
        .then(function(res) {
            if (publishBtn) publishBtn.disabled = false;
            if (saveDraftBtn) saveDraftBtn.disabled = false;
            if (res && res.success) {
                currentPid = res.post_id || currentPid;
                if (statusLabel) {
                    statusLabel.textContent = status === 'trash' ? 'Trashed' : (status === 'publish' ? 'Published' : 'Draft');
                    statusLabel.style.color = status === 'publish' ? '#10b981' : '#EABF57';
                }
                showNotice(res.message || 'Saved successfully!', false);
                if (status === 'publish' && res.permalink) {
                    setTimeout(function() { window.location.href = res.permalink; }, 1200);
                } else if (status === 'trash') {
                    setTimeout(function() { window.location.href = '<?php echo esc_url( home_url( '/dashboard/?tab=posts' ) ); ?>'; }, 1000);
                }
            } else {
                showNotice((res && res.message) ? res.message : 'Unable to save post. Please make sure you are signed in.', true);
            }
        })
        .catch(function() {
            if (publishBtn) publishBtn.disabled = false;
            if (saveDraftBtn) saveDraftBtn.disabled = false;
            showNotice('Network error saving post. Please try again.', true);
        });
    }

    if (publishBtn) publishBtn.addEventListener('click', function() { savePost('publish'); });
    if (saveDraftBtn) saveDraftBtn.addEventListener('click', function() { savePost('draft'); });
    if (trashBtn) {
        trashBtn.addEventListener('click', function() {
            if (!currentPid) {
                visualCanvas.innerHTML = '<p></p>';
                titleInput.value = '';
                showNotice('Editor cleared.', false);
                return;
            }
            savePost('trash');
        });
    }
})();
</script>

<?php get_footer(); ?>
