<?php
/**
 * Template Name: Advisory & Consultation Hub
 * Description: Cyber defense advisory board, 1-on-1 mentorship & expert consultation booking.
 * Package: HackersShikkhok Theme
 */

declare(strict_types=1);

get_header();
?>
<main id="primary" class="hs-advisory-page" style="background:#050811; color:#f8fafc; min-height:100vh; padding:40px 20px;">
    <div style="margin:0 auto; max-width:1200px;">
        <!-- Header -->
        <div style="text-align:center; margin-bottom:50px;">
            <div style="display:inline-block; padding:6px 16px; border-radius:9999px; background:rgba(124,58,237,0.1); border:1px solid rgba(124,58,237,0.3); color:#a78bfa; font-size:12px; font-weight:800; text-transform:uppercase; letter-spacing:1px; margin-bottom:15px;">
                💡 Strategic Advisory &amp; Cyber Mentorship
            </div>
            <h1 style="font-size:36px; font-weight:900; background:linear-gradient(to right, #a78bfa, #00f5d4, #f43f5e); -webkit-background-clip:text; -webkit-text-fill-color:transparent; margin-bottom:15px;">
                HackersShikkhok Advisory &amp; Consultation Council
            </h1>
            <p style="font-size:16px; color:#94a3b8; max-width:750px; margin:0 auto; line-height:1.7;">
                Connect with principal security architects, defensive engineers, and industry leaders for one-on-one career guidance, infrastructure threat modeling, and research consultation.
            </p>
        </div>

        <!-- 3 Pillars Grid -->
        <div style="display:grid; grid-template-columns:repeat(auto-fit, minmax(320px, 1fr)); gap:24px; margin-bottom:50px;">
            <!-- Pillar 1 -->
            <div style="background:#0b1120; border:1px solid #1e293b; border-radius:16px; padding:28px;">
                <div style="font-size:30px; margin-bottom:12px;">👨‍💻</div>
                <h3 style="font-size:18px; font-weight:800; color:#00f5d4; margin-bottom:10px;">Student &amp; Practitioner Mentorship</h3>
                <p style="font-size:13px; color:#94a3b8; line-height:1.6; margin-bottom:15px;">
                    Accelerate your trajectory from novice to seasoned white-hat defender. Receive tailored roadmaps, hands-on CTF debriefs, and certification guidance.
                </p>
                <div style="font-size:12px; color:#64748b;">• Portfolio Review · Bug Bounty Strategies · Career Blueprint</div>
            </div>

            <!-- Pillar 2 -->
            <div style="background:#0b1120; border:1px solid #1e293b; border-radius:16px; padding:28px;">
                <div style="font-size:30px; margin-bottom:12px;">🏢</div>
                <h3 style="font-size:18px; font-weight:800; color:#a78bfa; margin-bottom:10px;">Startup &amp; Enterprise Security Advisory</h3>
                <p style="font-size:13px; color:#94a3b8; line-height:1.6; margin-bottom:15px;">
                    Prior to launching your fintech, SaaS, or e-commerce platform, conduct proactive architectural threat modeling, cloud posture auditing, and compliance verification.
                </p>
                <div style="font-size:12px; color:#64748b;">• Virtual CISO · Security Policy Framework · Zero-Trust Review</div>
            </div>

            <!-- Pillar 3 -->
            <div style="background:#0b1120; border:1px solid #1e293b; border-radius:16px; padding:28px;">
                <div style="font-size:30px; margin-bottom:12px;">🤝</div>
                <h3 style="font-size:18px; font-weight:800; color:#f43f5e; margin-bottom:10px;">Join as a Faculty Advisor or Researcher</h3>
                <p style="font-size:13px; color:#94a3b8; line-height:1.6; margin-bottom:15px;">
                    Industry veterans and security researchers are invited to contribute curriculum modules, lead live lab simulations, and mentor the next cohort of engineers.
                </p>
                <div style="font-size:12px; color:#64748b;">• Global Community Reach · Research Co-Authoring · Keynote Opportunities</div>
            </div>
        </div>

        <!-- Consultation Booking Form -->
        <div style="background:#0b1120; border:1px solid rgba(0,245,212,0.3); border-radius:20px; padding:35px; max-width:800px; margin:0 auto; box-shadow:0 15px 40px rgba(0,0,0,0.6);">
            <h3 style="font-size:22px; font-weight:800; color:#ffffff; margin-bottom:10px; text-align:center;">
                📝 Request an Expert Consultation Session
            </h3>
            <p style="font-size:13px; color:#94a3b8; text-align:center; margin-bottom:25px;">
                Complete the brief form below. Our senior cybersecurity advisory council responds within 24 business hours.
            </p>

            <form action="mailto:support@hackersshikkhok.com" method="post" enctype="text/plain" style="display:grid; grid-template-columns:1fr 1fr; gap:16px;">
                <div>
                    <label style="display:block; font-size:12px; color:#cbd5e1; margin-bottom:6px;">Your Name *</label>
                    <input type="text" name="name" required placeholder="e.g. Alex Morgan" style="width:100%; padding:10px 14px; background:#050811; border:1px solid #1e293b; border-radius:8px; color:#ffffff; font-size:13px;">
                </div>
                <div>
                    <label style="display:block; font-size:12px; color:#cbd5e1; margin-bottom:6px;">Email Address *</label>
                    <input type="email" name="email" required placeholder="alex@domain.com" style="width:100%; padding:10px 14px; background:#050811; border:1px solid #1e293b; border-radius:8px; color:#ffffff; font-size:13px;">
                </div>
                <div>
                    <label style="display:block; font-size:12px; color:#cbd5e1; margin-bottom:6px;">Phone / Signal Handle</label>
                    <input type="text" name="phone" placeholder="+1 (555) 019-2834" style="width:100%; padding:10px 14px; background:#050811; border:1px solid #1e293b; border-radius:8px; color:#ffffff; font-size:13px;">
                </div>
                <div>
                    <label style="display:block; font-size:12px; color:#cbd5e1; margin-bottom:6px;">Consultation Category *</label>
                    <select name="category" style="width:100%; padding:10px 14px; background:#050811; border:1px solid #1e293b; border-radius:8px; color:#ffffff; font-size:13px;">
                        <option value="student_career">Career Roadmap &amp; Mentorship</option>
                        <option value="enterprise_audit">Enterprise Cyber Architecture Audit</option>
                        <option value="wp_malware">WordPress Defense &amp; Hardening</option>
                        <option value="join_advisor">Join Faculty Advisory Board</option>
                    </select>
                </div>
                <div style="grid-column:1 / -1;">
                    <label style="display:block; font-size:12px; color:#cbd5e1; margin-bottom:6px;">Specific Challenge or Scope *</label>
                    <textarea name="message" rows="4" required placeholder="Briefly describe your objectives, architecture scope, or key questions..." style="width:100%; padding:10px 14px; background:#050811; border:1px solid #1e293b; border-radius:8px; color:#ffffff; font-size:13px;"></textarea>
                </div>
                <div style="grid-column:1 / -1; margin-top:10px;">
                    <button type="submit" style="width:100%; padding:12px; background:linear-gradient(to right, #00f5d4, #7c3aed); border:none; border-radius:8px; color:#050811; font-weight:800; font-size:14px; cursor:pointer;">
                        🚀 Submit Consultation Request
                    </button>
                </div>
            </form>
        </div>
    </div>
</main>
<?php get_footer(); ?>
