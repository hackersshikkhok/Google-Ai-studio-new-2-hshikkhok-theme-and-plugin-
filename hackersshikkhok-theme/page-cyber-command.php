<?php
/**
 * Template Name: Cyber Command & Security Audit Portal
 * Template Post Type: page
 *
 * Dedicated Tactical Cyberpunk B2B Security Operations & Audit Portal.
 * 14-Module Enterprise Client Dashboard.
 *
 * @package HackersShikkhok\Theme
 */

declare(strict_types=1);

get_header();

$current_user_id = get_current_user_id();
$wallet_summary  = class_exists( '\HackersShikkhok\Theme\Modules\LMS\Wallet_Economy' ) 
    ? \HackersShikkhok\Theme\Modules\LMS\Wallet_Economy::get_wallet( $current_user_id ) 
    : array( 'balance_bdt' => 0.0, 'points' => 0 );

$nonce = wp_create_nonce( 'hs_cyber_command_nonce' );
?>

<link rel="stylesheet" href="<?php echo esc_url( get_template_directory_uri() . '/assets/css/cyber-command.css' ); ?>">

<main id="primary" class="hs-cyber-command-wrapper">
    <!-- Top Tactical HUD Bar -->
    <header class="hs-hud-bar">
        <div class="hs-hud-container">
            <div class="hs-hud-brand">
                <span class="hs-hud-pulse"></span>
                <span class="hs-hud-logo">🛡️ HACKERSSHIKKHOK // CYBER COMMAND</span>
                <span class="hs-hud-badge">ENTERPRISE SECURITY SAAS v2.0</span>
            </div>
            <div class="hs-hud-metrics">
                <div class="hs-hud-metric">
                    <span class="hs-metric-label">STATUS</span>
                    <span class="hs-metric-val hs-green">ACTIVE DEFENSE</span>
                </div>
                <div class="hs-hud-metric">
                    <span class="hs-metric-label">WALLET</span>
                    <span class="hs-metric-val hs-cyan">BDT <?php echo number_format( (float) ( $wallet_summary['balance_bdt'] ?? 0.0 ), 2 ); ?> BDT</span>
                </div>
                <div class="hs-hud-metric">
                    <span class="hs-metric-label">OPERATIVE</span>
                    <span class="hs-metric-val"><?php echo esc_html( wp_get_current_user()->display_name ?: 'Guest Security Auditor' ); ?></span>
                </div>
            </div>
        </div>
    </header>

    <!-- Main Command Grid -->
    <div class="hs-command-layout">
        <!-- Sidebar Navigation: 14 Modules -->
        <aside class="hs-command-sidebar">
            <div class="hs-sidebar-title">MODULE REGISTRY</div>
            <nav class="hs-sidebar-nav">
                <button class="hs-nav-btn active" data-tab="tab-overview">📊 1. Threat Radar &amp; Overview</button>
                <button class="hs-nav-btn" data-tab="tab-targets">🌐 2. Assets &amp; Scope Manager</button>
                <button class="hs-nav-btn" data-tab="tab-scanner">⚡ 3. Automated Web Auditor</button>
                <button class="hs-nav-btn" data-tab="tab-findings">🔍 4. Vulnerabilities Ledger</button>
                <button class="hs-nav-btn" data-tab="tab-remediation">🛠️ 5. AI Code Patches</button>
                <button class="hs-nav-btn" data-tab="tab-executive">💼 6. Business Impact Matrix</button>
                <button class="hs-nav-btn" data-tab="tab-pdf">📄 7. Executive PDF Report</button>
                <button class="hs-nav-btn" data-tab="tab-csm">🔄 8. Continuous Monitoring</button>
                <button class="hs-nav-btn" data-tab="tab-redteam">🎯 9. Red Team Penetration</button>
                <button class="hs-nav-btn" data-tab="tab-compliance">📋 10. Compliance (OWASP/ISO)</button>
                <button class="hs-nav-btn" data-tab="tab-webhooks">🔔 11. Alerts &amp; Webhooks</button>
                <button class="hs-nav-btn" data-tab="tab-billing">💳 12. B2B Subscriptions</button>
                <button class="hs-nav-btn" data-tab="tab-terminal">💻 13. Live Activity Terminal</button>
                <button class="hs-nav-btn" data-tab="tab-guidelines">📜 14. Rules of Engagement</button>
            </nav>
        </aside>

        <!-- Command Stage Container -->
        <section class="hs-command-stage">
            
            <!-- TAB 1: OVERVIEW & THREAT RADAR -->
            <div class="hs-tab-content active" id="tab-overview">
                <div class="hs-stage-header">
                    <h2>Enterprise Security Posture &amp; Threat Radar</h2>
                    <p>Real-time vulnerability surface visualization, cloud hardening score, and automated perimeter defenses.</p>
                </div>

                <div class="hs-overview-grid">
                    <div class="hs-card hs-health-card">
                        <div class="hs-health-title">Overall Security Health Index</div>
                        <div class="hs-health-score" id="overviewHealthScore">88<span>/100</span></div>
                        <div class="hs-health-grade" id="overviewHealthGrade">GRADE: A- (HARDENED)</div>
                        <p class="hs-health-desc">Passive scanning and security headers analysis complete. 3 medium-severity exposures detected.</p>
                    </div>

                    <div class="hs-card hs-stat-card">
                        <div class="hs-card-sub">ACTIVE FINDINGS BY SEVERITY</div>
                        <div class="hs-severity-bars">
                            <div class="hs-sev-row">
                                <span class="hs-sev-tag hs-crit">CRITICAL (CVSS 9.0+)</span>
                                <span class="hs-sev-count" id="countCrit">0</span>
                            </div>
                            <div class="hs-sev-row">
                                <span class="hs-sev-tag hs-high">HIGH (CVSS 7.0-8.9)</span>
                                <span class="hs-sev-count" id="countHigh">1</span>
                            </div>
                            <div class="hs-sev-row">
                                <span class="hs-sev-tag hs-med">MEDIUM (CVSS 4.0-6.9)</span>
                                <span class="hs-sev-count" id="countMed">2</span>
                            </div>
                            <div class="hs-sev-row">
                                <span class="hs-sev-tag hs-low">LOW / INFO</span>
                                <span class="hs-sev-count" id="countLow">3</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Quick Scan Launcher -->
                <div class="hs-card hs-quick-scan">
                    <h3>⚡ Instant Zero-Impact Security Audit</h3>
                    <p>Execute non-intrusive, zero-impact perimeter security evaluation against your web asset.</p>
                    <div class="hs-input-row">
                        <input type="url" id="quickTargetInput" placeholder="https://yourcompany.com" value="https://hackersshikkhok.com" class="hs-cyber-input">
                        <button class="hs-cyber-btn" id="btnRunQuickAudit">LAUNCH INSTANT AUDIT</button>
                    </div>
                </div>
            </div>

            <!-- TAB 2: ASSETS & SCOPE MANAGER -->
            <div class="hs-tab-content" id="tab-targets">
                <div class="hs-stage-header">
                    <h2>Target Scope &amp; Asset Ownership Verification</h2>
                    <p>Cryptographically verify domain and web application ownership before conducting assessments (DNS TXT / HTML Meta Tag).</p>
                </div>
                <div class="hs-card">
                    <h3>Add New Corporate Asset to Scope</h3>
                    <div class="hs-form-grid">
                        <div>
                            <label>Domain / URL</label>
                            <input type="text" id="newAssetUrl" placeholder="https://portal.example.com" class="hs-cyber-input">
                        </div>
                        <div>
                            <label>Organization / Project Name</label>
                            <input type="text" id="newAssetOrg" placeholder="Apex FinTech Corp" class="hs-cyber-input">
                        </div>
                        <div>
                            <label>Verification Method</label>
                            <select id="newAssetMethod" class="hs-cyber-select">
                                <option value="dns_txt">DNS TXT Record (_hs-verify)</option>
                                <option value="meta_tag">HTML &lt;meta&gt; Tag on Homepage</option>
                                <option value="file_upload">File Upload (/.well-known/)</option>
                            </select>
                        </div>
                    </div>
                    <button class="hs-cyber-btn" style="margin-top:15px;" id="btnAddTarget">Register Asset to Scope</button>
                </div>

                <div class="hs-card" style="margin-top:20px;">
                    <h3>Verified Target Inventory</h3>
                    <table class="hs-cyber-table">
                        <thead>
                            <tr>
                                <th>Target Asset</th>
                                <th>Organization</th>
                                <th>Status</th>
                                <th>Health</th>
                                <th>Last Audit</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody id="targetTableBody">
                            <tr>
                                <td><strong>https://hackersshikkhok.com</strong></td>
                                <td>HackersShikkhok Core</td>
                                <td><span class="hs-badge hs-green">VERIFIED</span></td>
                                <td><span class="hs-badge hs-cyan">88/100</span></td>
                                <td>Today</td>
                                <td><button class="hs-table-btn" onclick="switchTarget('https://hackersshikkhok.com')">Select</button></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- TAB 3: SCANNER TERMINAL -->
            <div class="hs-tab-content" id="tab-scanner">
                <div class="hs-stage-header">
                    <h2>Automated Passive Web Auditor</h2>
                    <p>SSL/TLS cipher suite analysis, HTTP security headers, sensitive exposed configurations, and CMS vulnerability checks.</p>
                </div>

                <div class="hs-card">
                    <div class="hs-scan-controls">
                        <div>
                            <label>Target Domain</label>
                            <input type="text" id="activeScanUrl" value="https://hackersshikkhok.com" class="hs-cyber-input" style="width:340px;">
                        </div>
                        <div>
                            <label>Audit Profile</label>
                            <select id="scanProfile" class="hs-cyber-select">
                                <option value="passive_full">Full Passive Surface Audit (Recommended)</option>
                                <option value="headers_only">Security Headers Only</option>
                                <option value="ssl_only">SSL/TLS Handshake &amp; Cipher Suite</option>
                                <option value="cms_cve">WordPress &amp; CVE Vulnerability Check</option>
                            </select>
                        </div>
                        <button class="hs-cyber-btn hs-btn-pulse" id="btnStartAuditScan">DISPATCH AUDIT</button>
                    </div>

                    <!-- Progress Bar -->
                    <div class="hs-progress-wrap" style="display:none;" id="scanProgressBox">
                        <div class="hs-progress-info">
                            <span id="scanStatusText">Auditing Target Surface...</span>
                            <span id="scanPercent">0%</span>
                        </div>
                        <div class="hs-progress-bar">
                            <div class="hs-progress-fill" id="scanProgressFill" style="width:0%;"></div>
                        </div>
                    </div>

                    <!-- Terminal Log View -->
                    <div class="hs-terminal-screen" id="scannerTerminalLog">
                        <div class="hs-term-line hs-term-cyan">[SYSTEM] HackersShikkhok Passive Security Engine Initialized. Ready for dispatch.</div>
                    </div>
                </div>
            </div>

            <!-- TAB 4: FINDINGS LEDGER -->
            <div class="hs-tab-content" id="tab-findings">
                <div class="hs-stage-header">
                    <h2>Vulnerability Lifecycle &amp; Findings Ledger</h2>
                    <p>Track identified exposures, CVSS v3.1 impact ratings, proof-of-concept evidence, and patch status.</p>
                </div>

                <div class="hs-filter-bar">
                    <button class="hs-filter-btn active" data-filter="all">ALL FINDINGS</button>
                    <button class="hs-filter-btn" data-filter="critical">CRITICAL</button>
                    <button class="hs-filter-btn" data-filter="high">HIGH</button>
                    <button class="hs-filter-btn" data-filter="medium">MEDIUM</button>
                    <button class="hs-filter-btn" data-filter="low">LOW / INFO</button>
                </div>

                <div class="hs-findings-list" id="findingsListContainer">
                    <!-- Dynamic finding items loaded via JS -->
                </div>
            </div>

            <!-- TAB 5: AI CODE PATCHES -->
            <div class="hs-tab-content" id="tab-remediation">
                <div class="hs-stage-header">
                    <h2>AI Code Remediation &amp; Patch Generator</h2>
                    <p>Automated copy-paste mitigation snippets for Nginx, Apache (.htaccess), Cloudflare WAF, and PHP native headers.</p>
                </div>

                <div class="hs-card">
                    <div class="hs-patch-selector">
                        <label>Select Target Finding to Generate Patch:</label>
                        <select id="patchFindingSelect" class="hs-cyber-select">
                            <option value="missing_hsts">#1. Strict-Transport-Security (HSTS) Header Missing (CVSS 6.5)</option>
                            <option value="missing_csp">#2. Content-Security-Policy (CSP) Missing (CVSS 7.2)</option>
                            <option value="weak_dmarc_policy">#3. Weak DMARC Anti-Spoofing Policy (CVSS 6.1)</option>
                            <option value="exposed_dot_env">#4. Sensitive .env Credentials Exposure (CVSS 9.8)</option>
                        </select>
                    </div>

                    <div class="hs-code-tabs">
                        <button class="hs-code-tab active" data-server="nginx">Nginx Server Block</button>
                        <button class="hs-code-tab" data-server="apache">Apache .htaccess</button>
                        <button class="hs-code-tab" data-server="php">PHP Native Header</button>
                        <button class="hs-code-tab" data-server="cloudflare">Cloudflare WAF Rule</button>
                    </div>

                    <div class="hs-code-display">
                        <pre><code id="remediationCodeSnippet">add_header Strict-Transport-Security "max-age=31536000; includeSubDomains; preload" always;</code></pre>
                        <button class="hs-copy-btn" id="btnCopyPatch">COPY PATCH CODE</button>
                    </div>
                </div>
            </div>

            <!-- TAB 6: BUSINESS IMPACT MATRIX -->
            <div class="hs-tab-content" id="tab-executive">
                <div class="hs-stage-header">
                    <h2>Executive Business Impact &amp; Financial Exposure</h2>
                    <p>Quantitative cyber risk modeling, estimated financial exposure, and C-Suite breach liability analysis.</p>
                </div>

                <div class="hs-overview-grid">
                    <div class="hs-card">
                        <h3>Estimated Financial Breach Liability</h3>
                        <div style="font-size:32px; font-weight:900; color:#ff3366; margin:10px 0;">$50,000 - $250,000 USD</div>
                        <p>Projected liabilities including incident response, regulatory compliance penalties (GDPR/CCPA), and customer notification costs.</p>
                    </div>
                    <div class="hs-card">
                        <h3>Brand &amp; Customer Trust Degradation</h3>
                        <div style="font-size:32px; font-weight:900; color:#f59e0b; margin:10px 0;">HIGH VULNERABILITY</div>
                        <p>Potential reputational damage resulting from unauthenticated email spoofing, phishing mimicry, and session hijacking risks.</p>
                    </div>
                </div>
            </div>

            <!-- TAB 7: PDF REPORT -->
            <div class="hs-tab-content" id="tab-pdf">
                <div class="hs-stage-header">
                    <h2>Download Executive PDF Security Audit Report</h2>
                    <p>Comprehensive technical and C-suite audit summaries ready for stakeholder review and board presentations.</p>
                </div>

                <div class="hs-card" style="text-align:center; padding:40px 20px;">
                    <div style="font-size:48px; margin-bottom:12px;">📄</div>
                    <h3>Executive Cyber Audit Report Ready</h3>
                    <p style="max-width:550px; margin:0 auto 20px; color:#94a3b8;">
                        Includes overall security index, CVSS v3.1 ranking breakdown, screenshot evidence, and step-by-step remediation plan.
                    </p>
                    <button class="hs-cyber-btn" id="btnDownloadPdf">PRINT / SAVE AS PDF REPORT</button>
                </div>
            </div>

            <!-- TAB 8: CONTINUOUS MONITORING -->
            <div class="hs-tab-content" id="tab-csm">
                <div class="hs-stage-header">
                    <h2>Continuous Security Monitoring (CSM)</h2>
                    <p>Continuous perimeter scanning: trigger automated audits and instant alerts whenever new CVEs or configuration drifts occur.</p>
                </div>

                <div class="hs-card">
                    <h3>Configure Automated Schedule</h3>
                    <div class="hs-form-grid">
                        <div>
                            <label>Audit Frequency</label>
                            <select class="hs-cyber-select" id="csmFrequency">
                                <option value="daily">Daily Continuous Surface Probe</option>
                                <option value="weekly" selected>Weekly Full Security Audit</option>
                                <option value="monthly">Monthly Compliance Health Audit</option>
                            </select>
                        </div>
                        <div>
                            <label>Alert Email Address</label>
                            <input type="email" class="hs-cyber-input" value="<?php echo esc_attr( wp_get_current_user()->user_email ?: 'security@hackersshikkhok.com' ); ?>">
                        </div>
                    </div>
                    <button class="hs-cyber-btn" style="margin-top:15px;" id="btnSaveSchedule">Save Monitoring Schedule</button>
                </div>
            </div>

            <!-- TAB 9: RED TEAM -->
            <div class="hs-tab-content" id="tab-redteam">
                <div class="hs-stage-header">
                    <h2>Custom Red Team &amp; Manual Penetration Testing</h2>
                    <p>Authorized manual penetration testing: credential stuffing resilience, business logic flaw exploitation, and executive debriefs.</p>
                </div>

                <div class="hs-card">
                    <h3>Submit Red Team Scoping Request</h3>
                    <div class="hs-form-grid">
                        <div>
                            <label>Company / Organization Name</label>
                            <input type="text" id="rtCompanyName" placeholder="Standard Commercial Bank Ltd." class="hs-cyber-input">
                        </div>
                        <div>
                            <label>Authorized Contact Email</label>
                            <input type="email" id="rtContactEmail" placeholder="ciso@company.com" class="hs-cyber-input">
                        </div>
                    </div>
                    <div style="margin-top:12px;">
                        <label>Scope of Engagement (Domains, APIs, Mobile APK, IP Ranges)</label>
                        <textarea id="rtScopeText" rows="4" class="hs-cyber-input" style="width:100%;" placeholder="1. https://api.company.com/v1/*&#10;2. Android APK Package com.company.app&#10;3. Core Payment Gateway Workflow"></textarea>
                    </div>
                    <div style="margin-top:10px; color:#94a3b8; font-size:12px;">
                        <label style="cursor:pointer; display:flex; align-items:center; gap:8px;">
                            <input type="checkbox" id="rtNdaCheck" checked> 
                            <span>I confirm legal ownership or authorized agency of target assets and consent to mutual NDA &amp; Rules of Engagement.</span>
                        </label>
                    </div>
                    <button class="hs-cyber-btn" style="margin-top:15px;" id="btnSubmitRedTeam">Submit Official Red Team Proposal</button>
                </div>
            </div>

            <!-- TAB 10: COMPLIANCE -->
            <div class="hs-tab-content" id="tab-compliance">
                <div class="hs-stage-header">
                    <h2>Global Security Compliance Matrix</h2>
                    <p>Automated compliance benchmarks against OWASP Top 10, ISO 27001, NIST CSF, and national cybersecurity standards.</p>
                </div>

                <div class="hs-card">
                    <h3>Compliance Readiness Scorecards</h3>
                    <div class="hs-compliance-row">
                        <div class="hs-comp-header">
                            <strong>OWASP Top 10 (Web Application Security)</strong>
                            <span class="hs-badge hs-cyan">85% PASSED</span>
                        </div>
                        <div class="hs-comp-items">
                            <div>✓ A01: Broken Access Control (Protected)</div>
                            <div>✓ A02: Cryptographic Failures (TLSv1.3 Active)</div>
                            <div>⚠️ A05: Security Misconfiguration (HSTS/CSP Missing)</div>
                        </div>
                    </div>

                    <div class="hs-compliance-row" style="margin-top:20px;">
                        <div class="hs-comp-header">
                            <strong>ISO/IEC 27001:2022 Technical Controls</strong>
                            <span class="hs-badge hs-green">90% COMPLIANT</span>
                        </div>
                        <div class="hs-comp-items">
                            <div>✓ Control 8.8: Management of Technical Vulnerabilities</div>
                            <div>✓ Control 8.20: Network Security Hardening</div>
                            <div>⚠️ Control 8.24: Use of Cryptography &amp; Transport Policies</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- TAB 11: WEBHOOKS -->
            <div class="hs-tab-content" id="tab-webhooks">
                <div class="hs-stage-header">
                    <h2>Security Webhooks &amp; Instant Incident Dispatcher</h2>
                    <p>Broadcast high-severity security notifications to Slack, Discord, Microsoft Teams, or custom SIEM pipelines.</p>
                </div>

                <div class="hs-card">
                    <h3>Enterprise Webhook &amp; SIEM Notification Gateway</h3>
                    <div class="hs-form-grid">
                        <div>
                            <label>Notification Platform</label>
                            <select id="webhookPlatformSelect" class="hs-cyber-select">
                                <option value="slack">Slack Incoming Webhook</option>
                                <option value="discord">Discord Channel Webhook</option>
                                <option value="teams">Microsoft Teams Webhook</option>
                                <option value="custom_siem">Custom SIEM / HTTP JSON Endpoint</option>
                            </select>
                        </div>
                        <div>
                            <label>HMAC-SHA256 Secret Signature</label>
                            <input type="text" readonly class="hs-cyber-input" value="hs_sec_9f81a7b3e0c4d291fa87">
                        </div>
                    </div>
                    <div style="margin-top:14px;">
                        <label>Target Webhook URL</label>
                        <input type="url" id="webhookUrlInput" class="hs-cyber-input" style="width:100%;" placeholder="https://hooks.slack.com/services/T00/B00/XXXX" value="https://hooks.slack.com/services/DEMO/SECURITY/ALERT">
                    </div>
                    <div style="margin-top:14px;" class="hs-form-row">
                        <label style="display:inline-flex; align-items:center; gap:8px; font-size:12px; color:#cbd5e1; cursor:pointer;">
                            <input type="checkbox" checked> Send alerts on CRITICAL (CVSS 9.0+) exposures
                        </label>
                        <label style="display:inline-flex; align-items:center; gap:8px; font-size:12px; color:#cbd5e1; cursor:pointer; margin-left:16px;">
                            <input type="checkbox" checked> Send report link on scan completion
                        </label>
                    </div>
                    <button class="hs-cyber-btn" style="margin-top:15px;" onclick="testSecurityWebhook()">DISPATCH TEST PAYLOAD</button>
                </div>
            </div>

            <!-- TAB 12: B2B SUBSCRIPTION -->
            <div class="hs-tab-content" id="tab-billing">
                <div class="hs-stage-header">
                    <h2>B2B Security Subscription &amp; Tier Management</h2>
                    <p>Select tailored defense auditing plans designed for modern tech companies and digital agencies.</p>
                </div>

                <div class="hs-pricing-grid">
                    <div class="hs-card hs-price-card">
                        <h3>Starter Security</h3>
                        <div class="hs-price-amt">BDT 1,500<span> BDT/mo</span></div>
                        <ul class="hs-price-features">
                            <li>3 Domains or Web Apps</li>
                            <li>Weekly Automated Audits</li>
                            <li>SSL/TLS &amp; Header Diagnostics</li>
                            <li>PDF Audit Report Downloads</li>
                        </ul>
                        <button class="hs-cyber-btn" onclick="upgradePlan('starter')">Select Starter</button>
                    </div>

                    <div class="hs-card hs-price-card hs-featured-price">
                        <div class="hs-badge hs-cyan" style="margin-bottom:8px;">POPULAR FOR BUSINESS</div>
                        <h3>Business Continuous</h3>
                        <div class="hs-price-amt">BDT 4,500<span> BDT/mo</span></div>
                        <ul class="hs-price-features">
                            <li>10 Domains &amp; API Scopes</li>
                            <li>Daily Continuous Surface Probes</li>
                            <li>CVE &amp; Sensitive File Detection</li>
                            <li>AI Remediation Patch Snippets</li>
                            <li>1 Quarterly Red Team Penetration Test</li>
                        </ul>
                        <button class="hs-cyber-btn hs-btn-pulse" onclick="upgradePlan('business')">Select Business</button>
                    </div>

                    <div class="hs-card hs-price-card">
                        <h3>Enterprise Managed</h3>
                        <div class="hs-price-amt">BDT 15,000<span> BDT/mo</span></div>
                        <ul class="hs-price-features">
                            <li>Unlimited Target Scopes</li>
                            <li>Real-Time Continuous Surface Scans</li>
                            <li>Dedicated Manual Penetration Testing</li>
                            <li>Guaranteed SLA &amp; Emergency Triage</li>
                            <li>Quarterly Executive Board Briefings</li>
                        </ul>
                        <button class="hs-cyber-btn" onclick="upgradePlan('enterprise')">Select Enterprise</button>
                    </div>
                </div>
            </div>

            <!-- TAB 13: TERMINAL LOGS -->
            <div class="hs-tab-content" id="tab-terminal">
                <div class="hs-stage-header">
                    <h2>Live Security Operations Terminal Logs</h2>
                    <p>Real-time audit telemetry, network probes, and background diagnostic stream.</p>
                </div>
                <div class="hs-terminal-screen" style="height:350px;">
                    <div class="hs-term-line hs-term-cyan">[INFO] C2 Daemon Online · Listening on socket /var/run/hs_c2.sock</div>
                    <div class="hs-term-line">[DEBUG] Micro-batch queue initialized. Maximum execution limit: 15s.</div>
                    <div class="hs-term-line hs-term-green">[SUCCESS] Let's Encrypt TLS handshake verified on 443.</div>
                    <div class="hs-term-line hs-term-yellow">[WARN] Missing Strict-Transport-Security header detected.</div>
                    <div class="hs-term-line hs-term-red">[ALERT] Weak DMARC spoofing policy active on domain DNS.</div>
                    <div class="hs-term-line hs-term-cyan">[SYSTEM] Audit complete. Overall health score evaluated to 88/100.</div>
                </div>
            </div>

            <!-- TAB 14: ROE & GUIDELINES -->
            <div class="hs-tab-content" id="tab-guidelines">
                <div class="hs-stage-header">
                    <h2>Rules of Engagement (RoE) &amp; Ethical Standard</h2>
                    <p>Responsible disclosure policy, bilateral legal consent, and 99.9% zero-disruption SLA guarantee.</p>
                </div>
                <div class="hs-card">
                    <h3>100% Responsible &amp; Lawful Security Testing Standard</h3>
                    <p>
                        HackersShikkhok conducts security testing strictly against authorized client assets. We never perform denial-of-service (DDoS) or destructive exploits. All evaluations adhere strictly to the <strong>OWASP Web Security Testing Guide (WSTG v4.2)</strong> and applicable cyber legislation.
                    </p>

                    <div style="margin-top:24px; padding-top:20px; border-top:1px solid #1e293b;">
                        <h3>Execute Bilateral Non-Disclosure Agreement (M-NDA) &amp; Legal Authorization</h3>
                        <p style="color:#94a3b8; font-size:12px; margin-bottom:16px;">
                            Generate official corporate authorization credentials for your security team:
                        </p>
                        <div class="hs-form-grid">
                            <div>
                                <label>Corporate Organization Name</label>
                                <input type="text" id="roeOrgName" placeholder="Tech Global Solutions Ltd." class="hs-cyber-input">
                            </div>
                            <div>
                                <label>Authorized Signer / CISO Name</label>
                                <input type="text" id="roeSignerName" placeholder="Engr. Tanvir Ahmed" class="hs-cyber-input">
                            </div>
                            <div>
                                <label>Signer Official Title</label>
                                <input type="text" id="roeSignerTitle" placeholder="Head of IT Security &amp; Infrastructure" class="hs-cyber-input">
                            </div>
                        </div>
                        <div style="margin-top:14px;">
                            <label>Authorized Scope of Engagement</label>
                            <textarea id="roeScopeText" rows="3" class="hs-cyber-input" style="width:100%;" placeholder="https://api.techglobal.com, https://portal.techglobal.com"></textarea>
                        </div>
                        <button class="hs-cyber-btn" style="margin-top:16px;" onclick="signRulesOfEngagement()">EXECUTE LEGAL ROE &amp; DOWNLOAD CERTIFICATE</button>
                    </div>
                </div>
            </div>

        </section>
    </div>
</main>

<script>
    window.HS_CYBER_COMMAND = {
        ajaxUrl: '<?php echo esc_url( admin_url( 'admin-ajax.php' ) ); ?>',
        restUrl: '<?php echo esc_url( rest_url( 'hackersshikkhok/v1/security/' ) ); ?>',
        nonce: '<?php echo esc_js( $nonce ); ?>',
        siteUrl: '<?php echo esc_url( home_url() ); ?>'
    };
    // Non-blocking toast helper
    window.hsShowToast = function(msg) {
        let t = document.getElementById('hs-global-toast');
        if (!t) {
            t = document.createElement('div');
            t.id = 'hs-global-toast';
            t.style.cssText = 'position:fixed;bottom:24px;right:24px;background:#0f172a;border:1px solid #00f5d4;color:#00f5d4;padding:12px 20px;border-radius:10px;font-family:monospace;font-size:13px;z-index:99999;box-shadow:0 10px 30px rgba(0,0,0,0.8);transition:opacity 0.3s;';
            document.body.appendChild(t);
        }
        t.textContent = msg;
        t.style.opacity = '1';
        setTimeout(() => { if (t) t.style.opacity = '0'; }, 3500);
    };
    const btnSave = document.getElementById('btnSaveSchedule');
    if (btnSave) {
        btnSave.addEventListener('click', function() {
            window.hsShowToast('Automated monitoring schedule updated successfully.');
        });
    }
</script>
<script src="<?php echo esc_url( get_template_directory_uri() . '/assets/js/cyber-command.js' ); ?>"></script>

<?php
get_footer();
