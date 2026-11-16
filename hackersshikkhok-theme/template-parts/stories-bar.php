<?php
/**
 * HackersShikkhok - Member Cyber Stories Carousel & Viewer
 * Brand: HackersShikkhok (https://hackersshikkhok.com)
 *
 * STRICT PRIVACY & ROUTE RULE:
 * - Visible ONLY to logged-in authenticated members.
 * - Visible ONLY on the Home Page (is_front_page() || is_home()).
 */
declare(strict_types=1);

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

if ( ! is_user_logged_in() || ( ! is_front_page() && ! is_home() ) ) {
    return;
}

$current_user_id = get_current_user_id();
$current_user    = wp_get_current_user();
$current_uname   = $current_user->display_name ?: $current_user->user_login;

$all_active_stories = class_exists( '\HackersShikkhok\Theme\Modules\Community\StoryEngine' )
    ? \HackersShikkhok\Theme\Modules\Community\StoryEngine::get_active_stories()
    : array();
$hs_story_nonce = wp_create_nonce( 'hs_ajax_nonce' );

$user_stories = array();
foreach ( $all_active_stories as $st ) {
    $uid = (int) ( $st['user_id'] ?? 0 );
    if ( ! isset( $user_stories[ $uid ] ) ) {
        $user_stories[ $uid ] = array();
    }
    $user_stories[ $uid ][] = $st;
}

$community_users = get_users( array(
    'number'  => 15,
    'exclude' => array( $current_user_id ),
) );

$self_has_stories  = isset( $user_stories[ $current_user_id ] );
$self_stories_json = $self_has_stories ? wp_json_encode( $user_stories[ $current_user_id ] ) : '[]';
?>

<div class="hs-messenger-stories-wrap" id="hs-stories-section">
    <div class="hs-stories-scroll-box" id="hs-stories-scroll-box">
        <!-- 1. Self Story Node -->
        <div class="hs-story-circle-item self-node <?php echo $self_has_stories ? 'has-unviewed' : ''; ?>" id="hs-self-story-node" onclick="handleHsSelfStoryClick(event)" title="My Story / Add Story" aria-label="My Story">
            <div class="hs-avatar-ring-wrapper">
                <div class="hs-story-avatar">
                    <?php echo get_avatar( $current_user_id, 36 ); ?>
                </div>
                <div class="hs-create-story-plus" title="Add Story">+</div>
            </div>
        </div>

        <!-- 2. Community Member Stories -->
        <?php foreach ( $community_users as $u ) :
            $u_id         = (int) $u->ID;
            $has_stories  = isset( $user_stories[ $u_id ] );
            $user_st_json = $has_stories ? wp_json_encode( $user_stories[ $u_id ] ) : '[]';
            $u_avatar     = get_avatar_url( $u_id, array( 'size' => 36 ) );
            $u_name       = $u->display_name ?: $u->user_login;
        ?>
        <div class="hs-story-circle-item <?php echo $has_stories ? 'has-unviewed' : 'no-story'; ?>" onclick="openHsStoryViewer(<?php echo $u_id; ?>, '<?php echo esc_js( $u_name ); ?>', '<?php echo esc_url( $u_avatar ); ?>', <?php echo esc_attr( $user_st_json ); ?>)" title="<?php echo esc_attr( $u_name ); ?>" aria-label="<?php echo esc_attr( $u_name ); ?>">
            <div class="hs-avatar-ring-wrapper">
                <div class="hs-story-avatar">
                    <img src="<?php echo esc_url( $u_avatar ); ?>" alt="<?php echo esc_attr( $u_name ); ?>" loading="lazy" />
                </div>
                <?php if ( $has_stories ) : ?>
                    <span class="hs-active-pulse-dot" title="Active Story"></span>
                <?php endif; ?>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
</div>

<!-- Immersive Story Viewer Modal -->
<div id="hs-story-viewer-modal" class="hs-story-viewer-modal" style="display: none;">
    <div class="hs-viewer-overlay" onclick="closeHsStoryViewer()"></div>
    <div class="hs-viewer-content">
        <button type="button" class="hs-close-viewer-btn" onclick="closeHsStoryViewer()" aria-label="Close Story">✕</button>
        <div class="hs-viewer-progress-header" id="hs-viewer-progress-bars"></div>

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

        <div class="hs-viewer-main-body" id="hs-viewer-media-canvas" onclick="advanceHsStorySegment()"></div>
        <div class="hs-viewer-caption-box" id="hs-viewer-caption-box" style="display: none;"></div>

        <div class="hs-viewer-comments-panel" id="hs-viewer-comments-panel">
            <div class="hs-comments-panel-title">💬 Comments (<span id="hs-viewer-comments-count">0</span>)</div>
            <div class="hs-comments-scrollable-area" id="hs-viewer-comments-list"></div>
        </div>

        <div class="hs-viewer-interactions-dock">
            <div class="hs-reactions-flyout-container">
                <button type="button" class="hs-react-trigger-btn" onclick="toggleHsReactionsMenu()">❤️ React</button>
                <div class="hs-reactions-popover" id="hs-reactions-popover">
                    <span class="hs-react-em" onclick="submitHsStoryReaction('❤️')">❤️</span>
                    <span class="hs-react-em" onclick="submitHsStoryReaction('🔥')">🔥</span>
                    <span class="hs-react-em" onclick="submitHsStoryReaction('👏')">👏</span>
                    <span class="hs-react-em" onclick="submitHsStoryReaction('😮')">😮</span>
                    <span class="hs-react-em" onclick="submitHsStoryReaction('🛡️')">🛡️</span>
                </div>
            </div>
            <div class="hs-comment-input-row">
                <input type="text" id="hs-story-comment-field" placeholder="Write a comment..." onkeypress="handleHsStoryCommentKey(event)" />
                <button type="button" class="hs-story-send-comment-btn" onclick="submitHsStoryComment()">Send</button>
            </div>
        </div>
    </div>
</div>

<!-- Create Story Modal -->
<div id="hs-create-story-modal" class="hs-create-story-modal-overlay" style="display: none;">
    <div class="hs-create-story-box">
        <div class="hs-create-story-header">
            <h3>⚡ Publish New Cyber Story</h3>
            <button type="button" class="hs-close-create-btn" onclick="closeHsCreateStoryModal()" aria-label="Close">✕</button>
        </div>

        <div class="hs-create-tabs">
            <button type="button" class="hs-story-tab-btn active" id="hs-tab-btn-text" onclick="switchHsStoryTab('text')">✍️ Text Story</button>
            <button type="button" class="hs-story-tab-btn" id="hs-tab-btn-media" onclick="switchHsStoryTab('media')">📸 Photo / Video</button>
        </div>

        <form id="hs-story-upload-form" onsubmit="handleHsStoryUploadForm(event)">
            <div class="hs-tab-pane active" id="hs-pane-text">
                <div class="hs-story-form-group">
                    <textarea id="hs-story-text-content" rows="4" class="hs-story-input" placeholder="Share your coding or cybersecurity update..."></textarea>
                </div>
                <div class="hs-story-form-group">
                    <label class="hs-story-label">Select Background Theme:</label>
                    <div class="hs-bg-grad-selector-grid">
                        <div class="hs-grad-swatch active" style="background: linear-gradient(135deg, #00f5d4, #0077b6);" onclick="selectHsStoryGrad(this, 'linear-gradient(135deg, #00f5d4, #0077b6)')"></div>
                        <div class="hs-grad-swatch" style="background: linear-gradient(135deg, #ff007f, #7b2cbf);" onclick="selectHsStoryGrad(this, 'linear-gradient(135deg, #ff007f, #7b2cbf)')"></div>
                        <div class="hs-grad-swatch" style="background: linear-gradient(135deg, #10b981, #050811);" onclick="selectHsStoryGrad(this, 'linear-gradient(135deg, #10b981, #050811)')"></div>
                        <div class="hs-grad-swatch" style="background: linear-gradient(135deg, #f59e0b, #ef4444);" onclick="selectHsStoryGrad(this, 'linear-gradient(135deg, #f59e0b, #ef4444)')"></div>
                        <div class="hs-grad-swatch" style="background: linear-gradient(135deg, #3b82f6, #1e1b4b);" onclick="selectHsStoryGrad(this, 'linear-gradient(135deg, #3b82f6, #1e1b4b)')"></div>
                    </div>
                </div>
            </div>

            <div class="hs-tab-pane" id="hs-pane-media">
                <div class="hs-story-form-group">
                    <label class="hs-story-file-uploader-zone" for="hs-story-file-input">
                        <span style="font-size: 28px;">📷</span>
                        <p id="hs-file-chosen-label">Select an image or video clip from device</p>
                        <input type="file" id="hs-story-file-input" name="story_file" accept="image/*,video/*" style="display: none;" onchange="handleHsStoryFileChange(this)" />
                    </label>
                </div>
                <div class="hs-story-form-group">
                    <input type="url" id="hs-story-media-url-field" class="hs-story-input" placeholder="Or paste a direct image/video URL..." />
                </div>
                <div class="hs-story-form-group">
                    <input type="text" id="hs-story-caption-field" class="hs-story-input" placeholder="Story caption (optional)..." />
                </div>
            </div>

            <button type="submit" id="hs-submit-story-btn" class="hs-story-publish-btn">
                🚀 Publish Story (Earn +5 XP)
            </button>
        </form>
    </div>
</div>

<script>
var hsCurrentStoryPack = [];
var hsCurrentStoryIndex = 0;
var hsStoryTimer = null;
var hsActiveMediaType = 'text';
var hsSelectedGrad = 'linear-gradient(135deg, #00f5d4, #0077b6)';

function handleHsSelfStoryClick(e) {
    e.preventDefault();
    var selfStories = <?php echo $self_stories_json; ?>;
    if (selfStories && selfStories.length > 0) {
        openHsStoryViewer(<?php echo (int) $current_user_id; ?>, '<?php echo esc_js( $current_uname ); ?>', '<?php echo esc_url( get_avatar_url( $current_user_id, array( 'size' => 48 ) ) ); ?>', selfStories);
    } else {
        openHsCreateStoryModal();
    }
}

function openHsCreateStoryModal() {
    var m = document.getElementById('hs-create-story-modal');
    if (m) m.style.display = 'flex';
}

function closeHsCreateStoryModal() {
    var m = document.getElementById('hs-create-story-modal');
    if (m) m.style.display = 'none';
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
    document.querySelectorAll('.hs-grad-swatch').forEach(function(s) { s.classList.remove('active'); });
    elem.classList.add('active');
}

function handleHsStoryFileChange(input) {
    if (input.files && input.files[0]) {
        document.getElementById('hs-file-chosen-label').textContent = '✅ Selected: ' + input.files[0].name;
    }
}

function handleHsStoryUploadForm(e) {
    e.preventDefault();
    var btn = document.getElementById('hs-submit-story-btn');
    btn.disabled = true;
    btn.textContent = 'Publishing... Please wait.';

    var formData = new FormData();
    formData.append('action', 'hs_upload_story');
    formData.append('nonce', '<?php echo esc_js( $hs_story_nonce ); ?>');
    formData.append('media_type', hsActiveMediaType);

    if (hsActiveMediaType === 'text') {
        formData.append('caption', document.getElementById('hs-story-text-content').value);
        formData.append('bg_gradient', hsSelectedGrad);
    } else {
        formData.append('caption', document.getElementById('hs-story-caption-field').value);
        var fileInput = document.getElementById('hs-story-file-input');
        if (fileInput.files.length > 0) {
            formData.append('story_file', fileInput.files[0]);
        }
        formData.append('media_url', document.getElementById('hs-story-media-url-field').value);
    }

    fetch('<?php echo esc_url( admin_url( 'admin-ajax.php' ) ); ?>', {
        method: 'POST',
        body: formData
    })
    .then(function(r) { return r.json(); })
    .then(function(res) {
        if (res && res.success) {
            window.location.reload();
        } else {
            btn.disabled = false;
            btn.textContent = '🚀 Publish Story (Earn +5 XP)';
        }
    })
    .catch(function() {
        btn.disabled = false;
        btn.textContent = '🚀 Publish Story (Earn +5 XP)';
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
    var story = hsCurrentStoryPack[hsCurrentStoryIndex];

    var fd = new FormData();
    fd.append('action', 'hs_view_story');
    fd.append('nonce', '<?php echo esc_js( $hs_story_nonce ); ?>');
    fd.append('story_id', story.id);
    fetch('<?php echo esc_url( admin_url( 'admin-ajax.php' ) ); ?>', { method: 'POST', body: fd });

    document.getElementById('hs-viewer-views-count').textContent = (story.views || []).length;
    var sDate = new Date((story.created_at || Date.now() / 1000) * 1000);
    document.getElementById('hs-viewer-story-time').textContent = sDate.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });

    var barWrap = document.getElementById('hs-viewer-progress-bars');
    barWrap.innerHTML = '';
    hsCurrentStoryPack.forEach(function(_, idx) {
        var track = document.createElement('div');
        track.className = 'hs-prog-track';
        var fill = document.createElement('div');
        fill.className = 'hs-prog-fill';
        if (idx < hsCurrentStoryIndex) fill.style.width = '100%';
        track.appendChild(fill);
        barWrap.appendChild(track);
    });

    var canvas = document.getElementById('hs-viewer-media-canvas');
    canvas.innerHTML = '';
    var capBox = document.getElementById('hs-viewer-caption-box');

    if (story.media_type === 'text') {
        var txt = document.createElement('div');
        txt.className = 'hs-text-story-canvas';
        txt.style.background = story.bg_gradient || 'linear-gradient(135deg, #00f5d4, #7b2cbf)';
        txt.textContent = story.caption || '';
        canvas.appendChild(txt);
        capBox.style.display = 'none';
    } else if (story.media_type === 'video' && story.media_url) {
        var vid = document.createElement('video');
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
        var img = document.createElement('img');
        img.src = story.media_url;
        canvas.appendChild(img);
        if (story.caption) {
            capBox.textContent = story.caption;
            capBox.style.display = 'block';
        } else {
            capBox.style.display = 'none';
        }
    }

    document.getElementById('hs-viewer-comments-count').textContent = (story.comments || []).length;
    renderHsStoryComments(story.comments || []);

    var activeFill = barWrap.children[hsCurrentStoryIndex].querySelector('.hs-prog-fill');
    var start = null;
    var duration = story.media_type === 'text' ? 5000 : 7000;

    function step(ts) {
        if (!start) start = ts;
        var elapsed = ts - start;
        var pct = Math.min(100, (elapsed / duration) * 100);
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
    var pop = document.getElementById('hs-reactions-popover');
    pop.style.display = pop.style.display === 'flex' ? 'none' : 'flex';
}

function submitHsStoryReaction(type) {
    var story = hsCurrentStoryPack[hsCurrentStoryIndex];
    if (!story) return;
    document.getElementById('hs-reactions-popover').style.display = 'none';

    var fd = new FormData();
    fd.append('action', 'hs_react_story');
    fd.append('nonce', '<?php echo esc_js( $hs_story_nonce ); ?>');
    fd.append('story_id', story.id);
    fd.append('react_type', type);
    fetch('<?php echo esc_url( admin_url( 'admin-ajax.php' ) ); ?>', { method: 'POST', body: fd });
}

function submitHsStoryComment() {
    var story = hsCurrentStoryPack[hsCurrentStoryIndex];
    var field = document.getElementById('hs-story-comment-field');
    var val = field.value.trim();
    if (!story || !val) return;

    var fd = new FormData();
    fd.append('action', 'hs_comment_story');
    fd.append('nonce', '<?php echo esc_js( $hs_story_nonce ); ?>');
    fd.append('story_id', story.id);
    fd.append('comment_text', val);

    fetch('<?php echo esc_url( admin_url( 'admin-ajax.php' ) ); ?>', { method: 'POST', body: fd })
    .then(function(r) { return r.json(); })
    .then(function(res) {
        if (res && res.success && res.data.comments) {
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
    var list = document.getElementById('hs-viewer-comments-list');
    list.innerHTML = '';
    if (comments.length === 0) {
        list.innerHTML = '<div style="font-size:11px; color:#64748b; text-align:center; padding:6px;">No comments yet. Be the first to comment!</div>';
        return;
    }
    comments.forEach(function(c) {
        var item = document.createElement('div');
        item.className = 'hs-story-comment-bubble';
        var img = document.createElement('img');
        img.src = c.user_avatar || '';
        img.alt = '';
        var box = document.createElement('div');
        box.className = 'hs-comment-bubble-text';
        var b = document.createElement('b');
        b.textContent = c.user_name || 'User';
        box.appendChild(b);
        box.appendChild(document.createTextNode(' ' + (c.text || '')));
        item.appendChild(img);
        item.appendChild(box);
        list.appendChild(item);
    });
}
</script>
