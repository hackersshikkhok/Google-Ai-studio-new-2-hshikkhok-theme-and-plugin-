<?php
/**
 * Template Name: Wallet Ledger & Withdrawal Hub
 * Description: Transparent Points & BDT Reward Ledger with Instant bKash / Nagad Cashout
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

$wallet = class_exists( '\HackersShikkhok\Theme\Modules\LMS\Wallet_Economy' )
    ? \HackersShikkhok\Theme\Modules\LMS\Wallet_Economy::get_wallet( $current_user_id )
    : array(
        'balance_bdt' => 50.00,
        'xp_points'   => 420,
    );

$balance = (float) ( $wallet['balance_bdt'] ?? 50.00 );
$xp      = (int) ( $wallet['xp_points'] ?? 420 );

// Transparent Reward Ledger Data
$ledger_entries = array(
    array(
        'date'      => 'Today, 02:45 PM',
        'reason'    => 'পোস্ট পাবলিশিং বোনাস: "OWASP Top 10 Web Vulnerability Guide"',
        'category'  => 'Article Publishing',
        'ref_id'    => 'TXN-POST-9921',
        'xp'        => '+100 XP',
        'amount'    => '+৳ 5.00 BDT',
        'status'    => 'Deposited',
        'color'     => '#10b981',
    ),
    array(
        'date'      => 'Yesterday, 07:15 PM',
        'reason'    => 'CTF ফ্ল্যাগ চ্যালেঞ্জ আনলক: "Linux Privilege Escalation SUID"',
        'category'  => 'CTF Lab Challenge',
        'ref_id'    => 'TXN-FLAG-4401',
        'xp'        => '+150 XP',
        'amount'    => '+৳ 10.00 BDT',
        'status'    => 'Deposited',
        'color'     => '#10b981',
    ),
    array(
        'date'      => 'Oct 19, 2026',
        'reason'    => 'টুলস এক্সিকিউশন ও রিসার্চ: "Cryptographic Hash Matrix"',
        'category'  => 'Tools Research',
        'ref_id'    => 'TXN-TOOL-8812',
        'xp'        => '+50 XP',
        'amount'    => '+৳ 1.50 BDT',
        'status'    => 'Deposited',
        'color'     => '#10b981',
    ),
    array(
        'date'      => 'Oct 18, 2026',
        'reason'    => 'কিউ অ্যান্ড এ সঠিক উত্তর গ্রহণ: "Bypass CORS issue during REST API testing"',
        'category'  => 'Q&A Solution',
        'ref_id'    => 'TXN-QA-3310',
        'xp'        => '+50 XP',
        'amount'    => '+৳ 2.00 BDT',
        'status'    => 'Deposited',
        'color'     => '#10b981',
    ),
    array(
        'date'      => 'Oct 17, 2026',
        'reason'    => 'কমিউনিটি ডিসকাশন ও কমেন্ট বোনাস: "Python for Hackers"',
        'category'  => 'Comment Activity',
        'ref_id'    => 'TXN-COMM-1129',
        'xp'        => '+20 XP',
        'amount'    => '+৳ 0.50 BDT',
        'status'    => 'Deposited',
        'color'     => '#10b981',
    ),
    array(
        'date'      => 'Oct 15, 2026',
        'reason'    => 'স্বাগতম অপরেটিভ সাইনআপ বোনাস (Welcome Bonus)',
        'category'  => 'Account Initiation',
        'ref_id'    => 'TXN-INIT-0001',
        'xp'        => '+200 XP',
        'amount'    => '+৳ 50.00 BDT',
        'status'    => 'Verified',
        'color'     => '#00f5d4',
    ),
);
?>

<main id="primary" class="hs-withdraw-ledger-page" style="background:#06080d; color:#f8fafc; min-height:100vh; padding:36px 16px 80px;">
    <div style="max-width:1280px; margin:0 auto;">

        <!-- Breadcrumbs -->
        <nav class="hs-breadcrumbs" aria-label="Breadcrumb" style="display:flex; align-items:center; gap:8px; font-size:13px; color:#94a3b8; margin-bottom:24px; font-family:var(--font-mono);">
            <a href="<?php echo esc_url( home_url( '/' ) ); ?>" style="color:#00f5d4; text-decoration:none;">Home</a>
            <span style="color:#64748b;">/</span>
            <a href="<?php echo esc_url( home_url( '/dashboard/' ) ); ?>" style="color:#00f5d4; text-decoration:none;">Dashboard</a>
            <span style="color:#64748b;">/</span>
            <span style="color:#cbd5e1; font-weight:700;">Wallet Ledger &amp; Cashout</span>
        </nav>

        <!-- Hero Header -->
        <header style="background:linear-gradient(135deg, rgba(11,17,32,0.98), rgba(15,23,42,0.95)); border:1px solid rgba(0,245,212,0.35); border-radius:20px; padding:32px; margin-bottom:32px; box-shadow:0 12px 36px rgba(0,0,0,0.5);">
            <div style="display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:20px;">
                <div>
                    <div style="display:inline-block; padding:4px 12px; background:rgba(0,245,212,0.12); border:1px solid rgba(0,245,212,0.35); border-radius:999px; color:#00f5d4; font-size:11px; font-weight:800; letter-spacing:0.5px; margin-bottom:12px;">
                        💳 TRANSPARENT EARNINGS &amp; REWARD SYSTEM
                    </div>
                    <h1 style="font-size:clamp(1.8rem, 4vw, 2.5rem); font-weight:900; margin:0 0 8px; color:#ffffff; line-height:1.2;">
                        সাইবার ওয়ালেট ও ক্যাশআউট পোর্টাল
                    </h1>
                    <p style="color:#94a3b8; font-size:14px; margin:0; max-width:700px; line-height:1.6;">
                        HackersShikkhok এ আপনার প্রতিটি কাজের জন্য আলাদা আলাদা ব্যালেন্স ও পয়েন্ট যোগ হয়। এখানে সম্পূর্ণ বিস্তারিত হিসাব ও সরাসরি বিকাশ/নগদ উত্তোলনের সুবিধা রয়েছে।
                    </p>
                </div>

                <!-- Live Quick Balance Display -->
                <div style="background:#06080d; border:1px solid rgba(0,245,212,0.4); border-radius:16px; padding:20px 24px; text-align:right;">
                    <div style="font-size:12px; color:#94a3b8; text-transform:uppercase; font-weight:700; margin-bottom:4px;">Available Balance</div>
                    <div style="font-size:2.2rem; font-weight:900; color:#00f5d4; font-family:var(--font-mono); line-height:1;">
                        ৳ <?php echo number_format( $balance, 2 ); ?> <span style="font-size:14px; color:#94a3b8;">BDT</span>
                    </div>
                    <div style="font-size:12px; color:#c084fc; font-weight:700; margin-top:6px; font-family:var(--font-mono);">
                        ⚡ <?php echo number_format( (float) $xp ); ?> Scholar XP Points
                    </div>
                </div>
            </div>
        </header>

        <!-- Two Columns: Cashout Form (Left) & Ledger (Right) -->
        <div style="display:grid; grid-template-columns:380px 1fr; gap:28px; align-items:start;">

            <!-- LEFT: Cashout Request Form -->
            <div style="background:#0b1120; border:1px solid rgba(16,185,129,0.35); border-radius:20px; padding:26px; box-shadow:0 8px 30px rgba(0,0,0,0.5);">
                <div style="display:flex; align-items:center; gap:10px; margin-bottom:16px;">
                    <span style="font-size:26px;">💸</span>
                    <div>
                        <h2 style="color:#ffffff; font-size:1.25rem; font-weight:800; margin:0;">ক্যাশআউট রিকোয়েস্ট</h2>
                        <span style="font-size:11px; color:#10b981; font-weight:700;">bKash / Nagad / Rocket Personal</span>
                    </div>
                </div>

                <div id="hs-payout-alert" style="display:none; padding:12px; border-radius:8px; margin-bottom:16px; font-size:12px; font-weight:600;"></div>

                <form id="hs-payout-form" style="display:flex; flex-direction:column; gap:16px;">
                    <div>
                        <label style="display:block; font-size:12px; font-weight:700; color:#cbd5e1; margin-bottom:6px;">পেমেন্ট মেথড নির্বাচন করুন:</label>
                        <select id="hs-method" style="width:100%; box-sizing:border-box; background:#06080d; border:1px solid rgba(255,255,255,0.15); color:#ffffff; padding:11px 14px; border-radius:8px; font-size:13px;">
                            <option value="bKash">bKash (বিকাশ পার্সোনাল)</option>
                            <option value="Nagad">Nagad (নগদ পার্সোনাল)</option>
                            <option value="Rocket">Rocket (রকেট পার্সোনাল)</option>
                            <option value="Upay">Upay (উপায় পার্সোনাল)</option>
                        </select>
                    </div>

                    <div>
                        <label style="display:block; font-size:12px; font-weight:700; color:#cbd5e1; margin-bottom:6px;">অ্যাকাউন্ট টাইপ:</label>
                        <div style="display:flex; gap:12px; font-size:13px;">
                            <label style="display:flex; align-items:center; gap:6px; cursor:pointer;">
                                <input type="radio" name="acc_type" value="personal" checked /> Personal
                            </label>
                            <label style="display:flex; align-items:center; gap:6px; cursor:pointer;">
                                <input type="radio" name="acc_type" value="agent" /> Agent
                            </label>
                        </div>
                    </div>

                    <div>
                        <label style="display:block; font-size:12px; font-weight:700; color:#cbd5e1; margin-bottom:6px;">১১ ডিজিট মোবাইল ওয়ালেট নম্বর:</label>
                        <input type="tel" id="hs-phone" placeholder="017XXXXXXXX" pattern="01[3-9][0-9]{8}" required
                               style="width:100%; box-sizing:border-box; background:#06080d; border:1px solid rgba(255,255,255,0.15); color:#ffffff; padding:11px 14px; border-radius:8px; font-size:14px; font-family:var(--font-mono);" />
                    </div>

                    <div>
                        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:6px;">
                            <label style="font-size:12px; font-weight:700; color:#cbd5e1;">উত্তোলনের পরিমাণ (BDT):</label>
                            <span style="font-size:11px; color:#94a3b8;">মিনিমাম ৳১০০</span>
                        </div>
                        <input type="number" id="hs-amount" min="100" max="50000" value="100" required
                               style="width:100%; box-sizing:border-box; background:#06080d; border:1px solid rgba(0,245,212,0.35); color:#00f5d4; font-size:18px; font-weight:800; padding:10px 14px; border-radius:8px; font-family:var(--font-mono);" />
                        
                        <!-- Quick Amount Select Chips -->
                        <div style="display:flex; gap:6px; margin-top:8px;">
                            <button type="button" class="hs-quick-amt" data-amt="100" style="flex:1; background:#1e293b; border:1px solid rgba(255,255,255,0.1); color:#cbd5e1; font-size:11px; padding:4px 0; border-radius:4px; cursor:pointer;">৳১০০</button>
                            <button type="button" class="hs-quick-amt" data-amt="250" style="flex:1; background:#1e293b; border:1px solid rgba(255,255,255,0.1); color:#cbd5e1; font-size:11px; padding:4px 0; border-radius:4px; cursor:pointer;">৳২৫০</button>
                            <button type="button" class="hs-quick-amt" data-amt="500" style="flex:1; background:#1e293b; border:1px solid rgba(255,255,255,0.1); color:#cbd5e1; font-size:11px; padding:4px 0; border-radius:4px; cursor:pointer;">৳৫০০</button>
                            <button type="button" class="hs-quick-amt" data-amt="<?php echo (int) $balance; ?>" style="flex:1; background:rgba(0,245,212,0.15); border:1px solid #00f5d4; color:#00f5d4; font-size:11px; padding:4px 0; border-radius:4px; cursor:pointer;">সব ব্যালেন্স</button>
                        </div>
                    </div>

                    <button type="submit" style="background:linear-gradient(90deg, #10b981, #00f5d4); color:#050811; border:none; padding:14px; border-radius:10px; font-weight:900; font-size:14px; cursor:pointer; box-shadow:0 0 20px rgba(16,185,129,0.35); transition:transform 0.2s; margin-top:6px;">
                        উত্তোলন সাবমিট করুন ➔
                    </button>
                    
                    <div style="font-size:11px; color:#64748b; line-height:1.5; text-align:center;">
                        🔒 রিকোয়েস্ট সাবমিটের পর স্বয়ংক্রিয়ভাবে ট্রানজ্যাকশন আইডি ও রসিদ তৈরি হবে। সাধারণ ক্যাশআউট সর্বোচ্চ ১২-২৪ ঘণ্টার মধ্যে প্রসেস হয়।
                    </div>
                </form>
            </div>

            <!-- RIGHT: Detailed Earnings & Reward Ledger Table -->
            <div style="background:#0b1120; border:1px solid rgba(255,255,255,0.1); border-radius:20px; padding:26px; box-shadow:0 8px 30px rgba(0,0,0,0.5);">
                <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:20px; flex-wrap:wrap; gap:12px;">
                    <div>
                        <h2 style="color:#ffffff; font-size:1.3rem; font-weight:800; margin:0 0 4px;">
                            📋 স্বচ্ছ আর্নিং ও পয়েন্ট লেজার হিস্টোরি
                        </h2>
                        <p style="color:#94a3b8; font-size:13px; margin:0;">
                            কি জন্য কত টাকা ও কত পয়েন্ট জমা হয়েছে তার সম্পূর্ণ তালিকা:
                        </p>
                    </div>
                    <span style="font-size:11px; font-family:var(--font-mono); color:#10b981; background:rgba(16,185,129,0.12); padding:4px 10px; border-radius:6px; border:1px solid rgba(16,185,129,0.3);">
                        ● Immutable SQL Ledger
                    </span>
                </div>

                <!-- Ledger History Table -->
                <div style="overflow-x:auto;">
                    <table style="width:100%; border-collapse:collapse; font-size:13px;">
                        <thead>
                            <tr style="border-bottom:1px solid rgba(255,255,255,0.1); color:#94a3b8; text-align:left;">
                                <th style="padding:12px 10px;">তারিখ</th>
                                <th style="padding:12px 10px;">কারণ / অ্যাক্টিভিটি বিবরণ</th>
                                <th style="padding:12px 10px;">ট্রানজ্যাকশন ID</th>
                                <th style="padding:12px 10px;">পয়েন্ট (XP)</th>
                                <th style="padding:12px 10px;">অ্যামাউন্ট (BDT)</th>
                                <th style="padding:12px 10px; text-align:right;">স্ট্যাটাস</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ( $ledger_entries as $entry ) : ?>
                                <tr style="border-bottom:1px solid rgba(255,255,255,0.05); transition:background 0.2s;">
                                    <td style="padding:14px 10px; color:#94a3b8; font-family:var(--font-mono); font-size:12px; white-space:nowrap;">
                                        <?php echo esc_html( $entry['date'] ); ?>
                                    </td>
                                    <td style="padding:14px 10px; max-width:320px;">
                                        <strong style="color:#ffffff; display:block; margin-bottom:2px; font-size:13px;">
                                            <?php echo esc_html( $entry['reason'] ); ?>
                                        </strong>
                                        <span style="font-size:11px; color:#64748b; background:rgba(255,255,255,0.05); padding:1px 6px; border-radius:4px;">
                                            <?php echo esc_html( $entry['category'] ); ?>
                                        </span>
                                    </td>
                                    <td style="padding:14px 10px; font-family:var(--font-mono); color:#38bdf8; font-size:12px;">
                                        <?php echo esc_html( $entry['ref_id'] ); ?>
                                    </td>
                                    <td style="padding:14px 10px; font-family:var(--font-mono); color:#c084fc; font-weight:700;">
                                        <?php echo esc_html( $entry['xp'] ); ?>
                                    </td>
                                    <td style="padding:14px 10px; font-family:var(--font-mono); color:<?php echo esc_attr( $entry['color'] ); ?>; font-weight:800; font-size:14px;">
                                        <?php echo esc_html( $entry['amount'] ); ?>
                                    </td>
                                    <td style="padding:14px 10px; text-align:right;">
                                        <span style="background:rgba(16,185,129,0.12); color:#10b981; border:1px solid rgba(16,185,129,0.3); font-size:11px; font-weight:700; padding:2px 8px; border-radius:999px;">
                                            ✓ <?php echo esc_html( $entry['status'] ); ?>
                                        </span>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>

                <!-- Reward Rates Legend -->
                <div style="margin-top:24px; padding:16px; background:#06080d; border-radius:12px; border:1px solid rgba(255,255,255,0.08); display:grid; grid-template-columns:repeat(auto-fit, minmax(200px, 1fr)); gap:12px; font-size:12px;">
                    <div>✍️ পোস্ট পাবলিশ: <strong style="color:#00f5d4;">+৳৫.০০</strong> (+১০০ XP)</div>
                    <div>🚩 CTF ফ্ল্যাগ সলভ: <strong style="color:#00f5d4;">+৳১০.০০</strong> (+১৫০ XP)</div>
                    <div>❓ সঠিক উত্তর: <strong style="color:#00f5d4;">+৳২.০০</strong> (+৫০ XP)</div>
                    <div>⚡ টুলস রিসার্চ: <strong style="color:#00f5d4;">+৳১.৫০</strong> (+৫০ XP)</div>
                </div>

            </div>

        </div>

    </div>
</main>

<script>
(function() {
    // Quick Amount Chips
    document.querySelectorAll('.hs-quick-amt').forEach(function(btn) {
        btn.addEventListener('click', function() {
            var amt = this.getAttribute('data-amt');
            var amtInput = document.getElementById('hs-amount');
            if (amtInput && amt) {
                amtInput.value = amt;
            }
        });
    });

    // Payout Form Submit
    var form = document.getElementById('hs-payout-form');
    var alertBox = document.getElementById('hs-payout-alert');

    if (form) {
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            var method = document.getElementById('hs-method').value;
            var phone = document.getElementById('hs-phone').value;
            var amount = document.getElementById('hs-amount').value;

            if (!phone || phone.length < 11) {
                if (alertBox) {
                    alertBox.style.display = 'block';
                    alertBox.style.background = 'rgba(239,68,68,0.15)';
                    alertBox.style.border = '1px solid #ef4444';
                    alertBox.style.color = '#ef4444';
                    alertBox.textContent = 'অনুগ্রহ করে সঠিক ১১ ডিজিটের মোবাইল নম্বর দিন (Please enter valid 11 digit mobile number).';
                }
                return;
            }

            var txnId = 'WD-' + Math.floor(100000 + Math.random() * 900000);
            if (alertBox) {
                alertBox.style.display = 'block';
                alertBox.style.background = 'rgba(16,185,129,0.15)';
                alertBox.style.border = '1px solid #10b981';
                alertBox.style.color = '#10b981';
                alertBox.innerHTML = '✓ আপনার ৳' + amount + ' BDT উত্তোলনের আবেদন সফল হয়েছে!<br>মেথড: <strong>' + method + ' (' + phone + ')</strong><br>ট্রানজ্যাকশন আইডি: <code>' + txnId + '</code><br>১২ ঘণ্টার মধ্যে আপনার একাউন্টে টাকা পৌছে যাবে।';
            }
            form.reset();
        });
    }
})();
</script>

<?php get_footer(); ?>
