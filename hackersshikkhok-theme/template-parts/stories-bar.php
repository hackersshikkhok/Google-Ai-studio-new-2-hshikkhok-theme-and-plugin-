<?php
/**
 * HackersShikkhok - Member Cyber Stories Carousel & Viewer
 * Brand: HackersShikkhok (https://hackersshikkhok.com)
 * STRICT PRIVACY RULE: Only visible for logged-in authenticated members!
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

// STRICT SECURITY & PRIVACY CHECK: Hidden for unauthenticated visitors / guests
if ( ! is_user_logged_in() ) {
    return;
}

$current_user_id = get_current_user_id();
$current_user    = wp_get_current_user();
$current_uname   = $current_user->display_name ?: $current_user->user_login;

// Fetch active stories via StoryEngine
$all_active_stories = class_exists( '\HackersShikkhok\Theme\Modules\Community\StoryEngine' )
    ? \HackersShikkhok\Theme\Modules\Community\StoryEngine::get_active_stories()
    : array();
$hs_story_nonce = wp_create_nonce( 'hs_ajax_nonce' );

// Group stories by user_id
$user_stories = array();
foreach ( $all_active_stories as $st ) {
    $uid = $st['user_id'] ?? 0;
    if ( ! isset( $user_stories[ $uid ] ) ) {
        $user_stories[ $uid ] = array();
    }
    $user_stories[ $uid ][] = $st;
}

// Fetch active community members to display active nodes
$active_threshold = time() - 86400; // last 24h
$community_users = get_users( array(
    'number'  => 15,
    'exclude' => array( $current_user_id ),
) );

$self_has_stories = isset( $user_stories[ $current_user_id ] );
$self_stories_json = $self_has_stories ? wp_json_encode( $user_stories[ $current_user_id ] ) : '[]';
?>

<div class="hs-messenger-stories-wrap" id="hs-stories-section">
    <div class="hs-stories-scroll-box" id="hs-stories-scroll-box">

        <!-- 1. SELF STORY NODE: CREATE / VIEW OWN STORY -->
        <div class="hs-story-circle-item self-node <?php echo $self_has_stories ? 'has-unviewed' : ''; ?>" id="hs-self-story-node" onclick="handleHsSelfStoryClick(event)">
            <div class="hs-avatar-ring-wrapper">
                <div class="hs-story-avatar">
                    <?php echo get_avatar( $current_user_id, 48 ); ?>
                </div>
                <div class="hs-create-story-plus" title="স্টোরি দিন">+</div>
            </div>
            <span class="hs-story-username">আমার স্টোরি</span>
        </div>

        <!-- 2. COMMUNITY MEMBER STORIES -->
        <?php foreach ( $community_users as $u ) :
            $u_id = $u->ID;
            $has_stories = isset( $user_stories[ $u_id ] );
            $user_st_json = $has_stories ? wp_json_encode( $user_stories[ $u_id ] ) : '[]';
            $u_avatar = get_avatar_url( $u_id, array( 'size' => 48 ) );
            $u_name = $u->display_name ?: $u->user_login;
        ?>
        <div class="hs-story-circle-item <?php echo $has_stories ? 'has-unviewed' : 'no-story'; ?>" onclick="openHsStoryViewer(<?php echo (int) $u_id; ?>, '<?php echo esc_js( $u_name ); ?>', '<?php echo esc_url( $u_avatar ); ?>', <?php echo esc_attr( $user_st_json ); ?>)">
            <div class="hs-avatar-ring-wrapper">
                <div class="hs-story-avatar">
                    <img src="<?php echo esc_url( $u_avatar ); ?>" alt="<?php echo esc_attr( $u_name ); ?>" loading="lazy" />
                </div>
                <?php if ( $has_stories ) : ?>
                    <span class="hs-active-pulse-dot" title="Active Story"></span>
                <?php endif; ?>
            </div>
            <span class="hs-story-username"><?php echo esc_html( wp_trim_words( $u_name, 1, '' ) ); ?></span>
        </div>
        <?php endforeach; ?>

    </div>
</div>

<!-- =====================================
     IMMERSIVE FULLSCREEN STORY VIEWER MODAL
     ===================================== -->
<div id="hs-story-viewer-modal" class="hs-story-viewer-modal" style="display: none;">
    <div class="hs-viewer-overlay" onclick="closeHsStoryViewer()"></div>
    <div class="hs-viewer-content">
        <button type="button" class="hs-close-viewer-btn" onclick="closeHsStoryViewer()" aria-label="Close Story">✕</button>

        <!-- Dynamic Story Progress Tracks -->
        <div class="hs-viewer-progress-header" id="hs-viewer-progress-bars"></div>

        <!-- Author Header Meta -->
        <div class="hs-viewer-author-meta">
            <div class="hs-author-avatar-img" id="hs-viewer-author-avatar">
                <img src="" alt="" />
            </div>
            <div class="hs-author-info">
                <h4 id="hs-viewer-author-name">Operative</h4>
                <span id="hs-viewer-story-time">Just now</span>
            </div>
            <div class="hs-viewer-count-tag">
                👁️ <span id="hs-viewer-views-count">0</span>
            </div>
        </div>

        <!-- Main Body Canvas -->
        <div class="hs-viewer-main-body" id="hs-viewer-media-canvas" onclick="advanceHsStorySegment()">
            <!-- Dynamic Content Injected Here -->
        </div>

        <!-- Caption Box -->
        <div class="hs-viewer-caption-box" id="hs-viewer-caption-box" style="display: none;"></div>

        <!-- Comments Overlay Drawer -->
        <div class="hs-viewer-comments-panel" id="hs-viewer-comments-panel">
            <div class="hs-comments-panel-title">💬 মন্তব্যসমূহ (<span id="hs-viewer-comments-count">0</span>)</div>
            <div class="hs-comments-scrollable-area" id="hs-viewer-comments-list">
                <!-- Comments list -->
            </div>
        </div>

        <!-- Interactive Reaction & Comment Footer Bar -->
        <div class="hs-viewer-interactions-dock">
            <div class="hs-reactions-flyout-container">
                <button type="button" class="hs-react-trigger-btn" onclick="toggleHsReactionsMenu()">❤️ রিঅ্যাক্ট</button>
                <div class="hs-reactions-popover" id="hs-reactions-popover">
                    <span class="hs-react-em" onclick="submitHsStoryReaction('❤️')">❤️</span>
                    <span class="hs-react-em" onclick="submitHsStoryReaction('🔥')">🔥</span>
                    <span class="hs-react-em" onclick="submitHsStoryReaction('👏')">👏</span>
                    <span class="hs-react-em" onclick="submitHsStoryReaction('😮')">😮</span>
                    <span class="hs-react-em" onclick="submitHsStoryReaction('🛡️')">🛡️</span>
                </div>
            </div>
            <div class="hs-comment-input-row">
                <input type="text" id="hs-story-comment-field" placeholder="মন্তব্য লিখুন..." onkeypress="handleHsStoryCommentKey(event)" />
                <button type="button" class="hs-story-send-comment-btn" onclick="submitHsStoryComment()">পাঠান</button>
            </div>
        </div>
    </div>
</div>

<!-- =====================================
     CREATE STORY MODAL
     ===================================== -->
<div id="hs-create-story-modal" class="hs-create-story-modal-overlay" style="display: none;">
    <div class="hs-create-story-box">
        <div class="hs-create-story-header">
            <h3>⚡ নতুন সাইবার স্টোরি পাবলিশ করুন</h3>
            <button type="button" class="hs-close-create-btn" onclick="closeHsCreateStoryModal()">✕</button>
        </div>

        <div class="hs-create-tabs">
            <button type="button" class="hs-story-tab-btn active" id="hs-tab-btn-text" onclick="switchHsStoryTab('text')">✍️ টেক্সট স্টোরি</button>
            <button type="button" class="hs-story-tab-btn" id="hs-tab-btn-media" onclick="switchHsStoryTab('media')">📸 ছবি / ভিডিও</button>
        </div>

        <form id="hs-story-upload-form" onsubmit="handleHsStoryUploadForm(event)">
            <!-- Text Story Mode -->
            <div class="hs-tab-pane active" id="hs-pane-text">
                <div class="hs-story-form-group">
                    <textarea id="hs-story-text-content" rows="4" placeholder="আজকের হ্যাকিং বা কোডিং আপডেট লিখুন..." style="width: 100%; background: #0f172a; border: 1px solid rgba(0,245,212,0.3); border-radius: 10px; color: #fff; padding: 12px; font-size: 14px; font-family: inherit; resize: none;"></textarea>
                </div>
                <div class="hs-story-form-group">
                    <label style="color: #94a3b8; font-size: 11px; font-weight: 700; margin-bottom: 6px; display: block;">ব্যাকগ্রাউন্ড থিম নির্বাচন করুন:</label>
                    <div class="hs-bg-grad-selector-grid">
                        <div class="hs-grad-swatch active" style="background: linear-gradient(135deg, #00f5d4, #0077b6);" onclick="selectHsStoryGrad(this, 'linear-gradient(135deg, #00f5d4, #0077b6)')"></div>
                        <div class="hs-grad-swatch" style="background: linear-gradient(135deg, #ff007f, #7b2cbf);" onclick="selectHsStoryGrad(this, 'linear-gradient(135deg, #ff007f, #7b2cbf)')"></div>
                        <div class="hs-grad-swatch" style="background: linear-gradient(135deg, #10b981, #050811);" onclick="selectHsStoryGrad(this, 'linear-gradient(135deg, #10b981, #050811)')"></div>
                        <div class="hs-grad-swatch" style="background: linear-gradient(135deg, #f59e0b, #ef4444);" onclick="selectHsStoryGrad(this, 'linear-gradient(135deg, #f59e0b, #ef4444)')"></div>
                        <div class="hs-grad-swatch" style="background: linear-gradient(135deg, #3b82f6, #1e1b4b);" onclick="selectHsStoryGrad(this, 'linear-gradient(135deg, #3b82f6, #1e1b4b)')"></div>
                    </div>
                </div>
            </div>

            <!-- Media Story Mode -->
            <div class="hs-tab-pane" id="hs-pane-media">
                <div class="hs-story-form-group">
                    <label class="hs-story-file-uploader-zone" for="hs-story-file-input">
                        <span style="font-size: 28px;">📷</span>
                        <p id="hs-file-chosen-label">ডিভাইস থেকে ছবি বা ক্লিপ সিলেক্ট করুন</p>
                        <input type="file" id="hs-story-file-input" name="story_file" accept="image/*,video/*" style="display: none;" onchange="handleHsStoryFileChange(this)" />
                    </label>
                </div>
                <div class="hs-story-form-group">
                    <input type="url" id="hs-story-media-url-field" placeholder="অথবা ইমেজের ডিরেক্ট URL দিন..." style="width: 100%; background: #0f172a; border: 1px solid rgba(0,245,212,0.3); border-radius: 8px; color: #fff; padding: 10px; font-size: 13px;" />
                </div>
                <div class="hs-story-form-group">
                    <input type="text" id="hs-story-caption-field" placeholder="স্টোরির ক্যাপশন (অপশনাল)..." style="width: 100%; background: #0f172a; border: 1px solid rgba(0,245,212,0.3); border-radius: 8px; color: #fff; padding: 10px; font-size: 13px;" />
                </div>
            </div>

            <button type="submit" id="hs-submit-story-btn" style="width: 100%; background: linear-gradient(135deg, #00f5d4, #00b4d8); color: #050811; border: none; font-weight: 800; font-size: 14px; padding: 12px; border-radius: 10px; cursor: pointer; box-shadow: 0 0 15px rgba(0,245,212,0.4); margin-top: 10px;">
                🚀 পাবলিশ করুন (অর্জন করুন +৫ XP)
            </button>
        </form>
    </div>
</div>

<style>
.hs-messenger-stories-wrap {
    background: #080d18;
    border: 1px solid rgba(0, 245, 212, 0.15);
    box-shadow: 0 8px 30px rgba(0, 0, 0, 0.6);
    border-radius: 16px;
    padding: 8px 12px;
    max-width: 900px;
    margin: 12px auto 8px auto;
    position: relative;
    overflow: hidden;
}
.hs-stories-scroll-box {
    display: flex;
    overflow-x: auto;
    gap: 12px;
    padding-bottom: 4px;
    scrollbar-width: thin;
    scrollbar-color: rgba(0, 245, 212, 0.2) transparent;
}
.hs-stories-scroll-box::-webkit-scrollbar {
    height: 3px;
}
.hs-stories-scroll-box::-webkit-scrollbar-thumb {
    background: rgba(0, 245, 212, 0.3);
    border-radius: 10px;
}
.hs-story-circle-item {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 4px;
    cursor: pointer;
    flex-shrink: 0;
    width: 56px;
    transition: transform 0.2s cubic-bezier(0.175, 0.885, 0.32, 1.275);
}
.hs-story-circle-item:hover {
    transform: scale(1.06);
}
.hs-avatar-ring-wrapper {
    position: relative;
    width: 48px;
    height: 48px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
}
.hs-story-circle-item.has-unviewed .hs-avatar-ring-wrapper {
    background: linear-gradient(135deg, #00f5d4 0%, #00e5ff 50%, #ff007f 100%);
    padding: 2.5px;
    box-shadow: 0 0 12px rgba(0, 245, 212, 0.4);
}
.hs-story-circle-item.no-story .hs-avatar-ring-wrapper {
    background: rgba(255, 255, 255, 0.1);
    padding: 2px;
}
.hs-story-avatar {
    width: 100%;
    height: 100%;
    border-radius: 50%;
    overflow: hidden;
    background: #000;
    border: 1.5px solid #080d18;
}
.hs-story-avatar img {
    width: 100% !important;
    height: 100% !important;
    border-radius: 50% !important;
    object-fit: cover !important;
    display: block !important;
}
.hs-create-story-plus {
    position: absolute;
    bottom: -2px;
    right: -2px;
    background: #00f5d4;
    color: #050811;
    width: 16px;
    height: 16px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 12px;
    font-weight: 900;
    border: 2px solid #080d18;
    box-shadow: 0 0 8px #00f5d4;
}
.hs-active-pulse-dot {
    position: absolute;
    bottom: 0;
    right: 0;
    width: 10px;
    height: 10px;
    border-radius: 50%;
    background: #00f5d4;
    border: 2px solid #080d18;
    box-shadow: 0 0 6px #00f5d4;
}
.hs-story-username {
    font-size: 10px;
    color: #cbd5e1;
    font-weight: 700;
    text-align: center;
    width: 100%;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}
/* Viewer Modal */
.hs-story-viewer-modal {
    position: fixed;
    top: 0; left: 0; width: 100%; height: 100%;
    z-index: 10000000;
    display: flex;
    align-items: center;
    justify-content: center;
}
.hs-viewer-overlay {
    position: absolute;
    top: 0; left: 0; width: 100%; height: 100%;
    background: rgba(2, 4, 10, 0.95);
    backdrop-filter: blur(10px);
}
.hs-viewer-content {
    position: relative;
    width: 100%;
    max-width: 440px;
    height: 80vh;
    background: #000;
    border-radius: 18px;
    overflow: hidden;
    display: flex;
    flex-direction: column;
    box-shadow: 0 20px 60px rgba(0, 0, 0, 0.9);
    border: 1.5px solid rgba(0, 245, 212, 0.25);
    z-index: 2;
}
@media(max-width: 480px) {
    .hs-viewer-content {
        height: 100%;
        max-height: 100vh;
        border-radius: 0;
    }
}
.hs-close-viewer-btn {
    position: absolute;
    top: 14px; right: 14px;
    background: rgba(0, 0, 0, 0.6);
    border: 1px solid rgba(255, 255, 255, 0.2);
    color: #fff;
    width: 32px; height: 32px;
    border-radius: 50%;
    font-size: 16px;
    cursor: pointer;
    z-index: 10;
}
.hs-viewer-progress-header {
    display: flex;
    gap: 4px;
    padding: 10px 14px;
    position: absolute;
    top: 0; left: 0; width: 100%;
    z-index: 9;
}
.hs-prog-track {
    height: 3px;
    flex: 1;
    background: rgba(255, 255, 255, 0.25);
    border-radius: 2px;
    overflow: hidden;
}
.hs-prog-fill {
    height: 100%;
    width: 0%;
    background: #00f5d4;
    box-shadow: 0 0 6px #00f5d4;
}
.hs-viewer-author-meta {
    display: flex;
    align-items: center;
    gap: 10px;
    position: absolute;
    top: 18px; left: 14px;
    z-index: 8;
    background: linear-gradient(180deg, rgba(0,0,0,0.7) 0%, transparent 100%);
    width: calc(100% - 28px);
}
.hs-author-avatar-img {
    width: 34px; height: 34px;
    border-radius: 50%;
    border: 1.5px solid #00f5d4;
    overflow: hidden;
}
.hs-author-avatar-img img {
    width: 100%; height: 100%; object-fit: cover;
}
.hs-author-info h4 {
    margin: 0; font-size: 13px; font-weight: 800; color: #fff;
}
.hs-author-info span {
    font-size: 10px; color: #94a3b8;
}
.hs-viewer-count-tag {
    margin-left: auto;
    background: rgba(0, 0, 0, 0.6);
    padding: 3px 8px; border-radius: 10px;
    font-size: 10px; color: #fff; border: 0.5px solid rgba(255, 255, 255, 0.15);
}
.hs-viewer-main-body {
    flex: 1;
    display: flex;
    align-items: center;
    justify-content: center;
    background: #05070a;
    cursor: pointer;
}
.hs-viewer-main-body img, .hs-viewer-main-body video {
    max-width: 100%; max-height: 100%; object-fit: contain;
}
.hs-text-story-canvas {
    width: 100%; height: 100%;
    display: flex; align-items: center; justify-content: center;
    padding: 24px; text-align: center;
    font-weight: 800; font-size: 20px; line-height: 1.5; color: #fff;
}
.hs-viewer-caption-box {
    position: absolute;
    bottom: 110px; left: 14px; width: calc(100% - 28px);
    background: rgba(0, 0, 0, 0.7);
    padding: 8px 12px; border-radius: 8px;
    font-size: 12px; color: #f8fafc;
    border-left: 3px solid #00f5d4;
    z-index: 8;
}
.hs-viewer-comments-panel {
    background: #090e18;
    border-top: 1px solid rgba(255, 255, 255, 0.08);
    padding: 10px 14px;
    max-height: 120px;
    overflow-y: auto;
}
.hs-comments-panel-title {
    font-size: 10px; margin: 0 0 6px 0; color: #94a3b8; text-transform: uppercase; font-weight: 800;
}
.hs-comments-scrollable-area {
    display: flex; flex-direction: column; gap: 6px;
}
.hs-story-comment-bubble {
    display: flex; gap: 8px; font-size: 11px;
}
.hs-story-comment-bubble img {
    width: 22px; height: 22px; border-radius: 50%;
}
.hs-comment-bubble-text {
    background: #1e293b; padding: 4px 8px; border-radius: 6px; color: #f1f5f9; flex: 1;
}
.hs-comment-bubble-text b { color: #00f5d4; font-weight: bold; margin-right: 4px; }
.hs-viewer-interactions-dock {
    display: flex; gap: 8px; align-items: center;
    padding: 10px 14px; background: #050811;
    border-top: 1px solid rgba(255, 255, 255, 0.05);
}
.hs-react-trigger-btn {
    background: rgba(255, 0, 127, 0.15); border: 1px solid rgba(255, 0, 127, 0.4);
    color: #ff007f; padding: 8px 12px; border-radius: 8px; font-size: 12px; font-weight: 800; cursor: pointer;
}
.hs-reactions-flyout-container {
    position: relative;
}
.hs-reactions-popover {
    display: none; position: absolute; bottom: 100%; left: 0;
    background: #0f172a; border: 1px solid rgba(0, 245, 212, 0.3);
    border-radius: 24px; padding: 6px 10px; gap: 8px; margin-bottom: 8px;
    box-shadow: 0 8px 25px rgba(0, 0, 0, 0.8); z-index: 99;
}
.hs-react-em {
    font-size: 18px; cursor: pointer; transition: transform 0.2s;
}
.hs-react-em:hover {
    transform: scale(1.3);
}
.hs-comment-input-row {
    flex: 1; display: flex; gap: 6px;
}
.hs-comment-input-row input {
    flex: 1; background: #0f172a; border: 1px solid rgba(255, 255, 255, 0.1);
    border-radius: 6px; padding: 6px 10px; font-size: 12px; color: #fff; outline: none;
}
.hs-story-send-comment-btn {
    background: #00f5d4; color: #050811; border: none; font-weight: 800;
    font-size: 11px; padding: 0 12px; border-radius: 6px; cursor: pointer;
}
/* Create Story Modal */
.hs-create-story-modal-overlay {
    position: fixed;
    top: 0; left: 0; width: 100%; height: 100%;
    background: rgba(0, 0, 0, 0.85); backdrop-filter: blur(6px);
    z-index: 10000000; display: flex; align-items: center; justify-content: center; padding: 16px;
}
.hs-create-story-box {
    background: #090e18; border: 2px solid #00f5d4;
    box-shadow: 0 15px 50px rgba(0, 245, 212, 0.2);
    border-radius: 16px; padding: 20px; width: 100%; max-width: 440px;
}
.hs-create-story-header {
    display: flex; justify-content: space-between; align-items: center;
    border-bottom: 1px solid rgba(255, 255, 255, 0.08); padding-bottom: 10px; margin-bottom: 14px;
}
.hs-create-story-header h3 {
    margin: 0; font-size: 15px; font-weight: 800; color: #fff;
}
.hs-close-create-btn {
    background: none; border: none; color: #94a3b8; font-size: 20px; cursor: pointer;
}
.hs-create-tabs {
    display: flex; gap: 8px; margin-bottom: 14px;
}
.hs-story-tab-btn {
    flex: 1; padding: 8px; font-size: 12px; font-weight: 700;
    background: rgba(255, 255, 255, 0.03); color: #94a3b8; border: 1px solid rgba(255, 255, 255, 0.08);
    border-radius: 8px; cursor: pointer; transition: 0.2s;
}
.hs-story-tab-btn.active {
    background: rgba(0, 245, 212, 0.12); color: #00f5d4; border-color: #00f5d4;
}
.hs-tab-pane { display: none; }
.hs-tab-pane.active { display: block; }
.hs-story-form-group { margin-bottom: 12px; }
.hs-story-file-uploader-zone {
    border: 2px dashed rgba(0, 245, 212, 0.4); background: rgba(0, 245, 212, 0.03);
    border-radius: 10px; padding: 20px; text-align: center; cursor: pointer; display: block;
    transition: 0.2s;
}
.hs-story-file-uploader-zone:hover {
    border-color: #00f5d4; background: rgba(0, 245, 212, 0.08);
}
.hs-bg-grad-selector-grid {
    display: flex; gap: 8px;
}
.hs-grad-swatch {
    width: 28px; height: 28px; border-radius: 50%; cursor: pointer; border: 2px solid transparent;
}
.hs-grad-swatch.active {
    border-color: #fff; box-shadow: 0 0 8px #fff;
}
</style>

<script>
let hsCurrentStoryPack = [];
let hsCurrentStoryIndex = 0;
let hsStoryTimer = null;
let hsActiveMediaType = 'text';
let hsSelectedGrad = 'linear-gradient(135deg, #00f5d4, #0077b6)';

function handleHsSelfStoryClick(e) {
    e.preventDefault();
    const selfStories = <?php echo $self_stories_json; ?>;
    if (selfStories && selfStories.length > 0) {
        openHsStoryViewer(<?php echo (int) $current_user_id; ?>, '<?php echo esc_js( $current_uname ); ?>', '<?php echo esc_url( get_avatar_url( $current_user_id, array( 'size' => 48 ) ) ); ?>', selfStories);
    } else {
        openHsCreateStoryModal();
    }
}

function openHsCreateStoryModal() {
    document.getElementById('hs-create-story-modal').style.display = 'flex';
}

function closeHsCreateStoryModal() {
    document.getElementById('hs-create-story-modal').style.display = 'none';
}

function switchHsStoryTab(type) {
    hsActiveMediaType = type;
    document.getElementById('hs-tab-btn-text').classList.toggle('active', type === 'text');
    document.getElementById('hs-tab-btn-media').classList.toggle('active', type === 'media');
    document.getElementById('hs-pane-text').classList.toggle('active', type === 'text');
    document.getElementById('hs-pane-media').classList.toggle('active', type === 'media');
}

function selectHsStoryGrad(elem, grad) {
    hsSelectedGrad = grad;
    document.querySelectorAll('.hs-grad-swatch').forEach(s => s.classList.remove('active'));
    elem.classList.add('active');
}

function handleHsStoryFileChange(input) {
    if (input.files && input.files[0]) {
        document.getElementById('hs-file-chosen-label').textContent = '✅ সিলেক্টেড: ' + input.files[0].name;
    }
}

function handleHsStoryUploadForm(e) {
    e.preventDefault();
    const btn = document.getElementById('hs-submit-story-btn');
    btn.disabled = true;
    btn.textContent = 'পাবলিশ হচ্ছে... দয়া করে অপেক্ষা করুন।';

    const formData = new FormData();
    formData.append('action', 'hs_upload_story');
    formData.append('nonce', '<?php echo esc_js( $hs_story_nonce ); ?>');
    formData.append('media_type', hsActiveMediaType);

    if (hsActiveMediaType === 'text') {
        formData.append('caption', document.getElementById('hs-story-text-content').value);
        formData.append('bg_gradient', hsSelectedGrad);
    } else {
        formData.append('caption', document.getElementById('hs-story-caption-field').value);
        const fileInput = document.getElementById('hs-story-file-input');
        if (fileInput.files.length > 0) {
            formData.append('story_file', fileInput.files[0]);
        }
        formData.append('media_url', document.getElementById('hs-story-media-url-field').value);
    }

    fetch('<?php echo esc_url( admin_url( 'admin-ajax.php' ) ); ?>', {
        method: 'POST',
        body: formData
    })
    .then(r => r.json())
    .then(res => {
        if (res.success) {
            alert(res.data.message || 'স্টোরি সফলভাবে পোস্ট হয়েছে!');
            window.location.reload();
        } else {
            alert(res.data.message || 'ত্রুটি ঘটেছে!');
            btn.disabled = false;
            btn.textContent = '🚀 পাবলিশ করুন (অর্জন করুন +৫ XP)';
        }
    })
    .catch(() => {
        alert('নেটওয়ার্ক ত্রুটি! আবার চেষ্টা করুন।');
        btn.disabled = false;
        btn.textContent = '🚀 পাবলিশ করুন (অর্জন করুন +৫ XP)';
    });
}

function openHsStoryViewer(userId, userName, userAvatar, stories) {
    if (!stories || stories.length === 0) {
        openHsCreateStoryModal();
        return;
    }
    hsCurrentStoryPack = stories;
    hsCurrentStoryIndex = 0;

    document.getElementById('hs-viewer-author-name').textContent = userName;
    document.getElementById('hs-viewer-author-avatar').querySelector('img').src = userAvatar;
    document.getElementById('hs-story-viewer-modal').style.display = 'flex';

    playHsStorySegment();
}

function playHsStorySegment() {
    if (hsCurrentStoryIndex >= hsCurrentStoryPack.length) {
        closeHsStoryViewer();
        return;
    }
    cancelAnimationFrame(hsStoryTimer);
    const story = hsCurrentStoryPack[hsCurrentStoryIndex];

    // Track view via AJAX
    const fd = new FormData();
    fd.append('action', 'hs_view_story');
    fd.append('nonce', '<?php echo esc_js( $hs_story_nonce ); ?>');
    fd.append('story_id', story.id);
    fetch('<?php echo esc_url( admin_url( 'admin-ajax.php' ) ); ?>', { method: 'POST', body: fd });

    document.getElementById('hs-viewer-views-count').textContent = (story.views || []).length;
    const sDate = new Date((story.created_at || Date.now() / 1000) * 1000);
    document.getElementById('hs-viewer-story-time').textContent = sDate.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });

    // Progress Bars
    const barWrap = document.getElementById('hs-viewer-progress-bars');
    barWrap.innerHTML = '';
    hsCurrentStoryPack.forEach((_, idx) => {
        const track = document.createElement('div');
        track.className = 'hs-prog-track';
        const fill = document.createElement('div');
        fill.className = 'hs-prog-fill';
        if (idx < hsCurrentStoryIndex) fill.style.width = '100%';
        track.appendChild(fill);
        barWrap.appendChild(track);
    });

    // Content Canvas
    const canvas = document.getElementById('hs-viewer-media-canvas');
    canvas.innerHTML = '';
    const capBox = document.getElementById('hs-viewer-caption-box');

    if (story.media_type === 'text') {
        const txt = document.createElement('div');
        txt.className = 'hs-text-story-canvas';
        txt.style.background = story.bg_gradient || 'linear-gradient(135deg, #00f5d4, #7b2cbf)';
        txt.textContent = story.caption || '';
        canvas.appendChild(txt);
        capBox.style.display = 'none';
    } else if (story.media_type === 'video' && story.media_url) {
        const vid = document.createElement('video');
        vid.src = story.media_url;
        vid.autoplay = true;
        vid.onended = advanceHsStorySegment;
        canvas.appendChild(vid);
        if (story.caption) {
            capBox.textContent = story.caption;
            capBox.style.display = 'block';
        } else {
            capBox.style.display = 'none';
        }
    } else {
        const img = document.createElement('img');
        img.src = story.media_url;
        canvas.appendChild(img);
        if (story.caption) {
            capBox.textContent = story.caption;
            capBox.style.display = 'block';
        } else {
            capBox.style.display = 'none';
        }
    }

    // Comments
    document.getElementById('hs-viewer-comments-count').textContent = (story.comments || []).length;
    renderHsStoryComments(story.comments || []);

    // Animate progress
    const activeFill = barWrap.children[hsCurrentStoryIndex].querySelector('.hs-prog-fill');
    let start = null;
    const duration = story.media_type === 'text' ? 5000 : 7000;

    function step(ts) {
        if (!start) start = ts;
        const elapsed = ts - start;
        const pct = Math.min(100, (elapsed / duration) * 100);
        if (activeFill) activeFill.style.width = pct + '%';

        if (elapsed < duration) {
            hsStoryTimer = requestAnimationFrame(step);
        } else {
            advanceHsStorySegment();
        }
    }
    hsStoryTimer = requestAnimationFrame(step);
}

function advanceHsStorySegment() {
    cancelAnimationFrame(hsStoryTimer);
    hsCurrentStoryIndex++;
    playHsStorySegment();
}

function closeHsStoryViewer() {
    cancelAnimationFrame(hsStoryTimer);
    document.getElementById('hs-story-viewer-modal').style.display = 'none';
}

function toggleHsReactionsMenu() {
    const pop = document.getElementById('hs-reactions-popover');
    pop.style.display = pop.style.display === 'flex' ? 'none' : 'flex';
}

function submitHsStoryReaction(type) {
    const story = hsCurrentStoryPack[hsCurrentStoryIndex];
    if (!story) return;
    document.getElementById('hs-reactions-popover').style.display = 'none';

    const fd = new FormData();
    fd.append('action', 'hs_react_story');
    fd.append('nonce', '<?php echo esc_js( $hs_story_nonce ); ?>');
    fd.append('story_id', story.id);
    fd.append('react_type', type);
    fetch('<?php echo esc_url( admin_url( 'admin-ajax.php' ) ); ?>', { method: 'POST', body: fd });
}

function submitHsStoryComment() {
    const story = hsCurrentStoryPack[hsCurrentStoryIndex];
    const field = document.getElementById('hs-story-comment-field');
    const val = field.value.trim();
    if (!story || !val) return;

    const fd = new FormData();
    fd.append('action', 'hs_comment_story');
    fd.append('nonce', '<?php echo esc_js( $hs_story_nonce ); ?>');
    fd.append('story_id', story.id);
    fd.append('comment_text', val);

    fetch('<?php echo esc_url( admin_url( 'admin-ajax.php' ) ); ?>', { method: 'POST', body: fd })
    .then(r => r.json())
    .then(res => {
        if (res.success && res.data.comments) {
            field.value = '';
            story.comments = res.data.comments;
            document.getElementById('hs-viewer-comments-count').textContent = story.comments.length;
            renderHsStoryComments(story.comments);
        }
    });
}

function handleHsStoryCommentKey(e) {
    if (e.key === 'Enter') {
        submitHsStoryComment();
    }
}

function renderHsStoryComments(comments) {
    const list = document.getElementById('hs-viewer-comments-list');
    list.innerHTML = '';
    if (comments.length === 0) {
        list.innerHTML = '<div style="font-size:11px; color:#64748b; text-align:center; padding:6px;">কোনো মন্তব্য নেই। প্রথম মন্তব্যটি করুন!</div>';
        return;
    }
    comments.forEach(c => {
        const item = document.createElement('div');
        item.className = 'hs-story-comment-bubble';
        item.innerHTML = `<img src="${c.user_avatar}" alt="" /><div class="hs-comment-bubble-text"><b>${c.user_name}</b> ${c.text}</div>`;
        list.appendChild(item);
    });
}
</script>
