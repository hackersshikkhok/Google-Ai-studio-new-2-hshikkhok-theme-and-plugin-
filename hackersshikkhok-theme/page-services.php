<?php
/**
 * Template Name: Cyber Defense & Professional Services
 * Description: Enterprise cybersecurity, VAPT, WordPress malware recovery & audit services hub.
 * Package: HackersShikkhok Theme
 */

declare(strict_types=1);

get_header();
?>
<main id="primary" class="hs-services-page" style="background:#050811; color:#f8fafc; min-height:100vh; padding:40px 20px;">
    <div style="margin:0 auto; max-width:1200px;">
        <!-- Header Hero -->
        <div style="text-align:center; margin-bottom:50px;">
            <div style="display:inline-block; padding:6px 16px; border-radius:9999px; background:rgba(0,245,212,0.1); border:1px solid rgba(0,245,212,0.3); color:#00f5d4; font-size:12px; font-weight:800; text-transform:uppercase; letter-spacing:1px; margin-bottom:15px;">
                🛡️ Enterprise-Grade Cyber Defense &amp; Security Services
            </div>
            <h1 style="font-size:36px; font-weight:900; background:linear-gradient(to right, #00f5d4, #7c3aed, #ff3366); -webkit-background-clip:text; -webkit-text-fill-color:transparent; margin-bottom:15px;">
                HackersShikkhok Professional Security Services Hub
            </h1>
            <p style="font-size:16px; color:#94a3b8; max-width:750px; margin:0 auto; line-height:1.7;">
                Safeguard your enterprise infrastructure, WordPress ecosystem, cloud deployments, and web applications against modern cyber threats with certified white-hat security engineers.
            </p>
        </div>

        <!-- 6 Core Services Grid -->
        <div style="display:grid; grid-template-columns:repeat(auto-fit, minmax(320px, 1fr)); gap:24px; margin-bottom:60px;">
            <!-- Service 1 -->
            <div style="background:#0b1120; border:1px solid rgba(0,245,212,0.25); border-radius:16px; padding:28px; box-shadow:0 10px 30px rgba(0,0,0,0.5);">
                <div style="font-size:32px; margin-bottom:12px;">🔍</div>
                <h3 style="font-size:20px; font-weight:800; color:#00f5d4; margin-bottom:10px;">VAPT (Vulnerability Assessment &amp; Penetration Testing)</h3>
                <p style="font-size:13px; color:#94a3b8; line-height:1.6; margin-bottom:18px;">
                    OWASP Top 10, API security testing, and infrastructure vulnerability assessment with rigorous manual exploitation and actionable remediation roadmaps.
                </p>
                <div style="font-size:12px; color:#10b981; font-weight:700; margin-bottom:15px;">✓ Black-Box &amp; Grey-Box Auditing · CVSS Scoring &amp; PoC Reports</div>
                <a href="mailto:support@hackersshikkhok.com?subject=VAPT%20Service%20Inquiry" style="display:inline-block; width:100%; text-align:center; padding:10px 0; border-radius:8px; background:#00f5d4; color:#050811; font-weight:800; font-size:13px; text-decoration:none;">Request Assessment</a>
            </div>

            <!-- Service 2 -->
            <div style="background:#0b1120; border:1px solid rgba(124,58,237,0.3); border-radius:16px; padding:28px; box-shadow:0 10px 30px rgba(0,0,0,0.5);">
                <div style="font-size:32px; margin-bottom:12px;">🧹</div>
                <h3 style="font-size:20px; font-weight:800; color:#a78bfa; margin-bottom:10px;">WordPress Malware Recovery &amp; De-Blacklisting</h3>
                <p style="font-size:13px; color:#94a3b8; line-height:1.6; margin-bottom:18px;">
                    Rapid 24-hour cleanup of compromised WordPress installations, eradication of persistent backdoors and SEO spam redirects, plus Google Safe Browsing delisting.
                </p>
                <div style="font-size:12px; color:#a78bfa; font-weight:700; margin-bottom:15px;">✓ Database Sanitization · WAF Configuration &amp; Core Hardening</div>
                <a href="mailto:support@hackersshikkhok.com?subject=WordPress%20Malware%20Removal" style="display:inline-block; width:100%; text-align:center; padding:10px 0; border-radius:8px; background:#7c3aed; color:#ffffff; font-weight:800; font-size:13px; text-decoration:none;">Emergency Malware Recovery</a>
            </div>

            <!-- Service 3 -->
            <div style="background:#0b1120; border:1px solid rgba(255,51,102,0.25); border-radius:16px; padding:28px; box-shadow:0 10px 30px rgba(0,0,0,0.5);">
                <div style="font-size:32px; margin-bottom:12px;">🛡️</div>
                <h3 style="font-size:20px; font-weight:800; color:#ff3366; margin-bottom:10px;">Secure SDLC &amp; Source Code Review</h3>
                <p style="font-size:13px; color:#94a3b8; line-height:1.6; margin-bottom:18px;">
                    Static (SAST) and dynamic (DAST) analysis across PHP, Node.js, Python, and React repositories to eliminate SQLi, XSS, and business logic flaws before release.
                </p>
                <div style="font-size:12px; color:#f43f5e; font-weight:700; margin-bottom:15px;">✓ GitHub/GitLab CI Security · Dependency &amp; Secret Scanning</div>
                <a href="mailto:support@hackersshikkhok.com?subject=Source%20Code%20Audit" style="display:inline-block; width:100%; text-align:center; padding:10px 0; border-radius:8px; background:#ff3366; color:#ffffff; font-weight:800; font-size:13px; text-decoration:none;">Book Code Audit</a>
            </div>

            <!-- Service 4 -->
            <div style="background:#0b1120; border:1px solid rgba(16,185,129,0.25); border-radius:16px; padding:28px;">
                <div style="font-size:32px; margin-bottom:12px;">☁️</div>
                <h3 style="font-size:20px; font-weight:800; color:#10b981; margin-bottom:10px;">Cloud &amp; Linux Infrastructure Hardening</h3>
                <p style="font-size:13px; color:#94a3b8; line-height:1.6; margin-bottom:18px;">
                    AWS, GCP, DigitalOcean, and dedicated VPS posture hardening: SSH key policies, Fail2ban, UFW, container isolation, and Layer 7 DDoS mitigation.
                </p>
                <div style="font-size:12px; color:#10b981; font-weight:700; margin-bottom:15px;">✓ Zero-Trust Architecture · Automated Snapshot Policies</div>
                <a href="mailto:support@hackersshikkhok.com?subject=Server%20Hardening" style="display:inline-block; width:100%; text-align:center; padding:10px 0; border-radius:8px; background:#10b981; color:#050811; font-weight:800; font-size:13px; text-decoration:none;">Harden Infrastructure</a>
            </div>

            <!-- Service 5 -->
            <div style="background:#0b1120; border:1px solid rgba(245,158,11,0.25); border-radius:16px; padding:28px;">
                <div style="font-size:32px; margin-bottom:12px;">📱</div>
                <h3 style="font-size:20px; font-weight:800; color:#f59e0b; margin-bottom:10px;">Website to Native Android App Conversion</h3>
                <p style="font-size:13px; color:#94a3b8; line-height:1.6; margin-bottom:18px;">
                    Transform any responsive web application or WordPress portal into a high-performance native Android Studio APK with offline caching and push notifications.
                </p>
                <div style="font-size:12px; color:#f59e0b; font-weight:700; margin-bottom:15px;">✓ Firebase Push Messaging · Play Store Ready · Biometric Auth</div>
                <a href="<?php echo esc_url( home_url('/app-maker/') ); ?>" style="display:inline-block; width:100%; text-align:center; padding:10px 0; border-radius:8px; background:#f59e0b; color:#050811; font-weight:800; font-size:13px; text-decoration:none;">Launch App Studio</a>
            </div>

            <!-- Service 6 -->
            <div style="background:#0b1120; border:1px solid rgba(56,189,248,0.25); border-radius:16px; padding:28px;">
                <div style="font-size:32px; margin-bottom:12px;">💼</div>
                <h3 style="font-size:20px; font-weight:800; color:#38bdf8; margin-bottom:10px;">Corporate Cyber Defense &amp; Phishing Labs</h3>
                <p style="font-size:13px; color:#94a3b8; line-height:1.6; margin-bottom:18px;">
                    Interactive red-team simulation workshops for corporate teams: phishing detection, credential hygiene, social engineering defense, and incident triage.
                </p>
                <div style="font-size:12px; color:#38bdf8; font-weight:700; margin-bottom:15px;">✓ Hands-On Interactive Labs · Verified Certification Badges</div>
                <a href="<?php echo esc_url( home_url('/advisory/') ); ?>" style="display:inline-block; width:100%; text-align:center; padding:10px 0; border-radius:8px; background:#38bdf8; color:#050811; font-weight:800; font-size:13px; text-decoration:none;">Schedule Corporate Training</a>
            </div>
        </div>

        <!-- Trust & AdSense Compliance Banner -->
        <div style="background:#080e1e; border:1px solid #1e293b; border-radius:16px; padding:30px; text-align:center;">
            <h4 style="color:#ffffff; font-size:18px; margin-bottom:8px;">🔒 100% Authorized White-Hat Security Guarantee</h4>
            <p style="color:#64748b; font-size:13px; max-width:800px; margin:0 auto 15px; line-height:1.6;">
                HackersShikkhok strictly operates within legal bounds under formal Non-Disclosure Agreements (NDAs) and verified written authorizations. We do not participate in unconsented attacks or black-hat operations.
            </p>
            <div style="font-size:12px; color:#00f5d4; font-family:monospace;">Corporate Headquarters · Official Email: support@hackersshikkhok.com · Web: https://hackersshikkhok.com</div>
        </div>
    </div>
</main>
<?php get_footer(); ?>
