<?php
/**
 * Lazy PSR-4 Autoloader for HackersShikkhok Modular Theme.
 * Ensures heavy registries (Tools, LMS, AI) are only loaded into memory when explicitly requested.
 * Also provides seamless class_alias() backward compatibility for migrated HackersShikkhok\Core\* classes.
 */

declare(strict_types=1);

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

spl_autoload_register( static function ( string $class ): void {
    static $legacy_aliases = array(
        'HackersShikkhok\Core\AI\AiEngine' => 'HackersShikkhok\Theme\Modules\AI\AiEngine',
        'HackersShikkhok\Core\AI\AiTutorEngine' => 'HackersShikkhok\Theme\Modules\AI\AiTutorEngine',
        'HackersShikkhok\Core\AI\ApiRotationManager' => 'HackersShikkhok\Theme\Modules\AI\ApiRotationManager',
        'HackersShikkhok\Core\AI\Autopilot' => 'HackersShikkhok\Theme\Modules\AI\Autopilot',
        'HackersShikkhok\Core\AI\AutopilotRegistry' => 'HackersShikkhok\Theme\Modules\AI\AutopilotRegistry',
        'HackersShikkhok\Core\AI\AutopilotScheduler' => 'HackersShikkhok\Theme\Modules\AI\AutopilotScheduler',
        'HackersShikkhok\Core\AI\AutopilotSuite' => 'HackersShikkhok\Theme\Modules\AI\AutopilotSuite',
        'HackersShikkhok\Core\AI\Behavioral_Analytics_Engine' => 'HackersShikkhok\Theme\Modules\AI\Behavioral_Analytics_Engine',
        'HackersShikkhok\Core\AI\BotEcosystem' => 'HackersShikkhok\Theme\Modules\AI\BotEcosystem',
        'HackersShikkhok\Core\AI\ProjectBuilder' => 'HackersShikkhok\Theme\Modules\AI\ProjectBuilder',
        'HackersShikkhok\Core\AI\ProvenanceManager' => 'HackersShikkhok\Theme\Modules\AI\ProvenanceManager',
        'HackersShikkhok\Core\AI\QualityGate' => 'HackersShikkhok\Theme\Modules\AI\QualityGate',
        'HackersShikkhok\Core\AI\UniversalAutopilotEngine' => 'HackersShikkhok\Theme\Modules\AI\UniversalAutopilotEngine',
        'HackersShikkhok\Core\AI\UniversalFactoryAndEmergencyManager' => 'HackersShikkhok\Theme\Modules\AI\UniversalFactoryAndEmergencyManager',
        'HackersShikkhok\Core\API\RestController' => 'HackersShikkhok\Theme\API\RestController',
        'HackersShikkhok\Core\Academy\CourseContentImporter' => 'HackersShikkhok\Theme\Modules\LMS\CourseContentImporter',
        'HackersShikkhok\Core\Academy\CyberAcademyLmsEngine' => 'HackersShikkhok\Theme\Modules\LMS\CyberAcademyLmsEngine',
        'HackersShikkhok\Core\AdSense\AdSenseAutomationEngine' => 'HackersShikkhok\Theme\Modules\Monetization\AdSenseAutomationEngine',
        'HackersShikkhok\Core\Admin\ContentStudioMetaEngine' => 'HackersShikkhok\Theme\Admin\ContentStudioMetaEngine',
        'HackersShikkhok\Core\Auth\NativeBrandedAuthEngine' => 'HackersShikkhok\Theme\Modules\Platform\NativeBrandedAuthEngine',
        'HackersShikkhok\Core\Centers\CenterManager' => 'HackersShikkhok\Theme\Modules\Platform\CenterManager',
        'HackersShikkhok\Core\Centers\PlatformHubEngine' => 'HackersShikkhok\Theme\Modules\Platform\PlatformHubEngine',
        'HackersShikkhok\Core\Code\CodeLibrary' => 'HackersShikkhok\Theme\Modules\Platform\CodeLibrary',
        'HackersShikkhok\Core\Code\CodeValidator' => 'HackersShikkhok\Theme\Modules\Platform\CodeValidator',
        'HackersShikkhok\Core\Community\BookmarksAndNotifications' => 'HackersShikkhok\Theme\Modules\Community\BookmarksAndNotifications',
        'HackersShikkhok\Core\Community\EngagementManager' => 'HackersShikkhok\Theme\Modules\Community\EngagementManager',
        'HackersShikkhok\Core\Community\ForumManager' => 'HackersShikkhok\Theme\Modules\Community\ForumManager',
        'HackersShikkhok\Core\Community\GamificationAndChallenges' => 'HackersShikkhok\Theme\Modules\Community\GamificationAndChallenges',
        'HackersShikkhok\Core\Community\UniversalInteractionEngine' => 'HackersShikkhok\Theme\Modules\Community\UniversalInteractionEngine',
        'HackersShikkhok\Core\Core\BackupEngine' => 'HackersShikkhok\Theme\Core\BackupEngine',
        'HackersShikkhok\Core\Core\CacheCompatibilityManager' => 'HackersShikkhok\Theme\Core\CacheCompatibilityManager',
        'HackersShikkhok\Core\Core\DiagnosticInspector' => 'HackersShikkhok\Theme\Core\DiagnosticInspector',
        'HackersShikkhok\Core\Core\EcosystemControlCenter' => 'HackersShikkhok\Theme\Modules\Platform\EcosystemControlCenter',
        'HackersShikkhok\Core\Core\EcosystemServices' => 'HackersShikkhok\Theme\Modules\Platform\EcosystemServices',
        'HackersShikkhok\Core\Core\EliteGlobalPlatformEngine' => 'HackersShikkhok\Theme\Modules\Platform\EliteGlobalPlatformEngine',
        'HackersShikkhok\Core\Core\Logger' => 'HackersShikkhok\Theme\Core\Logger',
        'HackersShikkhok\Core\Core\Settings' => 'HackersShikkhok\Theme\Core\Settings',
        'HackersShikkhok\Core\Core\SystemHealth' => 'HackersShikkhok\Theme\Core\SystemHealth',
        'HackersShikkhok\Core\Core\UltimateHackersMasterEngine' => 'HackersShikkhok\Theme\Modules\Platform\UltimateHackersMasterEngine',
        'HackersShikkhok\Core\Dashboard\ControlCenter' => 'HackersShikkhok\Theme\Admin\ControlCenter',
        'HackersShikkhok\Core\Dashboard\PostPayCounterAdmin' => 'HackersShikkhok\Theme\Admin\PostPayCounterAdmin',
        'HackersShikkhok\Core\Demo\LiveDemo' => 'HackersShikkhok\Theme\Modules\Platform\LiveDemo',
        'HackersShikkhok\Core\Downloads\DownloadManager' => 'HackersShikkhok\Theme\Modules\Platform\DownloadManager',
        'HackersShikkhok\Core\Earnings\PostPayCounterEngine' => 'HackersShikkhok\Theme\Modules\Monetization\PostPayCounterEngine',
        'HackersShikkhok\Core\Editor\NativeClassicEditorEngine' => 'HackersShikkhok\Theme\Modules\Platform\NativeClassicEditorEngine',
        'HackersShikkhok\Core\Email\NativeBrandedEmailEngine' => 'HackersShikkhok\Theme\Modules\Platform\NativeBrandedEmailEngine',
        'HackersShikkhok\Core\GitHub\GitHubManager' => 'HackersShikkhok\Theme\Modules\Platform\GitHubManager',
        'HackersShikkhok\Core\Integrations\NativeGoogleAndAdsEngine' => 'HackersShikkhok\Theme\Modules\Monetization\NativeGoogleAndAdsEngine',
        'HackersShikkhok\Core\LMS\Bulk_Course_Importer' => 'HackersShikkhok\Theme\Modules\LMS\Bulk_Course_Importer',
        'HackersShikkhok\Core\LMS\CTF_Engine' => 'HackersShikkhok\Theme\Modules\LMS\CTF_Engine',
        'HackersShikkhok\Core\LMS\Certificate_Engine' => 'HackersShikkhok\Theme\Modules\LMS\Certificate_Engine',
        'HackersShikkhok\Core\LMS\Course_Manager' => 'HackersShikkhok\Theme\Modules\LMS\Course_Manager',
        'HackersShikkhok\Core\LMS\SVG_Annotator' => 'HackersShikkhok\Theme\Modules\LMS\SVG_Annotator',
        'HackersShikkhok\Core\LMS\Theming_Engine' => 'HackersShikkhok\Theme\Modules\LMS\Theming_Engine',
        'HackersShikkhok\Core\LMS\Video_Stream_Shield' => 'HackersShikkhok\Theme\Modules\LMS\Video_Stream_Shield',
        'HackersShikkhok\Core\LMS\Wallet_Economy' => 'HackersShikkhok\Theme\Modules\LMS\Wallet_Economy',
        'HackersShikkhok\Core\Labs\DeviceAndGamesManager' => 'HackersShikkhok\Theme\Modules\Platform\DeviceAndGamesManager',
        'HackersShikkhok\Core\Labs\EliteTriPlatformExpansionSuite' => 'HackersShikkhok\Theme\Modules\Platform\EliteTriPlatformExpansionSuite',
        'HackersShikkhok\Core\Labs\EngineeringAndCncLab' => 'HackersShikkhok\Theme\Modules\Platform\EngineeringAndCncLab',
        'HackersShikkhok\Core\Labs\HardwareAndIotEngine' => 'HackersShikkhok\Theme\Modules\Platform\HardwareAndIotEngine',
        'HackersShikkhok\Core\Labs\LabPeaksControllerStore' => 'HackersShikkhok\Theme\Modules\Platform\LabPeaksControllerStore',
        'HackersShikkhok\Core\Localization\TranslationLedger' => 'HackersShikkhok\Theme\Modules\SEO\TranslationLedger',
        'HackersShikkhok\Core\Metrics\MetricsManager' => 'HackersShikkhok\Theme\Modules\Platform\MetricsManager',
        'HackersShikkhok\Core\SEO\GlobalTechnicalSeoEngine' => 'HackersShikkhok\Theme\Modules\SEO\GlobalTechnicalSeoEngine',
        'HackersShikkhok\Core\SEO\GoogleSearchConsoleAndSitemapManager' => 'HackersShikkhok\Theme\Modules\SEO\GoogleSearchConsoleAndSitemapManager',
        'HackersShikkhok\Core\SEO\InstantIndexingEngine' => 'HackersShikkhok\Theme\Modules\SEO\InstantIndexingEngine',
        'HackersShikkhok\Core\SEO\InternalLinker' => 'HackersShikkhok\Theme\Modules\SEO\InternalLinker',
        'HackersShikkhok\Core\SEO\MultilingualSitemapEngine' => 'HackersShikkhok\Theme\Modules\SEO\MultilingualSitemapEngine',
        'HackersShikkhok\Core\SEO\NativeSeoAndSitemapEngine' => 'HackersShikkhok\Theme\Modules\SEO\NativeSeoAndSitemapEngine',
        'HackersShikkhok\Core\SEO\SeoEngine' => 'HackersShikkhok\Theme\Modules\SEO\SeoEngine',
        'HackersShikkhok\Core\SecuritySaaS\AiRemediationEngine' => 'HackersShikkhok\Theme\Modules\Security\AiRemediationEngine',
        'HackersShikkhok\Core\SecuritySaaS\ComplianceChecklistEngine' => 'HackersShikkhok\Theme\Modules\Security\ComplianceChecklistEngine',
        'HackersShikkhok\Core\SecuritySaaS\FindingsLedger' => 'HackersShikkhok\Theme\Modules\Security\FindingsLedger',
        'HackersShikkhok\Core\SecuritySaaS\PassiveAuditorEngine' => 'HackersShikkhok\Theme\Modules\Security\PassiveAuditorEngine',
        'HackersShikkhok\Core\SecuritySaaS\PdfReportGenerator' => 'HackersShikkhok\Theme\Modules\Security\PdfReportGenerator',
        'HackersShikkhok\Core\SecuritySaaS\RedTeamEngagementManager' => 'HackersShikkhok\Theme\Modules\Security\RedTeamEngagementManager',
        'HackersShikkhok\Core\SecuritySaaS\RulesOfEngagementManager' => 'HackersShikkhok\Theme\Modules\Security\RulesOfEngagementManager',
        'HackersShikkhok\Core\SecuritySaaS\ScanSchedulerQueue' => 'HackersShikkhok\Theme\Modules\Security\ScanSchedulerQueue',
        'HackersShikkhok\Core\SecuritySaaS\SecurityIntelligenceEngine' => 'HackersShikkhok\Theme\Modules\Security\SecurityIntelligenceEngine',
        'HackersShikkhok\Core\SecuritySaaS\SecuritySaaSEngine' => 'HackersShikkhok\Theme\Modules\Security\SecuritySaaSEngine',
        'HackersShikkhok\Core\SecuritySaaS\SecurityWebhooksDispatcher' => 'HackersShikkhok\Theme\Modules\Security\SecurityWebhooksDispatcher',
        'HackersShikkhok\Core\SecuritySaaS\SubscriptionAndBillingManager' => 'HackersShikkhok\Theme\Modules\Security\SubscriptionAndBillingManager',
        'HackersShikkhok\Core\SecuritySaaS\TargetManager' => 'HackersShikkhok\Theme\Modules\Security\TargetManager',
        'HackersShikkhok\Core\Security\Content_Protection_Lock' => 'HackersShikkhok\Theme\Modules\Security\Content_Protection_Lock',
        'HackersShikkhok\Core\Security\SecurityAwarenessDefenseLab' => 'HackersShikkhok\Theme\Modules\Security\SecurityAwarenessDefenseLab',
        'HackersShikkhok\Core\Security\SecurityManager' => 'HackersShikkhok\Theme\Modules\Security\SecurityManager',
        'HackersShikkhok\Core\Tools\AdvancedWebToolsSuite' => 'HackersShikkhok\Theme\Modules\Tools\AdvancedWebToolsSuite',
        'HackersShikkhok\Core\Tools\CssRgbLab' => 'HackersShikkhok\Theme\Modules\Tools\CssRgbLab',
        'HackersShikkhok\Core\Tools\Master1000ToolsRegistry' => 'HackersShikkhok\Theme\Modules\Tools\Master1000ToolsRegistry',
        'HackersShikkhok\Core\Tools\NextGen498ToolsRegistry' => 'HackersShikkhok\Theme\Modules\Tools\NextGen498ToolsRegistry',
        'HackersShikkhok\Core\Tools\ToolExecutionEngine' => 'HackersShikkhok\Theme\Modules\Tools\ToolExecutionEngine',
        'HackersShikkhok\Core\Tools\ToolFactory' => 'HackersShikkhok\Theme\Modules\Tools\ToolFactory',
        'HackersShikkhok\Core\Tools\ToolsEngine' => 'HackersShikkhok\Theme\Modules\Tools\ToolsEngine',
        'HackersShikkhok\Core\Tools\UniversalToolCapabilityRegistry' => 'HackersShikkhok\Theme\Modules\Tools\UniversalToolCapabilityRegistry',
        'HackersShikkhok\Core\Tools\UniversalToolRegistry' => 'HackersShikkhok\Theme\Modules\Tools\UniversalToolRegistry',
        'HackersShikkhok\Core\Tools\UniversalToolRegistryExtra' => 'HackersShikkhok\Theme\Modules\Tools\UniversalToolRegistryExtra',
        'HackersShikkhok\Core\Users\PasskeyAndGeoManager' => 'HackersShikkhok\Theme\Modules\Platform\PasskeyAndGeoManager',
        'HackersShikkhok\Core\Users\SubmissionManager' => 'HackersShikkhok\Theme\Modules\Platform\SubmissionManager',
        'HackersShikkhok\Core\Users\UserEcosystem' => 'HackersShikkhok\Theme\Modules\Platform\UserEcosystem',
        'HackersShikkhok\Core\YouTube\YouTubeManager' => 'HackersShikkhok\Theme\Modules\Platform\YouTubeManager',
    );

    if ( isset( $legacy_aliases[ $class ] ) ) {
        $target = $legacy_aliases[ $class ];
        if ( class_exists( $target ) && ! class_exists( $class, false ) ) {
            class_alias( $target, $class );
        }
        return;
    }

    $prefix = 'HackersShikkhok\\Theme\\';
    if ( strncmp( $prefix, $class, strlen( $prefix ) ) !== 0 ) {
        return;
    }

    $relative = substr( $class, strlen( $prefix ) );
    $parts    = explode( '\\', $relative );
    $cls_file = array_pop( $parts ) . '.php';

    $dir_map = array(
        'Core'                 => 'inc/core',
        'Admin'                => 'inc/admin',
        'API'                  => 'inc/api',
        'Modules\AI'           => 'inc/modules/ai',
        'Modules\Tools'        => 'inc/modules/tools',
        'Modules\LMS'          => 'inc/modules/lms',
        'Modules\Community'    => 'inc/modules/community',
        'Modules\Security'     => 'inc/modules/security',
        'Modules\SEO'          => 'inc/modules/seo',
        'Modules\Monetization' => 'inc/modules/monetization',
        'Modules\Platform'     => 'inc/modules/platform',
    );

    $ns_key = implode( '\\', $parts );
    if ( isset( $dir_map[ $ns_key ] ) ) {
        $path = HS_THEME_DIR . '/' . $dir_map[ $ns_key ] . '/' . $cls_file;
        if ( file_exists( $path ) ) {
            require_once $path;
        }
        return;
    }

    // Generic fallback mapping
    $fallback = HS_THEME_DIR . '/inc/' . strtolower( implode( '/', $parts ) ) . '/' . $cls_file;
    if ( file_exists( $fallback ) ) {
        require_once $fallback;
    }
} );
