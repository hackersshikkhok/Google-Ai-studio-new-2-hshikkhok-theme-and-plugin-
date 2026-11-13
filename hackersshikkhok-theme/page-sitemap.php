<?php
/**
 * Template Name: HTML Master Sitemap & Architecture Directory
 * Description: Human-friendly and AI-readable comprehensive directory of all tools, courses, centers and hubs.
 * Package: HackersShikkhok Theme
 */

declare(strict_types=1);

get_header();
?>
<main id="primary" class="hs-sitemap-page" style="background:#050811; color:#f8fafc; min-height:100vh; padding:50px 20px;">
    <div style="margin:0 auto; max-width:1200px;">
        <div style="text-align:center; margin-bottom:50px;">
            <div style="display:inline-block; padding:6px 16px; border-radius:9999px; background:rgba(0,245,212,0.1); border:1px solid rgba(0,245,212,0.3); color:#00f5d4; font-size:12px; font-weight:800; text-transform:uppercase; letter-spacing:1px; margin-bottom:15px;">
                🗺️ Full Architecture &amp; Crawl Directory
            </div>
            <h1 style="font-size:36px; font-weight:900; background:linear-gradient(to right, #00f5d4, #7c3aed, #ff3366); -webkit-background-clip:text; -webkit-text-fill-color:transparent; margin-bottom:15px;">
                Master Sitemap &amp; Index Directory
            </h1>
            <p style="font-size:15px; color:#94a3b8; max-width:700px; margin:0 auto;">
                Comprehensive structural directory of all platforms, academic faculties, security centers, and developer utilities for visitors, search engines, and AI agents.
            </p>
        </div>

        <div style="display:grid; grid-template-columns:repeat(auto-fit, minmax(280px, 1fr)); gap:24px;">
            <!-- Category 1 -->
            <div style="background:#0b1120; border:1px solid #1e293b; border-radius:16px; padding:24px;">
                <h3 style="font-size:18px; font-weight:800; color:#00f5d4; margin-bottom:15px; border-bottom:1px solid #1e293b; padding-bottom:10px;">
                    🎯 Core Centers &amp; Academy
                </h3>
                <ul style="list-style:none; padding:0; margin:0; line-height:2.2; font-size:14px; color:#cbd5e1;">
                    <li><a href="<?php echo esc_url( home_url('/') ); ?>" style="color:#f8fafc; text-decoration:none;">🏠 Homepage &amp; Threat Portal</a></li>
                    <li><a href="<?php echo esc_url( home_url('/academy/') ); ?>" style="color:#00f5d4; text-decoration:none;">🎓 Cyber Defense Academy &amp; CTF</a></li>
                    <li><a href="<?php echo esc_url( home_url('/cyber-command/') ); ?>" style="color:#f8fafc; text-decoration:none;">⚡ Cyber Command Center &amp; Threat Feed</a></li>
                    <li><a href="<?php echo esc_url( home_url('/tools-lab/') ); ?>" style="color:#f8fafc; text-decoration:none;">🧪 2,024+ Developer Tools Lab</a></li>
                    <li><a href="<?php echo esc_url( home_url('/platform-center/') ); ?>" style="color:#00f5d4; text-decoration:none;">🚀 Platform Center &amp; Studio Hub</a></li>
                    <li><a href="<?php echo esc_url( home_url('/app-maker/') ); ?>" style="color:#f8fafc; text-decoration:none;">📱 Website to Android APK Maker</a></li>
                    <li><a href="<?php echo esc_url( home_url('/verify-certificate/') ); ?>" style="color:#f8fafc; text-decoration:none;">📜 Certificate Verification Portal</a></li>
                </ul>
            </div>

            <!-- Category 2 -->
            <div style="background:#0b1120; border:1px solid #1e293b; border-radius:16px; padding:24px;">
                <h3 style="font-size:18px; font-weight:800; color:#a78bfa; margin-bottom:15px; border-bottom:1px solid #1e293b; padding-bottom:10px;">
                    💼 Services &amp; Advisory
                </h3>
                <ul style="list-style:none; padding:0; margin:0; line-height:2.2; font-size:14px; color:#cbd5e1;">
                    <li><a href="<?php echo esc_url( home_url('/services/') ); ?>" style="color:#a78bfa; text-decoration:none;">🛡️ Professional Security Services</a></li>
                    <li><a href="<?php echo esc_url( home_url('/services/#vapt') ); ?>" style="color:#f8fafc; text-decoration:none;">🔍 VAPT &amp; Penetration Testing Audit</a></li>
                    <li><a href="<?php echo esc_url( home_url('/services/#malware') ); ?>" style="color:#f8fafc; text-decoration:none;">🧹 WordPress Malware Recovery</a></li>
                    <li><a href="<?php echo esc_url( home_url('/services/#hardening') ); ?>" style="color:#f8fafc; text-decoration:none;">☁️ Cloud Server Hardening</a></li>
                    <li><a href="<?php echo esc_url( home_url('/advisory/') ); ?>" style="color:#a78bfa; text-decoration:none;">💡 Strategic Advisory &amp; Mentorship</a></li>
                    <li><a href="<?php echo esc_url( home_url('/advisory/#join') ); ?>" style="color:#f8fafc; text-decoration:none;">🤝 Join as Faculty Advisor</a></li>
                </ul>
            </div>

            <!-- Category 3 -->
            <div style="background:#0b1120; border:1px solid #1e293b; border-radius:16px; padding:24px;">
                <h3 style="font-size:18px; font-weight:800; color:#ff3366; margin-bottom:15px; border-bottom:1px solid #1e293b; padding-bottom:10px;">
                    ⚖️ Legal, Policy &amp; Compliance
                </h3>
                <ul style="list-style:none; padding:0; margin:0; line-height:2.2; font-size:14px; color:#cbd5e1;">
                    <li><a href="<?php echo esc_url( home_url('/privacy-policy/') ); ?>" style="color:#ff3366; text-decoration:none;">🔒 Privacy Policy (GDPR/CCPA)</a></li>
                    <li><a href="<?php echo esc_url( home_url('/terms-of-service/') ); ?>" style="color:#f8fafc; text-decoration:none;">⚖️ Terms of Service &amp; Ethical Charter</a></li>
                    <li><a href="<?php echo esc_url( home_url('/disclaimer/') ); ?>" style="color:#f8fafc; text-decoration:none;">⚠️ Legal Disclaimer &amp; Fair Use</a></li>
                    <li><a href="<?php echo esc_url( home_url('/dmca/') ); ?>" style="color:#f8fafc; text-decoration:none;">🛡️ DMCA &amp; Copyright Policy</a></li>
                    <li><a href="<?php echo esc_url( home_url('/sitemap/') ); ?>" style="color:#ff3366; text-decoration:none;">🗺️ HTML Master Sitemap</a></li>
                </ul>
            </div>

            <!-- Category 4 -->
            <div style="background:#0b1120; border:1px solid #1e293b; border-radius:16px; padding:24px;">
                <h3 style="font-size:18px; font-weight:800; color:#10b981; margin-bottom:15px; border-bottom:1px solid #1e293b; padding-bottom:10px;">
                    ⚡ Academic Faculties
                </h3>
                <ul style="list-style:none; padding:0; margin:0; line-height:2.2; font-size:14px; color:#cbd5e1;">
                    <li><a href="<?php echo esc_url( home_url('/academy/?cat=cybersecurity') ); ?>" style="color:#10b981; text-decoration:none;">🛡️ Cybersecurity &amp; Ethical Hacking</a></li>
                    <li><a href="<?php echo esc_url( home_url('/academy/?cat=web-dev') ); ?>" style="color:#f8fafc; text-decoration:none;">💻 Full-Stack Web &amp; Software Systems</a></li>
                    <li><a href="<?php echo esc_url( home_url('/academy/?cat=ai-ml') ); ?>" style="color:#f8fafc; text-decoration:none;">🤖 Artificial Intelligence &amp; Autonomous Bots</a></li>
                    <li><a href="<?php echo esc_url( home_url('/academy/?cat=networking') ); ?>" style="color:#f8fafc; text-decoration:none;">🌐 Cloud Infrastructure &amp; Linux Hardening</a></li>
                    <li><a href="<?php echo esc_url( home_url('/academy/?cat=reverse-eng') ); ?>" style="color:#f8fafc; text-decoration:none;">🔬 Reverse Engineering &amp; Binary Analysis</a></li>
                </ul>
            </div>
        </div>
    </div>
</main>
<?php get_footer(); ?>
