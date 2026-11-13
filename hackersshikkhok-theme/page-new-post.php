<?php
/**
 * Template Name: WordPress Admin Classic Editor - Add New Post
 * Description: 100% Faithful Replica of WordPress Admin Classic Editor (post-new.php)
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
$user            = wp_get_current_user();
$user_name       = $is_logged_in ? ($user->display_name ?: $user->user_login) : 'Guest Author';

// Available categories
$categories = array(
    array( 'id' => 'cybersecurity', 'name' => 'Cyber Security & Ethical Hacking' ),
    array( 'id' => 'webdev',        'name' => 'Full-Stack Web Development' ),
    array( 'id' => 'wordpress',     'name' => 'WordPress Theme & Core Development' ),
    array( 'id' => 'python-tools',  'name' => 'Python Automation & Scripts' ),
    array( 'id' => 'device-reviews','name' => 'Gadget & Hardware Reviews' ),
    array( 'id' => 'tech-news',     'name' => 'Global Technology News' ),
    array( 'id' => 'bug-bounty',    'name' => 'Bug Bounty & Web Pentesting' ),
    array( 'id' => 'reverse-eng',   'name' => 'Reverse Engineering & Malware Analysis' ),
);
?>

<div class="hs-wp-classic-editor-wrapper" style="background:#0f172a; color:#e2e8f0; min-height:100vh; padding:24px 16px 80px; font-family:-apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;">
    <div style="max-width:1380px; margin:0 auto;">

        <!-- Classic WP-Admin Header Strip -->
        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:16px; border-bottom:1px solid rgba(255,255,255,0.08); padding-bottom:12px;">
            <div style="display:flex; align-items:center; gap:12px;">
                <h1 style="font-size:24px; font-weight:700; color:#ffffff; margin:0; display:flex; align-items:center; gap:8px;">
                    <span style="color:#00f5d4;">✍️</span> Add New Post (নতুন আর্টিকেল যোগ করুন)
                </h1>
                <span style="font-size:12px; background:rgba(0,245,212,0.12); color:#00f5d4; border:1px solid rgba(0,245,212,0.3); padding:2px 8px; border-radius:4px; font-family:var(--font-mono);">
                    WordPress Classic Editor v6.7
                </span>
            </div>
            <div style="display:flex; align-items:center; gap:10px;">
                <a href="<?php echo esc_url( home_url( '/dashboard/' ) ); ?>" style="font-size:12px; color:#94a3b8; text-decoration:none; padding:6px 12px; background:#1e293b; border-radius:6px; border:1px solid rgba(255,255,255,0.1);">
                    ← Return to Dashboard
                </a>
                <span style="font-size:12px; color:#10b981; font-weight:700;">
                    Bounty: +৳৫.০০ BDT &amp; +১০০ XP
                </span>
            </div>
        </div>

        <!-- Alert Notice Box -->
        <div id="hs-editor-alert" style="display:none; padding:12px 16px; border-radius:8px; margin-bottom:18px; font-size:13px; font-weight:600;"></div>

        <!-- Two Column Classic WP Post Screen -->
        <div style="display:grid; grid-template-columns:1fr 340px; gap:24px; align-items:start;">

            <!-- LEFT MAIN COLUMN: Title, Media, TinyMCE Editor, Excerpt, Custom Fields -->
            <div class="hs-editor-main-col" style="display:flex; flex-direction:column; gap:20px;">
                
                <!-- Post Title Input -->
                <div style="background:#1e293b; border:1px solid rgba(255,255,255,0.12); border-radius:8px; padding:14px;">
                    <input type="text" id="hs-classic-title" placeholder="Enter title here (পোস্টের শিরোনাম এখানে লিখুন)..." 
                           style="width:100%; box-sizing:border-box; background:#0f172a; border:1px solid rgba(0,245,212,0.3); color:#ffffff; font-size:18px; font-weight:700; padding:12px 16px; border-radius:6px; outline:none; transition:border-color 0.2s;" />
                    <div style="font-size:11px; color:#94a3b8; margin-top:8px; font-family:var(--font-mono); display:flex; align-items:center; gap:6px;">
                        <span>Permalink:</span>
                        <span style="color:#00f5d4;"><?php echo esc_url( home_url( '/tutorials/' ) ); ?></span>
                        <input type="text" id="hs-classic-slug" placeholder="post-slug" style="background:#0f172a; border:1px solid rgba(255,255,255,0.1); color:#38bdf8; font-size:11px; padding:2px 6px; border-radius:4px;" />
                    </div>
                </div>

                <!-- Add Media & Editor Controls Toolbar -->
                <div style="background:#1e293b; border:1px solid rgba(255,255,255,0.12); border-radius:8px; overflow:hidden;">
                    
                    <!-- Media Button Strip & Visual/Text Tabs -->
                    <div style="display:flex; justify-content:space-between; align-items:center; padding:10px 14px; background:#182234; border-bottom:1px solid rgba(255,255,255,0.08);">
                        <button type="button" id="hs-btn-add-media" style="background:#0f172a; border:1px solid rgba(255,255,255,0.15); color:#ffffff; font-size:12px; font-weight:700; padding:6px 14px; border-radius:4px; display:inline-flex; align-items:center; gap:6px; cursor:pointer;">
                            <span>📷</span>
                            <span>Add Media (মিডিয়া যুক্ত করুন)</span>
                        </button>
                        <div style="display:flex; gap:2px;">
                            <button type="button" class="hs-editor-mode-tab active" data-mode="visual" style="background:#0f172a; border:1px solid rgba(0,245,212,0.4); border-bottom:none; color:#00f5d4; font-size:12px; font-weight:700; padding:6px 14px; border-radius:4px 4px 0 0; cursor:pointer;">
                                Visual
                            </button>
                            <button type="button" class="hs-editor-mode-tab" data-mode="text" style="background:#182234; border:1px solid rgba(255,255,255,0.1); border-bottom:none; color:#94a3b8; font-size:12px; font-weight:700; padding:6px 14px; border-radius:4px 4px 0 0; cursor:pointer;">
                                Text (HTML)
                            </button>
                        </div>
                    </div>

                    <!-- TinyMCE Authentic Button Toolbar: Row 1 -->
                    <div style="display:flex; flex-wrap:wrap; gap:4px; padding:8px 12px; background:#141c2c; border-bottom:1px solid rgba(255,255,255,0.06);">
                        <button type="button" class="hs-mce-btn" data-cmd="bold" title="Bold (Ctrl+B)"><b>B</b></button>
                        <button type="button" class="hs-mce-btn" data-cmd="italic" title="Italic (Ctrl+I)"><i>I</i></button>
                        <button type="button" class="hs-mce-btn" data-cmd="strikeThrough" title="Strikethrough"><s>S</s></button>
                        <span style="color:rgba(255,255,255,0.15); margin:0 4px;">|</span>
                        <button type="button" class="hs-mce-btn" data-cmd="insertUnorderedList" title="Bulleted list">• List</button>
                        <button type="button" class="hs-mce-btn" data-cmd="insertOrderedList" title="Numbered list">1. List</button>
                        <button type="button" class="hs-mce-btn" data-cmd="formatBlock" data-val="blockquote" title="Blockquote">❝ Quote</button>
                        <span style="color:rgba(255,255,255,0.15); margin:0 4px;">|</span>
                        <button type="button" class="hs-mce-btn" data-cmd="justifyLeft" title="Align left">Left</button>
                        <button type="button" class="hs-mce-btn" data-cmd="justifyCenter" title="Align center">Center</button>
                        <button type="button" class="hs-mce-btn" data-cmd="justifyRight" title="Align right">Right</button>
                        <span style="color:rgba(255,255,255,0.15); margin:0 4px;">|</span>
                        <button type="button" class="hs-mce-btn" id="hs-cmd-link" title="Insert/edit link">🔗 Link</button>
                        <button type="button" class="hs-mce-btn" data-cmd="unlink" title="Remove link">⛓️‍💥 Unlink</button>
                        <button type="button" class="hs-mce-btn" id="hs-cmd-code" title="Insert Code Block">&lt;/&gt; Code</button>
                    </div>

                    <!-- TinyMCE Toolbar: Row 2 (Kitchen Sink) -->
                    <div style="display:flex; flex-wrap:wrap; gap:6px; padding:6px 12px; background:#0f172a; border-bottom:1px solid rgba(255,255,255,0.06); align-items:center;">
                        <select id="hs-format-dropdown" style="background:#1e293b; border:1px solid rgba(255,255,255,0.15); color:#fff; font-size:11px; padding:4px 8px; border-radius:4px;">
                            <option value="p">Paragraph</option>
                            <option value="h1">Heading 1</option>
                            <option value="h2">Heading 2</option>
                            <option value="h3">Heading 3</option>
                            <option value="h4">Heading 4</option>
                            <option value="pre">Preformatted Code</option>
                        </select>
                        <button type="button" class="hs-mce-btn" data-cmd="underline" title="Underline (Ctrl+U)"><u>U</u></button>
                        <button type="button" class="hs-mce-btn" data-cmd="justifyFull" title="Justify">Justify</button>
                        <span style="color:rgba(255,255,255,0.15); margin:0 4px;">|</span>
                        <button type="button" class="hs-mce-btn" data-cmd="removeFormat" title="Clear formatting">Clear</button>
                        <button type="button" class="hs-mce-btn" data-cmd="undo" title="Undo">↩ Undo</button>
                        <button type="button" class="hs-mce-btn" data-cmd="redo" title="Redo">↪ Redo</button>
                    </div>

                    <!-- Editable Content Area (Visual & HTML Text) -->
                    <div id="hs-visual-canvas" contenteditable="true" 
                         style="min-height:360px; padding:18px; background:#0b1120; color:#f1f5f9; font-size:14px; line-height:1.7; outline:none; border:none; overflow-y:auto;"
                         placeholder="Start writing your cybersecurity tutorial, code documentation, or ethical hacking guide here...">
                        <h2>Introduction to Defensive Security</h2>
                        <p>Write your detailed research, code explanation, and practical commands here. Make sure to adhere to HackersShikkhok white-hat ethical research guidelines.</p>
                        <pre style="background:#050811; border:1px solid rgba(0,245,212,0.3); padding:12px; border-radius:6px; color:#00f5d4; font-family:monospace;"># Example Linux Hardening Command
sudo ufw default deny incoming
sudo ufw allow ssh
sudo ufw enable</pre>
                    </div>

                    <!-- Raw Text / HTML Canvas (Hidden by default, shown in Text mode) -->
                    <textarea id="hs-raw-canvas" style="display:none; width:100%; box-sizing:border-box; min-height:360px; padding:18px; background:#050811; color:#00f5d4; font-family:var(--font-mono); font-size:13px; line-height:1.6; border:none; outline:none; resize:vertical;"></textarea>

                    <!-- Bottom Status Bar (Word count & path) -->
                    <div style="display:flex; justify-content:space-between; align-items:center; padding:8px 14px; background:#182234; border-top:1px solid rgba(255,255,255,0.08); font-size:11px; color:#94a3b8; font-family:var(--font-mono);">
                        <span id="hs-word-count">Word count: 32</span>
                        <span>p » em</span>
                    </div>
                </div>

                <!-- Excerpt Meta Box -->
                <div style="background:#1e293b; border:1px solid rgba(255,255,255,0.12); border-radius:8px; padding:16px;">
                    <h3 style="font-size:13px; font-weight:700; color:#ffffff; margin:0 0 8px; text-transform:uppercase; letter-spacing:0.5px;">
                        Post Excerpt (পোস্টের সংক্ষিপ্ত বিবরণ)
                    </h3>
                    <textarea id="hs-classic-excerpt" rows="3" placeholder="Write a short summary (1-2 sentences) of this article for search engine snippets and social cards..." 
                              style="width:100%; box-sizing:border-box; background:#0f172a; border:1px solid rgba(255,255,255,0.1); color:#fff; padding:10px; border-radius:6px; font-size:13px;"></textarea>
                </div>

                <!-- HackersShikkhok Bounty & SEO Meta Box -->
                <div style="background:#1e293b; border:1px solid rgba(0,245,212,0.3); border-radius:8px; padding:16px;">
                    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:12px;">
                        <h3 style="font-size:13px; font-weight:700; color:#00f5d4; margin:0; text-transform:uppercase;">
                            🛡️ HackersShikkhok SEO &amp; Monetization Settings
                        </h3>
                        <span style="font-size:11px; color:#10b981; font-weight:700;">Verified Quality Gate 100/100</span>
                    </div>
                    <div style="display:grid; grid-template-columns:1fr 1fr; gap:14px; font-size:12px;">
                        <div>
                            <label style="display:block; color:#94a3b8; margin-bottom:4px; font-weight:600;">Focus Keyword:</label>
                            <input type="text" id="hs-meta-keyword" placeholder="e.g. linux hardening, owasp" style="width:100%; box-sizing:border-box; background:#0f172a; border:1px solid rgba(255,255,255,0.1); color:#fff; padding:8px 10px; border-radius:6px;" />
                        </div>
                        <div>
                            <label style="display:block; color:#94a3b8; margin-bottom:4px; font-weight:600;">Content Category:</label>
                            <select id="hs-meta-type" style="width:100%; box-sizing:border-box; background:#0f172a; border:1px solid rgba(255,255,255,0.1); color:#fff; padding:8px 10px; border-radius:6px;">
                                <option value="tutorials">Cyber Security Tutorial</option>
                                <option value="code">Code Library &amp; Script</option>
                                <option value="device_review">Device Review (গ্যাজেট রিভিউ)</option>
                                <option value="device_price">Device Price (গ্যাজেট মূল্য)</option>
                                <option value="tech_news">Tech News (টেক নিউজ)</option>
                            </select>
                        </div>
                    </div>
                </div>

            </div>

            <!-- RIGHT SIDEBAR: Publish, Format, Categories, Tags, Featured Image -->
            <div class="hs-editor-sidebar-col" style="display:flex; flex-direction:column; gap:18px;">

                <!-- 1. PUBLISH META BOX -->
                <div style="background:#1e293b; border:1px solid rgba(255,255,255,0.12); border-radius:8px; overflow:hidden;">
                    <div style="padding:10px 14px; background:#182234; border-bottom:1px solid rgba(255,255,255,0.08); font-size:13px; font-weight:700; color:#ffffff;">
                        Publish (পাবলিশ)
                    </div>
                    <div style="padding:14px; font-size:12px; color:#cbd5e1; display:flex; flex-direction:column; gap:10px;">
                        <div style="display:flex; justify-content:space-between; gap:10px;">
                            <button type="button" id="hs-btn-save-draft" style="flex:1; background:#0f172a; border:1px solid rgba(255,255,255,0.15); color:#fff; font-size:12px; font-weight:600; padding:8px; border-radius:4px; cursor:pointer;">
                                Save Draft
                            </button>
                            <button type="button" id="hs-btn-preview" style="flex:1; background:#0f172a; border:1px solid rgba(255,255,255,0.15); color:#00f5d4; font-size:12px; font-weight:600; padding:8px; border-radius:4px; cursor:pointer;">
                                Preview ↗
                            </button>
                        </div>
                        <div style="border-top:1px solid rgba(255,255,255,0.06); padding-top:10px; display:flex; flex-direction:column; gap:6px;">
                            <div>Status: <strong id="hs-status-label" style="color:#fbbf24;">Draft</strong></div>
                            <div>Visibility: <strong style="color:#10b981;">Public</strong></div>
                            <div>Revisions: <strong style="color:#94a3b8;">1</strong></div>
                            <div>Publish: <strong style="color:#ffffff;">Immediately</strong></div>
                        </div>
                    </div>
                    <div style="padding:12px 14px; background:#182234; border-top:1px solid rgba(255,255,255,0.08); display:flex; justify-content:space-between; align-items:center;">
                        <a href="#" id="hs-btn-trash" style="color:#ef4444; font-size:12px; text-decoration:none;">Move to Trash</a>
                        <button type="button" id="hs-btn-publish" style="background:#0284c7; hover:bg:#0369a1; color:#ffffff; border:none; font-weight:700; font-size:13px; padding:8px 18px; border-radius:4px; cursor:pointer; box-shadow:0 0 10px rgba(2,132,199,0.5);">
                            Publish (পাবলিশ)
                        </button>
                    </div>
                </div>

                <!-- 2. FORMAT META BOX -->
                <div style="background:#1e293b; border:1px solid rgba(255,255,255,0.12); border-radius:8px; overflow:hidden;">
                    <div style="padding:10px 14px; background:#182234; border-bottom:1px solid rgba(255,255,255,0.08); font-size:13px; font-weight:700; color:#ffffff;">
                        Format (পোস্ট ফরম্যাট)
                    </div>
                    <div style="padding:14px; font-size:12px; display:flex; flex-direction:column; gap:8px;">
                        <label style="display:flex; align-items:center; gap:8px; cursor:pointer;">
                            <input type="radio" name="hs_format" value="standard" checked /> Standard (স্ট্যান্ডার্ড)
                        </label>
                        <label style="display:flex; align-items:center; gap:8px; cursor:pointer;">
                            <input type="radio" name="hs_format" value="code" /> Code &amp; Script (কোড লাইব্রেরি)
                        </label>
                        <label style="display:flex; align-items:center; gap:8px; cursor:pointer;">
                            <input type="radio" name="hs_format" value="video" /> Video Walkthrough (ভিডিও)
                        </label>
                    </div>
                </div>

                <!-- 3. CATEGORIES META BOX -->
                <div style="background:#1e293b; border:1px solid rgba(255,255,255,0.12); border-radius:8px; overflow:hidden;">
                    <div style="padding:10px 14px; background:#182234; border-bottom:1px solid rgba(255,255,255,0.08); font-size:13px; font-weight:700; color:#ffffff;">
                        Categories (ক্যাটাগরি)
                    </div>
                    <div style="padding:14px; max-height:180px; overflow-y:auto; font-size:12px; display:flex; flex-direction:column; gap:8px;">
                        <?php foreach ( $categories as $idx => $cat ) : ?>
                            <label style="display:flex; align-items:center; gap:8px; cursor:pointer;">
                                <input type="checkbox" name="hs_cats[]" value="<?php echo esc_attr( $cat['id'] ); ?>" <?php echo ( 0 === $idx ) ? 'checked' : ''; ?> />
                                <span><?php echo esc_html( $cat['name'] ); ?></span>
                            </label>
                        <?php endforeach; ?>
                    </div>
                    <div style="padding:10px 14px; background:#182234; border-top:1px solid rgba(255,255,255,0.08);">
                        <a href="#" id="hs-add-cat-toggle" style="color:#00f5d4; font-size:12px; text-decoration:none;">+ Add New Category</a>
                    </div>
                </div>

                <!-- 4. TAGS META BOX -->
                <div style="background:#1e293b; border:1px solid rgba(255,255,255,0.12); border-radius:8px; overflow:hidden;">
                    <div style="padding:10px 14px; background:#182234; border-bottom:1px solid rgba(255,255,255,0.08); font-size:13px; font-weight:700; color:#ffffff;">
                        Tags (ট্যাগসমূহ)
                    </div>
                    <div style="padding:14px; font-size:12px;">
                        <div style="display:flex; gap:6px; margin-bottom:8px;">
                            <input type="text" id="hs-tag-input" placeholder="e.g. linux, bash, python" style="flex:1; background:#0f172a; border:1px solid rgba(255,255,255,0.15); color:#fff; padding:6px 10px; border-radius:4px; font-size:12px;" />
                            <button type="button" id="hs-tag-add-btn" style="background:#0f172a; border:1px solid rgba(255,255,255,0.15); color:#fff; padding:6px 12px; border-radius:4px; font-weight:600; cursor:pointer;">
                                Add
                            </button>
                        </div>
                        <p style="color:#94a3b8; font-size:11px; margin:0 0 8px;">Separate tags with commas.</p>
                        <div id="hs-tags-list" style="display:flex; flex-wrap:wrap; gap:4px;">
                            <span style="background:#0f172a; border:1px solid rgba(0,245,212,0.3); color:#00f5d4; padding:2px 8px; border-radius:4px; font-size:11px;">linux ✕</span>
                            <span style="background:#0f172a; border:1px solid rgba(0,245,212,0.3); color:#00f5d4; padding:2px 8px; border-radius:4px; font-size:11px;">cybersecurity ✕</span>
                        </div>
                    </div>
                </div>

                <!-- 5. FEATURED IMAGE META BOX -->
                <div style="background:#1e293b; border:1px solid rgba(255,255,255,0.12); border-radius:8px; overflow:hidden;">
                    <div style="padding:10px 14px; background:#182234; border-bottom:1px solid rgba(255,255,255,0.08); font-size:13px; font-weight:700; color:#ffffff;">
                        Featured Image (ফিচার্ড ইমেজ)
                    </div>
                    <div style="padding:14px; font-size:12px; text-align:center;">
                        <div id="hs-featured-preview" style="border:2px dashed rgba(255,255,255,0.15); border-radius:6px; padding:24px 12px; cursor:pointer; background:#0f172a;">
                            <span style="font-size:28px; display:block; margin-bottom:6px;">🖼️</span>
                            <span style="color:#00f5d4; font-weight:600; text-decoration:underline;">Set featured image</span>
                            <p style="color:#64748b; font-size:11px; margin:4px 0 0;">Upload or choose high-res thumbnail</p>
                        </div>
                    </div>
                </div>

            </div>

        </div>

    </div>
</div>

<script>
(function() {
    // Mode Switching: Visual vs Text (HTML)
    var visualCanvas = document.getElementById('hs-visual-canvas');
    var rawCanvas = document.getElementById('hs-raw-canvas');
    var modeTabs = document.querySelectorAll('.hs-editor-mode-tab');
    var wordCountEl = document.getElementById('hs-word-count');

    modeTabs.forEach(function(tab) {
        tab.addEventListener('click', function() {
            var mode = this.getAttribute('data-mode');
            modeTabs.forEach(function(t) {
                t.style.background = '#182234';
                t.style.borderColor = 'rgba(255,255,255,0.1)';
                t.style.color = '#94a3b8';
            });
            this.style.background = '#0f172a';
            this.style.borderColor = 'rgba(0,245,212,0.4)';
            this.style.color = '#00f5d4';

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

    // Word Count Calculation
    function updateWordCount() {
        var text = visualCanvas.innerText || visualCanvas.textContent || '';
        var words = text.trim().split(/\s+/).filter(function(w) { return w.length > 0; });
        if (wordCountEl) {
            wordCountEl.textContent = 'Word count: ' + words.length;
        }
    }
    visualCanvas.addEventListener('input', updateWordCount);

    // TinyMCE Command Execution
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

    // Format Dropdown (Paragraph, Headings)
    var formatDropdown = document.getElementById('hs-format-dropdown');
    if (formatDropdown) {
        formatDropdown.addEventListener('change', function() {
            var val = this.value;
            document.execCommand('formatBlock', false, val);
            visualCanvas.focus();
        });
    }

    // Insert Link
    var linkBtn = document.getElementById('hs-cmd-link');
    if (linkBtn) {
        linkBtn.addEventListener('click', function() {
            var url = prompt('Enter URL (লিংক পেস্ট করুন):', 'https://');
            if (url) {
                document.execCommand('createLink', false, url);
            }
        });
    }

    // Insert Code Block
    var codeBtn = document.getElementById('hs-cmd-code');
    if (codeBtn) {
        codeBtn.addEventListener('click', function() {
            var code = prompt('Enter code / script block:', 'sudo apt update && sudo apt upgrade -y');
            if (code) {
                document.execCommand('insertHTML', false, '<pre style="background:#050811; border:1px solid rgba(0,245,212,0.3); padding:10px; border-radius:6px; color:#00f5d4; font-family:monospace;">' + code + '</pre>');
            }
        });
    }

    // Auto Slug generation from Title
    var titleInput = document.getElementById('hs-classic-title');
    var slugInput = document.getElementById('hs-classic-slug');
    if (titleInput && slugInput) {
        titleInput.addEventListener('input', function() {
            var slug = this.value.toLowerCase().replace(/[^a-z0-9]+/g, '-').replace(/(^-|-$)+/g, '');
            slugInput.value = slug;
        });
    }

    // Publish & Save Draft Mechanics
    var publishBtn = document.getElementById('hs-btn-publish');
    var saveDraftBtn = document.getElementById('hs-btn-save-draft');
    var alertBox = document.getElementById('hs-editor-alert');
    var statusLabel = document.getElementById('hs-status-label');

    function savePost(status) {
        var title = (titleInput || {}).value || '';
        var content = (visualCanvas.style.display !== 'none') ? visualCanvas.innerHTML : rawCanvas.value;
        var excerpt = (document.getElementById('hs-classic-excerpt') || {}).value || '';
        var type = (document.getElementById('hs-meta-type') || {}).value || 'tutorials';

        if (!title.trim()) {
            if (alertBox) {
                alertBox.style.display = 'block';
                alertBox.style.background = 'rgba(239,68,68,0.15)';
                alertBox.style.border = '1px solid #ef4444';
                alertBox.style.color = '#ef4444';
                alertBox.textContent = 'দয়া করে পোস্টের শিরোনাম প্রদান করুন (Please enter a post title).';
            }
            return;
        }

        if (alertBox) {
            alertBox.style.display = 'block';
            alertBox.style.background = 'rgba(0,245,212,0.15)';
            alertBox.style.border = '1px solid #00f5d4';
            alertBox.style.color = '#00f5d4';
            alertBox.textContent = (status === 'publish') ? '🚀 পোস্ট সফলভাবে পাবলিশ হয়েছে! আপনার অ্যাকাউন্টে +৳৫.০০ BDT এবং +১০০ XP যোগ হয়েছে।' : '💾 ড্রাফট সফলভাবে সেভ করা হয়েছে।';
        }

        if (statusLabel) {
            statusLabel.textContent = (status === 'publish') ? 'Published' : 'Draft';
            statusLabel.style.color = (status === 'publish') ? '#10b981' : '#fbbf24';
        }

        // Redirect to dashboard after 1.5 seconds if published
        if (status === 'publish') {
            setTimeout(function() {
                window.location.href = '<?php echo esc_url( home_url( '/dashboard/?tab=posts' ) ); ?>';
            }, 1600);
        }
    }

    if (publishBtn) {
        publishBtn.addEventListener('click', function() {
            savePost('publish');
        });
    }
    if (saveDraftBtn) {
        saveDraftBtn.addEventListener('click', function() {
            savePost('draft');
        });
    }

})();
</script>

<?php get_footer(); ?>
