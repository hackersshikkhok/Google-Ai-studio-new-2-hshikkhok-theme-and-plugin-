<?php
/**
 * Master Academy Courses Archive Template - HackersShikkhok
 * Brand: HackersShikkhok (https://hackersshikkhok.com)
 */
declare(strict_types=1);

get_header();

$faculty_query = isset( $_GET['cat'] ) ? sanitize_text_field( wp_unslash( $_GET['cat'] ) ) : 'all';
?>
<main id="primary" class="site-main hs-academy-archive-wrap">

    <header class="hs-tools-hero">
        <div class="hs-tools-hero-inner">
            <nav class="hs-breadcrumbs" aria-label="Breadcrumb">
                <a href="<?php echo esc_url( home_url( '/' ) ); ?>">Home</a>
                <span class="hs-breadcrumb-sep">/</span>
                <span class="hs-breadcrumb-current">Cyber Defense Academy</span>
            </nav>

            <span class="hs-hero-badge">🎓 DEFENSIVE CYBER ACADEMY</span>
            <h1 class="hs-tools-hero-title">8 Specialized Engineering Faculties &amp; CTF Labs</h1>
            <p class="hs-tools-hero-sub">
                Comprehensive, open-access curricula covering authorized penetration testing, secure software engineering, WordPress core, and autonomous AI systems.
            </p>

            <div style="display:flex;gap:12px;justify-content:center;flex-wrap:wrap;margin-top:16px;">
                <a href="<?php echo esc_url( home_url( '/academy/' ) ); ?>" class="hs-btn-primary">⚡ Launch Web Terminal &amp; CTF Sandbox</a>
                <a href="<?php echo esc_url( home_url( '/verify-certificate/' ) ); ?>" class="hs-btn-secondary">📜 Verify Certificate</a>
            </div>
        </div>
    </header>

    <!-- Faculty Tabs -->
    <section class="hs-tools-filters-container">
        <div class="hs-filter-chips-row">
            <a href="<?php echo esc_url( home_url( '/academy/' ) ); ?>" class="hs-filter-chip hs-filter-active">All Courses</a>
            <a href="<?php echo esc_url( home_url( '/academy/?cat=cybersecurity' ) ); ?>" class="hs-filter-chip">🛡️ Cybersecurity</a>
            <a href="<?php echo esc_url( home_url( '/academy/?cat=webdev' ) ); ?>" class="hs-filter-chip">💻 Web Dev</a>
            <a href="<?php echo esc_url( home_url( '/academy/?cat=wordpress' ) ); ?>" class="hs-filter-chip">⚡ WordPress</a>
            <a href="<?php echo esc_url( home_url( '/academy/?cat=ai_data' ) ); ?>" class="hs-filter-chip">🤖 AI &amp; Data</a>
            <a href="<?php echo esc_url( home_url( '/academy/?cat=freelancing' ) ); ?>" class="hs-filter-chip">💼 Freelancing</a>
            <a href="<?php echo esc_url( home_url( '/academy/?cat=design_media' ) ); ?>" class="hs-filter-chip">🎨 Design Systems</a>
            <a href="<?php echo esc_url( home_url( '/academy/?cat=hardware_iot' ) ); ?>" class="hs-filter-chip">📡 Hardware &amp; IoT</a>
            <a href="<?php echo esc_url( home_url( '/academy/?cat=marketing_business' ) ); ?>" class="hs-filter-chip">📈 SEO &amp; Growth</a>
        </div>
    </section>

    <!-- 8 Master Faculties Grid -->
    <section class="hs-section-container">
        <div class="hs-card-grid">
            
            <div class="hs-cyber-card">
                <span class="hs-card-badge">Faculty 01</span>
                <h3 class="hs-card-title">Cybersecurity &amp; Ethical Hacking</h3>
                <p class="hs-card-desc">Defensive architecture, authorized penetration testing, Linux hardening, and OWASP Top 10 mitigation.</p>
                <div style="margin-top:auto;display:flex;justify-content:space-between;align-items:center;padding-top:16px;border-top:1px solid var(--hs-border);">
                    <span style="font-size:12px;color:var(--hs-primary);font-weight:700;">10 Master Courses</span>
                    <a href="<?php echo esc_url( home_url( '/academy/?cat=cybersecurity' ) ); ?>" class="hs-read-more-btn">Open Faculty &rarr;</a>
                </div>
            </div>

            <div class="hs-cyber-card">
                <span class="hs-card-badge">Faculty 02</span>
                <h3 class="hs-card-title">Full-Stack Web &amp; Software Systems</h3>
                <p class="hs-card-desc">Modern JavaScript, TypeScript, React 19, Node.js, Python backends, and microservices architecture.</p>
                <div style="margin-top:auto;display:flex;justify-content:space-between;align-items:center;padding-top:16px;border-top:1px solid var(--hs-border);">
                    <span style="font-size:12px;color:var(--hs-primary);font-weight:700;">10 Master Courses</span>
                    <a href="<?php echo esc_url( home_url( '/academy/?cat=webdev' ) ); ?>" class="hs-read-more-btn">Open Faculty &rarr;</a>
                </div>
            </div>

            <div class="hs-cyber-card">
                <span class="hs-card-badge">Faculty 03</span>
                <h3 class="hs-card-title">WordPress Engineering &amp; Ecosystem</h3>
                <p class="hs-card-desc">Professional theme and core plugin development, CPTs, REST APIs, security audits, and database tuning.</p>
                <div style="margin-top:auto;display:flex;justify-content:space-between;align-items:center;padding-top:16px;border-top:1px solid var(--hs-border);">
                    <span style="font-size:12px;color:var(--hs-primary);font-weight:700;">10 Master Courses</span>
                    <a href="<?php echo esc_url( home_url( '/academy/?cat=wordpress' ) ); ?>" class="hs-read-more-btn">Open Faculty &rarr;</a>
                </div>
            </div>

            <div class="hs-cyber-card">
                <span class="hs-card-badge">Faculty 04</span>
                <h3 class="hs-card-title">AI, Data Science &amp; Autonomous Agents</h3>
                <p class="hs-card-desc">LLM orchestration, multi-provider failover, Python data intelligence, and autonomous autopilot engines.</p>
                <div style="margin-top:auto;display:flex;justify-content:space-between;align-items:center;padding-top:16px;border-top:1px solid var(--hs-border);">
                    <span style="font-size:12px;color:var(--hs-primary);font-weight:700;">10 Master Courses</span>
                    <a href="<?php echo esc_url( home_url( '/academy/?cat=ai_data' ) ); ?>" class="hs-read-more-btn">Open Faculty &rarr;</a>
                </div>
            </div>

            <div class="hs-cyber-card">
                <span class="hs-card-badge">Faculty 05</span>
                <h3 class="hs-card-title">Freelancing &amp; Global Remote Careers</h3>
                <p class="hs-card-desc">International client acquisition, technical proposals, security auditing consulting, and remote job strategies.</p>
                <div style="margin-top:auto;display:flex;justify-content:space-between;align-items:center;padding-top:16px;border-top:1px solid var(--hs-border);">
                    <span style="font-size:12px;color:var(--hs-primary);font-weight:700;">10 Master Courses</span>
                    <a href="<?php echo esc_url( home_url( '/academy/?cat=freelancing' ) ); ?>" class="hs-read-more-btn">Open Faculty &rarr;</a>
                </div>
            </div>

            <div class="hs-cyber-card">
                <span class="hs-card-badge">Faculty 06</span>
                <h3 class="hs-card-title">UI/UX Design Systems &amp; Media</h3>
                <p class="hs-card-desc">Figma design systems, glassmorphism UI tokens, YouTube tech branding, and high-fidelity vector styling.</p>
                <div style="margin-top:auto;display:flex;justify-content:space-between;align-items:center;padding-top:16px;border-top:1px solid var(--hs-border);">
                    <span style="font-size:12px;color:var(--hs-primary);font-weight:700;">10 Master Courses</span>
                    <a href="<?php echo esc_url( home_url( '/academy/?cat=design_media' ) ); ?>" class="hs-read-more-btn">Open Faculty &rarr;</a>
                </div>
            </div>

            <div class="hs-cyber-card">
                <span class="hs-card-badge">Faculty 07</span>
                <h3 class="hs-card-title">Hardware, Networking &amp; IoT Defense</h3>
                <p class="hs-card-desc">Router security hardening, home lab infrastructure, packet analysis, and embedded IoT firmware development.</p>
                <div style="margin-top:auto;display:flex;justify-content:space-between;align-items:center;padding-top:16px;border-top:1px solid var(--hs-border);">
                    <span style="font-size:12px;color:var(--hs-primary);font-weight:700;">10 Master Courses</span>
                    <a href="<?php echo esc_url( home_url( '/academy/?cat=hardware_iot' ) ); ?>" class="hs-read-more-btn">Open Faculty &rarr;</a>
                </div>
            </div>

            <div class="hs-cyber-card">
                <span class="hs-card-badge">Faculty 08</span>
                <h3 class="hs-card-title">Technical SEO &amp; Digital Growth</h3>
                <p class="hs-card-desc">Structured data (JSON-LD), Core Web Vitals optimization, content architecture, and tech sales funnels.</p>
                <div style="margin-top:auto;display:flex;justify-content:space-between;align-items:center;padding-top:16px;border-top:1px solid var(--hs-border);">
                    <span style="font-size:12px;color:var(--hs-primary);font-weight:700;">10 Master Courses</span>
                    <a href="<?php echo esc_url( home_url( '/academy/?cat=marketing_business' ) ); ?>" class="hs-read-more-btn">Open Faculty &rarr;</a>
                </div>
            </div>

        </div>
    </section>

</main>
<?php
get_footer();
