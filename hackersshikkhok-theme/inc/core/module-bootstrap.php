<?php
/**
 * Conditional & Lazy Module Bootstrapper for HackersShikkhok Theme.
 * Replaces the old plugin's monolithic 60-class eager boot with setting-gated,
 * fault-tolerant registration so disabled modules consume zero CPU/memory.
 */

declare(strict_types=1);

namespace HackersShikkhok\Theme\Core;

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

final class ModuleBootstrap {
    private static bool $booted = false;

    public static function boot(): void {
        if ( self::$booted ) {
            return;
        }
        self::$booted = true;

        // 1. Core Diagnostics & Cache Invalidation (always lightweight)
        DiagnosticInspector::register();
        CacheCompatibilityManager::register();

        // 2. Community Stories & Real-Time Notifications (AJAX + UI)
        if ( hs_is_module_enabled( 'mod_community_social' ) ) {
            self::safe_boot( 'StoryEngine', array( \HackersShikkhok\Theme\Modules\Community\StoryEngine::class, 'init' ) );
            self::safe_boot( 'NotificationEngine', array( \HackersShikkhok\Theme\Modules\Community\NotificationEngine::class, 'init' ) );
            self::safe_boot( 'UniversalInteractionEngine', array( \HackersShikkhok\Theme\Modules\Community\UniversalInteractionEngine::class, 'register' ) );
            self::safe_boot( 'BookmarksAndNotifications', array( \HackersShikkhok\Theme\Modules\Community\BookmarksAndNotifications::class, 'register' ) );
            self::safe_boot( 'GamificationAndChallenges', array( \HackersShikkhok\Theme\Modules\Community\GamificationAndChallenges::class, 'register' ) );
        }

        // 3. Tools, CSS/RGB Lab, Code Library & Live Demo (hook registration only; heavy registries lazy-load on demand)
        if ( hs_is_module_enabled( 'mod_tools_engine' ) ) {
            self::safe_boot( 'ToolsEngine', array( \HackersShikkhok\Theme\Modules\Tools\ToolsEngine::class, 'register' ) );
            self::safe_boot( 'AdvancedWebToolsSuite', array( \HackersShikkhok\Theme\Modules\Tools\AdvancedWebToolsSuite::class, 'register' ) );
        }
        if ( hs_is_module_enabled( 'mod_css_rgb_lab' ) ) {
            self::safe_boot( 'CssRgbLab', array( \HackersShikkhok\Theme\Modules\Tools\CssRgbLab::class, 'register' ) );
        }
        if ( hs_is_module_enabled( 'mod_code_library' ) ) {
            self::safe_boot( 'CodeLibrary', array( \HackersShikkhok\Theme\Modules\Platform\CodeLibrary::class, 'register' ) );
        }
        if ( hs_is_module_enabled( 'mod_live_demo' ) ) {
            self::safe_boot( 'LiveDemo', array( \HackersShikkhok\Theme\Modules\Platform\LiveDemo::class, 'register' ) );
        }

        // 4. LMS, Academy, CTF & Wallet Economy
        if ( hs_is_module_enabled( 'mod_academy_lms' ) ) {
            self::safe_boot( 'CyberAcademyLmsEngine', array( \HackersShikkhok\Theme\Modules\LMS\CyberAcademyLmsEngine::class, 'register' ) );
            self::safe_boot( 'CourseContentImporter', array( \HackersShikkhok\Theme\Modules\LMS\CourseContentImporter::class, 'register' ) );
            self::safe_boot( 'Course_Manager', array( \HackersShikkhok\Theme\Modules\LMS\Course_Manager::class, 'register' ) );
            self::safe_boot( 'SVG_Annotator', array( \HackersShikkhok\Theme\Modules\LMS\SVG_Annotator::class, 'register' ) );
            self::safe_boot( 'Bulk_Course_Importer', array( \HackersShikkhok\Theme\Modules\LMS\Bulk_Course_Importer::class, 'register' ) );
            self::safe_boot( 'Theming_Engine', array( \HackersShikkhok\Theme\Modules\LMS\Theming_Engine::class, 'register' ) );
            self::safe_boot( 'Certificate_Engine', array( \HackersShikkhok\Theme\Modules\LMS\Certificate_Engine::class, 'register' ) );
        }
        if ( hs_is_module_enabled( 'mod_ctf_engine' ) ) {
            self::safe_boot( 'CTF_Engine', array( \HackersShikkhok\Theme\Modules\LMS\CTF_Engine::class, 'register' ) );
        }
        if ( hs_is_module_enabled( 'mod_wallet_economy' ) ) {
            self::safe_boot( 'Wallet_Economy', array( \HackersShikkhok\Theme\Modules\LMS\Wallet_Economy::class, 'register' ) );
        }
        if ( hs_is_module_enabled( 'mod_post_pay_counter' ) ) {
            self::safe_boot( 'PostPayCounterEngine', array( \HackersShikkhok\Theme\Modules\Monetization\PostPayCounterEngine::class, 'register' ) );
        }

        // 5. Security, Content Protection & Security SaaS
        self::safe_boot( 'SecurityManager', array( \HackersShikkhok\Theme\Modules\Security\SecurityManager::class, 'register' ) );
        if ( hs_is_module_enabled( 'enable_content_lock' ) ) {
            self::safe_boot( 'Content_Protection_Lock', array( \HackersShikkhok\Theme\Modules\Security\Content_Protection_Lock::class, 'register' ) );
        }
        if ( hs_is_module_enabled( 'enable_video_shield' ) ) {
            self::safe_boot( 'Video_Stream_Shield', array( \HackersShikkhok\Theme\Modules\LMS\Video_Stream_Shield::class, 'register' ) );
        }
        if ( hs_is_module_enabled( 'mod_security_saas' ) ) {
            self::safe_boot( 'SecurityAwarenessDefenseLab', array( \HackersShikkhok\Theme\Modules\Security\SecurityAwarenessDefenseLab::class, 'register' ) );
            self::safe_boot( 'SecuritySaaSEngine', array( \HackersShikkhok\Theme\Modules\Security\SecuritySaaSEngine::class, 'register' ) );
            self::safe_boot( 'SecurityIntelligenceEngine', array( \HackersShikkhok\Theme\Modules\Security\SecurityIntelligenceEngine::class, 'register' ) );
        }

        // 6. AI Systems & Cron Handlers (respecting Emergency Kill Switch)
        if ( hs_is_module_enabled( 'enable_ai_autopilot' ) ) {
            self::safe_boot( 'UniversalAutopilotEngine', array( \HackersShikkhok\Theme\Modules\AI\UniversalAutopilotEngine::class, 'register' ) );
            self::safe_boot( 'ApiRotationManager', array( \HackersShikkhok\Theme\Modules\AI\ApiRotationManager::class, 'register' ) );
            self::safe_boot( 'BotEcosystem', array( \HackersShikkhok\Theme\Modules\AI\BotEcosystem::class, 'register' ) );
            self::safe_boot( 'Autopilot', array( \HackersShikkhok\Theme\Modules\AI\Autopilot::class, 'register' ) );
            self::safe_boot( 'Behavioral_Analytics_Engine', array( \HackersShikkhok\Theme\Modules\AI\Behavioral_Analytics_Engine::class, 'register' ) );
            self::safe_boot( 'UltimateHackersMasterEngine', array( \HackersShikkhok\Theme\Modules\Platform\UltimateHackersMasterEngine::class, 'register' ) );
        }
        if ( hs_is_module_enabled( 'enable_ai_tutor' ) ) {
            self::safe_boot( 'AiTutorEngine', array( \HackersShikkhok\Theme\Modules\AI\AiTutorEngine::class, 'register' ) );
        }

        // 7. SEO, Sitemaps & AdSense
        self::safe_boot( 'SeoEngine', array( \HackersShikkhok\Theme\Modules\SEO\SeoEngine::class, 'register' ) );
        self::safe_boot( 'NativeSeoAndSitemapEngine', array( \HackersShikkhok\Theme\Modules\SEO\NativeSeoAndSitemapEngine::class, 'register' ) );
        self::safe_boot( 'GlobalTechnicalSeoEngine', array( \HackersShikkhok\Theme\Modules\SEO\GlobalTechnicalSeoEngine::class, 'register' ) );
        if ( hs_is_module_enabled( 'enable_xml_sitemap' ) ) {
            self::safe_boot( 'MultilingualSitemapEngine', array( \HackersShikkhok\Theme\Modules\SEO\MultilingualSitemapEngine::class, 'register' ) );
            self::safe_boot( 'GoogleSearchConsoleAndSitemapManager', array( \HackersShikkhok\Theme\Modules\SEO\GoogleSearchConsoleAndSitemapManager::class, 'register' ) );
            self::safe_boot( 'InstantIndexingEngine', array( \HackersShikkhok\Theme\Modules\SEO\InstantIndexingEngine::class, 'register' ) );
        }
        if ( hs_is_module_enabled( 'enable_auto_linking' ) ) {
            self::safe_boot( 'InternalLinker', array( \HackersShikkhok\Theme\Modules\SEO\InternalLinker::class, 'register' ) );
        }
        self::safe_boot( 'TranslationLedger', array( \HackersShikkhok\Theme\Modules\SEO\TranslationLedger::class, 'register' ) );
        self::safe_boot( 'AdSenseAutomationEngine', array( \HackersShikkhok\Theme\Modules\Monetization\AdSenseAutomationEngine::class, 'register' ) );
        self::safe_boot( 'NativeGoogleAndAdsEngine', array( \HackersShikkhok\Theme\Modules\Monetization\NativeGoogleAndAdsEngine::class, 'register' ) );

        // 8. Platform Centers, Device Lab, YouTube, Auth/Email & Users
        if ( hs_is_module_enabled( 'mod_platform_centers' ) ) {
            self::safe_boot( 'CenterManager', array( \HackersShikkhok\Theme\Modules\Platform\CenterManager::class, 'register' ) );
            self::safe_boot( 'PlatformHubEngine', array( \HackersShikkhok\Theme\Modules\Platform\PlatformHubEngine::class, 'register' ) );
            self::safe_boot( 'EcosystemControlCenter', array( \HackersShikkhok\Theme\Modules\Platform\EcosystemControlCenter::class, 'register' ) );
        }
        if ( hs_is_module_enabled( 'mod_device_games_lab' ) ) {
            self::safe_boot( 'HardwareAndIotEngine', array( \HackersShikkhok\Theme\Modules\Platform\HardwareAndIotEngine::class, 'register' ) );
            self::safe_boot( 'EngineeringAndCncLab', array( \HackersShikkhok\Theme\Modules\Platform\EngineeringAndCncLab::class, 'register' ) );
            self::safe_boot( 'EliteTriPlatformExpansionSuite', array( \HackersShikkhok\Theme\Modules\Platform\EliteTriPlatformExpansionSuite::class, 'register' ) );
            self::safe_boot( 'LabPeaksControllerStore', array( \HackersShikkhok\Theme\Modules\Platform\LabPeaksControllerStore::class, 'register' ) );
        }
        if ( hs_is_module_enabled( 'mod_youtube' ) ) {
            self::safe_boot( 'YouTubeManager', array( \HackersShikkhok\Theme\Modules\Platform\YouTubeManager::class, 'register' ) );
        }
        if ( hs_is_module_enabled( 'enable_branded_email' ) ) {
            self::safe_boot( 'NativeBrandedEmailEngine', array( \HackersShikkhok\Theme\Modules\Platform\NativeBrandedEmailEngine::class, 'register' ) );
        }
        if ( hs_is_module_enabled( 'enable_branded_auth' ) ) {
            self::safe_boot( 'NativeBrandedAuthEngine', array( \HackersShikkhok\Theme\Modules\Platform\NativeBrandedAuthEngine::class, 'register' ) );
        }
        self::safe_boot( 'NativeClassicEditorEngine', array( \HackersShikkhok\Theme\Modules\Platform\NativeClassicEditorEngine::class, 'register' ) );
        self::safe_boot( 'UserEcosystem', array( \HackersShikkhok\Theme\Modules\Platform\UserEcosystem::class, 'register' ) );
        self::safe_boot( 'MetricsManager', array( \HackersShikkhok\Theme\Modules\Platform\MetricsManager::class, 'register' ) );

        // 9. Admin-Only Modules
        if ( is_admin() ) {
            self::safe_boot( 'ControlCenter', array( \HackersShikkhok\Theme\Admin\ControlCenter::class, 'register' ) );
            self::safe_boot( 'PostPayCounterAdmin', array( \HackersShikkhok\Theme\Admin\PostPayCounterAdmin::class, 'register' ) );
            self::safe_boot( 'ContentStudioMetaEngine', array( \HackersShikkhok\Theme\Admin\ContentStudioMetaEngine::class, 'register' ) );
        }
    }

    private static function safe_boot( string $name, callable|array $callback ): void {
        try {
            if ( is_callable( $callback ) ) {
                call_user_func( $callback );
            }
        } catch ( \Throwable $e ) {
            DiagnosticInspector::log_system_error( $name, $e );
        }
    }
}
