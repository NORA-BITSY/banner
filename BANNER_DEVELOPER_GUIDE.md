# HumHub Banner Module - Developer Guide

## Table of Contents
1. [Architecture Overview](#architecture-overview)
2. [Module Structure](#module-structure)
3. [Core Components](#core-components)
4. [Event System](#event-system)
5. [Development Patterns](#development-patterns)
6. [Integration Examples](#integration-examples)
7. [Creating Custom Banner Types](#creating-custom-banner-types)
8. [Advanced Use Cases](#advanced-use-cases)
9. [Testing & Debugging](#testing--debugging)
10. [Security Considerations](#security-considerations)
11. [Performance Optimization](#performance-optimization)
12. [API Reference](#api-reference)

## Architecture Overview

The Banner Module follows HumHub's modular architecture pattern, providing a flexible system for displaying site-wide notifications. It's built on Yii2 framework principles with event-driven extensibility.

### Key Design Principles
- **Event-Driven**: Extensible through HumHub's event system
- **MVC Pattern**: Clean separation of logic, presentation, and data
- **Configuration-First**: Settings-based approach for easy management
- **Theme Agnostic**: CSS variable-based styling for compatibility
- **Performance Conscious**: Minimal overhead with smart asset loading

### Module Lifecycle
```
1. Module Registration (config.php)
   ↓
2. Event Binding (View::EVENT_BEGIN_BODY)
   ↓
3. Configuration Loading (with EVENT_AFTER_GET_CONFIGURATION)
   ↓
4. Content Rendering (based on user state and settings)
   ↓
5. Asset Injection (CSS/JS only when banner is shown)
```

## Module Structure

```
banner/
├── assets/
│   └── BannerAssets.php          # Asset bundle for CSS/JS
├── controllers/
│   └── ConfigController.php      # Admin configuration controller
├── messages/                     # i18n translations
│   └── [language]/
│       ├── base.php
│       └── config.php
├── models/
│   ├── Configuration.php         # Configuration model
│   └── BannerType.php           # Banner type constants
├── resources/
│   ├── css/
│   │   └── humhub.banner.css    # Banner styles
│   ├── js/
│   │   └── humhub.banner.js     # Banner JavaScript
│   └── module_image.png         # Module icon
├── views/
│   ├── banner/
│   │   ├── index.php            # Main banner view
│   │   └── types/               # Pre-built banner templates
│   └── config/
│       └── index.php            # Admin configuration view
├── config.php                   # Module configuration
├── Events.php                   # Event handlers
├── Module.php                   # Module class
└── module.json                  # Module metadata
```

## Core Components

### 1. Module Class (`Module.php`)

The central module class manages configuration and provides the main API:

```php
class Module extends \humhub\components\Module
{
    // Event constant for configuration override
    public const EVENT_AFTER_GET_CONFIGURATION = 'afterGetBannerConfiguration';
    
    // Configuration getter with event trigger
    public function getConfiguration(): Configuration
    {
        if ($this->_configuration === null) {
            $this->_configuration = new Configuration(['settingsManager' => $this->settings]);
            $this->_configuration->loadBySettings();
            
            // Trigger event for external modification
            $evt = new Event(['result' => $this->_configuration]);
            Event::trigger($this, static::EVENT_AFTER_GET_CONFIGURATION, $evt);
            $this->_configuration = $evt->result;
        }
        
        return $this->_configuration;
    }
}
```

### 2. Configuration Model (`models/Configuration.php`)

Manages banner settings with validation:

```php
class Configuration extends Model
{
    // Properties
    public bool $enabled = false;
    public ?string $content = '';
    public ?string $contentGuests = '';
    public bool $closeButton = false;
    public string $style = 'info';
    public string $bannerType = BannerType::MANUAL;
    
    // Validation rules
    public function rules()
    {
        return [
            [['enabled', 'closeButton'], 'boolean'],
            [['content', 'contentGuests'], 'string'],
            ['style', 'in', 'range' => ['info', 'success', 'warning', 'danger']],
            ['bannerType', 'in', 'range' => BannerType::getTypes()],
        ];
    }
}
```

### 3. Event Handler (`Events.php`)

Handles the view rendering event:

```php
class Events
{
    public static function onViewBeginBody(Event $event)
    {
        // Skip AJAX requests
        if (Yii::$app->request->isAjax) {
            return;
        }
        
        // Get configuration (triggers EVENT_AFTER_GET_CONFIGURATION)
        $configuration = $module->getConfiguration();
        
        // Render banner if enabled with content
        if ($configuration->enabled && !empty($content)) {
            BannerAssets::register($view);
            echo Yii::$app->controller->renderPartial('@banner/views/banner/index', [
                'configuration' => $configuration
            ]);
        }
    }
}
```

### 4. Asset Bundle (`assets/BannerAssets.php`)

Manages CSS and JavaScript resources:

```php
class BannerAssets extends AssetBundle
{
    public $sourcePath = '@banner/resources';
    public $css = ['css/humhub.banner.css'];
    public $js = ['js/humhub.banner.js'];
}
```

## Event System

### EVENT_AFTER_GET_CONFIGURATION

The primary extension point for developers to modify banner behavior:

```php
// Basic event handler
Event::on(
    \humhub\modules\banner\Module::class,
    \humhub\modules\banner\Module::EVENT_AFTER_GET_CONFIGURATION,
    function($event) {
        /** @var Configuration $configuration */
        $configuration = $event->result;
        
        // Modify configuration
        $configuration->enabled = true;
        $configuration->content = 'Dynamic content';
    }
);
```

### Event Handler Registration

Register in your module's `config.php`:

```php
return [
    'id' => 'my-module',
    'class' => MyModule::class,
    'events' => [
        [
            'class' => \humhub\modules\banner\Module::class,
            'event' => \humhub\modules\banner\Module::EVENT_AFTER_GET_CONFIGURATION,
            'callback' => [MyEvents::class, 'onBannerConfiguration'],
        ],
    ],
];
```

## Development Patterns

### 1. Conditional Banner Display

Show banners based on custom conditions:

```php
class MyEvents
{
    public static function onBannerConfiguration($event)
    {
        $configuration = $event->result;
        
        // Show during business hours only
        $hour = (int)date('H');
        if ($hour < 9 || $hour > 17) {
            $configuration->enabled = false;
            return;
        }
        
        // Show to specific user groups
        if (Yii::$app->user->isGuest) {
            $configuration->content = 'Please login for full access';
            $configuration->style = 'info';
        } elseif (Yii::$app->user->identity->isSystemAdmin()) {
            $configuration->content = 'Admin: 5 pending approvals';
            $configuration->style = 'warning';
        }
    }
}
```

### 2. Dynamic Content Generation

Generate banner content from external sources:

```php
public static function onBannerConfiguration($event)
{
    $configuration = $event->result;
    
    // Fetch from API
    try {
        $alertData = file_get_contents('https://api.example.com/alerts');
        $alerts = json_decode($alertData, true);
        
        if (!empty($alerts)) {
            $configuration->enabled = true;
            $configuration->content = self::formatAlerts($alerts);
            $configuration->style = $alerts[0]['severity'] ?? 'info';
        }
    } catch (\Exception $e) {
        Yii::error('Failed to fetch alerts: ' . $e->getMessage());
    }
}

private static function formatAlerts($alerts)
{
    $html = '<div class="alerts-container">';
    foreach ($alerts as $alert) {
        $html .= sprintf(
            '<span class="alert-item">%s: %s</span> ',
            Html::encode($alert['title']),
            Html::encode($alert['message'])
        );
    }
    $html .= '</div>';
    return $html;
}
```

### 3. User-Specific Banners

Show personalized banners:

```php
public static function onBannerConfiguration($event)
{
    $configuration = $event->result;
    $user = Yii::$app->user->identity;
    
    if (!$user) {
        return;
    }
    
    // Birthday banner
    $birthday = new DateTime($user->profile->birthday);
    $today = new DateTime();
    if ($birthday->format('md') === $today->format('md')) {
        $configuration->enabled = true;
        $configuration->content = "🎉 Happy Birthday, {$user->displayName}! 🎂";
        $configuration->style = 'success';
        $configuration->closeButton = true;
        return;
    }
    
    // Task reminders
    $pendingTasks = Task::find()
        ->where(['user_id' => $user->id, 'status' => 'pending'])
        ->count();
    
    if ($pendingTasks > 0) {
        $configuration->enabled = true;
        $configuration->content = sprintf(
            'You have <strong>%d pending tasks</strong>. <a href="/tasks">View Tasks</a>',
            $pendingTasks
        );
        $configuration->style = 'warning';
    }
}
```

### 4. Space-Aware Banners

Show different banners based on current space:

```php
public static function onBannerConfiguration($event)
{
    $configuration = $event->result;
    $space = Yii::$app->controller->contentContainer ?? null;
    
    if (!$space instanceof \humhub\modules\space\models\Space) {
        return;
    }
    
    // Space-specific announcements
    $announcement = SpaceAnnouncement::find()
        ->where(['space_id' => $space->id, 'active' => 1])
        ->orderBy(['priority' => SORT_DESC])
        ->one();
    
    if ($announcement) {
        $configuration->enabled = true;
        $configuration->content = $announcement->content;
        $configuration->style = $announcement->style;
        $configuration->bannerType = BannerType::MANUAL;
    }
}
```

## Integration Examples

### Example 1: Weather Alert Integration

Create a weather monitoring module that displays alerts:

```php
// modules/weather/Events.php
namespace app\modules\weather;

use humhub\modules\banner\models\BannerType;
use humhub\modules\banner\models\Configuration;

class Events
{
    public static function onBannerConfiguration($event)
    {
        /** @var Configuration $configuration */
        $configuration = $event->result;
        
        // Check weather API
        $weather = WeatherService::getCurrentAlerts();
        
        if ($weather->hasAlerts()) {
            $configuration->enabled = true;
            $configuration->bannerType = BannerType::WEATHER_ALERT;
            $configuration->style = $weather->getSeverity(); // 'warning' or 'danger'
            $configuration->content = sprintf(
                '<strong>⚠️ Weather Alert:</strong> %s. Valid until %s.',
                $weather->getDescription(),
                $weather->getExpiration()->format('g:i A')
            );
            $configuration->closeButton = false; // Force visibility
        }
    }
}
```

### Example 2: Maintenance Mode Integration

```php
// modules/maintenance/Events.php
namespace app\modules\maintenance;

class Events
{
    public static function onBannerConfiguration($event)
    {
        $configuration = $event->result;
        $maintenance = MaintenanceMode::getCurrent();
        
        if ($maintenance && $maintenance->isActive()) {
            $configuration->enabled = true;
            $configuration->bannerType = BannerType::MAINTENANCE_WINDOW;
            $configuration->style = 'warning';
            
            // Different messages for different user types
            if (Yii::$app->user->isAdmin()) {
                $configuration->content = sprintf(
                    '🔧 Maintenance Mode Active (Admin View). Ends: %s. <a href="/admin/maintenance">Manage</a>',
                    $maintenance->end_time
                );
            } else {
                $configuration->content = sprintf(
                    '🔧 Scheduled maintenance in progress. Expected completion: %s',
                    $maintenance->end_time
                );
            }
            
            $configuration->closeButton = false;
        }
    }
}
```

### Example 3: A/B Testing Integration

```php
// modules/abtesting/Events.php
namespace app\modules\abtesting;

class Events
{
    public static function onBannerConfiguration($event)
    {
        $configuration = $event->result;
        $test = ABTest::getActive('banner_cta');
        
        if (!$test) {
            return;
        }
        
        $variant = $test->getVariantForUser(Yii::$app->user->id);
        
        switch ($variant) {
            case 'A':
                $configuration->content = 'New feature available! <a href="/features">Learn More</a>';
                $configuration->style = 'info';
                break;
            case 'B':
                $configuration->content = '🚀 Try our latest feature! <a href="/features" class="btn btn-sm btn-primary">Get Started</a>';
                $configuration->style = 'success';
                break;
        }
        
        // Track impression
        $test->trackImpression($variant, Yii::$app->user->id);
        
        // Add tracking for clicks
        $configuration->content = str_replace(
            'href="/features"',
            'href="/features" onclick="ABTest.track(\'banner_click\', \'' . $variant . '\')"',
            $configuration->content
        );
    }
}
```

## Creating Custom Banner Types

### Step 1: Define the Type Constant

Extend the BannerType class in your module:

```php
// modules/mymodule/models/ExtendedBannerType.php
namespace app\modules\mymodule\models;

use humhub\modules\banner\models\BannerType;

class ExtendedBannerType extends BannerType
{
    public const SYSTEM_UPDATE = 'system_update';
    public const POLL_ACTIVE = 'poll_active';
    public const ACHIEVEMENT = 'achievement';
}
```

### Step 2: Create the View Template

```php
// modules/mymodule/views/banner/types/system_update.php
<?php
use yii\helpers\Html;

/** @var array $updateInfo */
$updateInfo = Yii::$app->getModule('mymodule')->getUpdateInfo();
?>

<div class="system-update-banner">
    <i class="fa fa-download"></i>
    <strong>System Update Available:</strong>
    Version <?= Html::encode($updateInfo['version']) ?> -
    <?= Html::encode($updateInfo['description']) ?>
    <a href="<?= Html::encode($updateInfo['url']) ?>" class="btn btn-xs btn-primary">
        Update Now
    </a>
</div>
```

### Step 3: Register Custom Type

```php
// In your module's init() method
public function init()
{
    parent::init();
    
    // Add custom banner types
    Event::on(
        BannerType::class,
        'getTypes',
        function($event) {
            $event->types[] = ExtendedBannerType::SYSTEM_UPDATE;
            $event->types[] = ExtendedBannerType::POLL_ACTIVE;
            $event->types[] = ExtendedBannerType::ACHIEVEMENT;
        }
    );
}
```

### Step 4: Handle Custom Rendering

```php
public static function onBannerConfiguration($event)
{
    $configuration = $event->result;
    
    // Check for system updates
    if (SystemUpdate::hasAvailable()) {
        $configuration->enabled = true;
        $configuration->bannerType = ExtendedBannerType::SYSTEM_UPDATE;
        $configuration->style = 'info';
        // Custom type will use your template
    }
}
```

## Advanced Use Cases

### 1. Multi-Banner Queue System

Implement a queue system for multiple banners:

```php
class BannerQueueService
{
    private static $banners = [];
    
    public static function addBanner($id, $content, $priority = 0, $options = [])
    {
        self::$banners[$id] = [
            'content' => $content,
            'priority' => $priority,
            'options' => array_merge([
                'style' => 'info',
                'closeButton' => true,
                'duration' => null,
            ], $options)
        ];
    }
    
    public static function getNextBanner()
    {
        if (empty(self::$banners)) {
            return null;
        }
        
        // Sort by priority
        uasort(self::$banners, function($a, $b) {
            return $b['priority'] - $a['priority'];
        });
        
        // Get highest priority
        $banner = reset(self::$banners);
        $id = key(self::$banners);
        
        // Check if already shown this session
        $shownBanners = Yii::$app->session->get('shown_banners', []);
        if (in_array($id, $shownBanners)) {
            unset(self::$banners[$id]);
            return self::getNextBanner();
        }
        
        return ['id' => $id, 'data' => $banner];
    }
    
    public static function markAsShown($id)
    {
        $shown = Yii::$app->session->get('shown_banners', []);
        $shown[] = $id;
        Yii::$app->session->set('shown_banners', $shown);
    }
}

// Usage in event handler
public static function onBannerConfiguration($event)
{
    // Add various banners to queue
    if ($unreadMessages = User::getUnreadMessageCount()) {
        BannerQueueService::addBanner(
            'unread_messages',
            "You have {$unreadMessages} unread messages",
            10,
            ['style' => 'info']
        );
    }
    
    if ($pendingApprovals = Admin::getPendingApprovals()) {
        BannerQueueService::addBanner(
            'pending_approvals',
            "Admin: {$pendingApprovals} items need approval",
            20,
            ['style' => 'warning', 'closeButton' => false]
        );
    }
    
    // Get highest priority banner
    if ($nextBanner = BannerQueueService::getNextBanner()) {
        $configuration->enabled = true;
        $configuration->content = $nextBanner['data']['content'];
        $configuration->style = $nextBanner['data']['options']['style'];
        $configuration->closeButton = $nextBanner['data']['options']['closeButton'];
        
        // Mark as shown
        BannerQueueService::markAsShown($nextBanner['id']);
    }
}
```

### 2. Time-Based Campaign System

```php
class BannerCampaignService
{
    public static function getActiveCampaign()
    {
        return Campaign::find()
            ->where(['<=', 'start_date', new Expression('NOW()')])
            ->andWhere(['>=', 'end_date', new Expression('NOW()')])
            ->andWhere(['active' => 1])
            ->orderBy(['priority' => SORT_DESC])
            ->one();
    }
    
    public static function shouldShowToUser($campaign, $userId)
    {
        // Check targeting rules
        $targeting = $campaign->getTargeting();
        
        // User groups
        if (!empty($targeting['groups'])) {
            $userGroups = User::findOne($userId)->getGroups();
            if (!array_intersect($targeting['groups'], $userGroups)) {
                return false;
            }
        }
        
        // Time zones
        if (!empty($targeting['timezones'])) {
            $userTz = User::findOne($userId)->time_zone;
            if (!in_array($userTz, $targeting['timezones'])) {
                return false;
            }
        }
        
        // Frequency capping
        $impressions = CampaignImpression::find()
            ->where(['campaign_id' => $campaign->id, 'user_id' => $userId])
            ->count();
            
        if ($impressions >= $campaign->frequency_cap) {
            return false;
        }
        
        return true;
    }
}
```

### 3. Real-Time Notification System

```php
class RealTimeBannerService
{
    public static function registerPushHandlers()
    {
        // WebSocket integration
        Yii::$app->push->on('notification', function($data) {
            if ($data['type'] === 'banner') {
                Yii::$app->session->set('realtime_banner', [
                    'content' => $data['content'],
                    'style' => $data['style'] ?? 'info',
                    'expires' => time() + ($data['duration'] ?? 300)
                ]);
            }
        });
    }
    
    public static function checkRealTimeBanner($configuration)
    {
        $realtimeBanner = Yii::$app->session->get('realtime_banner');
        
        if ($realtimeBanner && $realtimeBanner['expires'] > time()) {
            $configuration->enabled = true;
            $configuration->content = $realtimeBanner['content'];
            $configuration->style = $realtimeBanner['style'];
            $configuration->closeButton = false;
            
            // Auto-remove after display
            Yii::$app->session->remove('realtime_banner');
            
            return true;
        }
        
        return false;
    }
}
```

### 4. Contextual Help System

```php
class ContextualBannerService
{
    private static $helpTriggers = [
        '/space/create' => [
            'content' => '💡 <strong>Tip:</strong> Creating a space? Start with a clear purpose and invite relevant members.',
            'style' => 'info'
        ],
        '/user/account/edit' => [
            'content' => '🔐 <strong>Security Tip:</strong> Enable two-factor authentication for enhanced security.',
            'style' => 'info'
        ],
        '/admin/module' => [
            'content' => '⚡ <strong>Pro Tip:</strong> Check module compatibility before updating HumHub.',
            'style' => 'warning'
        ]
    ];
    
    public static function getContextualHelp($route)
    {
        // Direct match
        if (isset(self::$helpTriggers[$route])) {
            return self::$helpTriggers[$route];
        }
        
        // Pattern matching
        foreach (self::$helpTriggers as $pattern => $help) {
            if (fnmatch($pattern, $route)) {
                return $help;
            }
        }
        
        return null;
    }
    
    public static function onBannerConfiguration($event)
    {
        $configuration = $event->result;
        $currentRoute = '/' . Yii::$app->controller->getRoute();
        
        if ($help = self::getContextualHelp($currentRoute)) {
            // Check if user hasn't dismissed this tip
            $dismissedTips = Yii::$app->session->get('dismissed_tips', []);
            if (!in_array($currentRoute, $dismissedTips)) {
                $configuration->enabled = true;
                $configuration->content = $help['content'];
                $configuration->style = $help['style'];
                $configuration->closeButton = true;
            }
        }
    }
}
```

## Testing & Debugging

### Unit Testing Banner Configuration

```php
// tests/unit/BannerConfigurationTest.php
namespace tests\unit;

use humhub\modules\banner\models\Configuration;
use humhub\modules\banner\Module;

class BannerConfigurationTest extends \Codeception\Test\Unit
{
    public function testConfigurationValidation()
    {
        $config = new Configuration();
        
        // Test invalid style
        $config->style = 'invalid';
        $this->assertFalse($config->validate(['style']));
        
        // Test valid styles
        foreach (['info', 'success', 'warning', 'danger'] as $style) {
            $config->style = $style;
            $this->assertTrue($config->validate(['style']));
        }
    }
    
    public function testEventModification()
    {
        $module = Yii::$app->getModule('banner');
        
        // Register test event
        Event::on(
            Module::class,
            Module::EVENT_AFTER_GET_CONFIGURATION,
            function($event) {
                $event->result->content = 'Modified by event';
            }
        );
        
        $config = $module->getConfiguration();
        $this->assertEquals('Modified by event', $config->content);
    }
}
```

### Functional Testing

```php
// tests/functional/BannerDisplayTest.php
class BannerDisplayTest extends \Codeception\Test\Unit
{
    public function testBannerVisibility()
    {
        // Enable banner
        $module = Yii::$app->getModule('banner');
        $module->settings->set('enabled', true);
        $module->settings->set('content', 'Test banner');
        
        // Test as guest
        $I = $this->tester;
        $I->amOnPage('/');
        $I->see('Test banner', '#banner');
        
        // Test as user
        $I->amLoggedInAs(1);
        $I->amOnPage('/');
        $I->see('Test banner', '#banner');
    }
}
```

### Debug Logging

```php
// Add debug logging to your event handlers
public static function onBannerConfiguration($event)
{
    Yii::debug('Banner configuration event triggered', __METHOD__);
    
    try {
        $configuration = $event->result;
        
        // Your logic here
        
        Yii::debug([
            'enabled' => $configuration->enabled,
            'content' => substr($configuration->content, 0, 50) . '...',
            'style' => $configuration->style
        ], __METHOD__);
        
    } catch (\Exception $e) {
        Yii::error('Banner configuration failed: ' . $e->getMessage(), __METHOD__);
    }
}
```

### JavaScript Debugging

```javascript
// Add to your module's JS for debugging
humhub.module('mymodule.banner', function(module, require, $) {
    
    var debugMode = true;
    
    var init = function() {
        if (debugMode) {
            console.log('Banner module initialized');
            console.log('Banner element:', $('#banner'));
            console.log('Banner config:', {
                height: getComputedStyle(document.documentElement)
                    .getPropertyValue('--hh-banner-height'),
                content: $('#banner-content').text()
            });
        }
    };
    
    // Monitor banner changes
    var observer = new MutationObserver(function(mutations) {
        if (debugMode) {
            console.log('Banner DOM changed:', mutations);
        }
    });
    
    observer.observe(document.getElementById('banner'), {
        attributes: true,
        childList: true,
        subtree: true
    });
    
    module.export({
        init: init
    });
});
```

## Security Considerations

### 1. Content Sanitization

Always sanitize user-generated content:

```php
public static function onBannerConfiguration($event)
{
    $configuration = $event->result;
    
    // Get user-submitted content
    $userContent = UserSubmission::getLatest();
    
    // Sanitize HTML
    $purifier = new \HTMLPurifier([
        'HTML.Allowed' => 'p,br,strong,em,a[href]',
        'HTML.TargetBlank' => true,
        'URI.DisableExternalResources' => true
    ]);
    
    $configuration->content = $purifier->purify($userContent);
}
```

### 2. XSS Prevention

```php
// Safe content generation
public static function generateBannerContent($data)
{
    // Always encode user data
    return sprintf(
        '<strong>%s</strong>: %s <a href="%s">Read more</a>',
        Html::encode($data['title']),
        Html::encode($data['message']),
        Url::to(['/announcement/view', 'id' => $data['id']])
    );
}
```

### 3. Access Control

```php
public static function onBannerConfiguration($event)
{
    $configuration = $event->result;
    
    // Only show sensitive information to authorized users
    if (!Yii::$app->user->can('viewSensitiveAlerts')) {
        return;
    }
    
    $configuration->content = self::getSensitiveAlert();
}
```

### 4. Rate Limiting

Prevent banner spam:

```php
class BannerRateLimiter
{
    public static function canShow($bannerId, $userId)
    {
        $key = "banner_shown:{$bannerId}:{$userId}";
        $count = Yii::$app->cache->get($key) ?? 0;
        
        if ($count >= 3) { // Max 3 times per hour
            return false;
        }
        
        Yii::$app->cache->set($key, $count + 1, 3600);
        return true;
    }
}
```

## Performance Optimization

### 1. Caching Strategies

```php
public static function onBannerConfiguration($event)
{
    $configuration = $event->result;
    
    // Cache banner content
    $cacheKey = 'banner_content:' . Yii::$app->user->id;
    $content = Yii::$app->cache->get($cacheKey);
    
    if ($content === false) {
        // Generate expensive content
        $content = self::generateComplexBanner();
        Yii::$app->cache->set($cacheKey, $content, 300); // 5 minutes
    }
    
    $configuration->content = $content;
}
```

### 2. Lazy Loading

```php
// Load banner content via AJAX for heavy operations
public static function onBannerConfiguration($event)
{
    $configuration = $event->result;
    
    // Set placeholder
    $configuration->content = '<div id="banner-dynamic" data-url="/banner/load">Loading...</div>';
    
    // Add lazy loading script
    $configuration->content .= '<script>
        $(function() {
            $("#banner-dynamic").load($(this).data("url"));
        });
    </script>';
}
```

### 3. Database Query Optimization

```php
public static function getEfficisentBannerData()
{
    return Yii::$app->db->createCommand('
        SELECT b.*, COUNT(bi.id) as impression_count
        FROM banner b
        LEFT JOIN banner_impressions bi ON b.id = bi.banner_id
        WHERE b.active = 1 
        AND b.start_date <= NOW() 
        AND b.end_date >= NOW()
        GROUP BY b.id
        ORDER BY b.priority DESC
        LIMIT 1
    ')->queryOne();
}
```

## API Reference

### Module Class

```php
namespace humhub\modules\banner;

class Module extends \humhub\components\Module
{
    /**
     * Event triggered after configuration is loaded
     */
    const EVENT_AFTER_GET_CONFIGURATION = 'afterGetBannerConfiguration';
    
    /**
     * @var string Module icon
     */
    public $icon = 'exclamation-triangle';
    
    /**
     * Get module configuration
     * @return Configuration
     */
    public function getConfiguration(): Configuration;
    
    /**
     * Get config URL
     * @return string
     */
    public function getConfigUrl();
}
```

### Configuration Model

```php
namespace humhub\modules\banner\models;

class Configuration extends \yii\base\Model
{
    /**
     * @var bool Enable/disable banner
     */
    public bool $enabled = false;
    
    /**
     * @var string|null HTML content for logged-in users
     */
    public ?string $content = '';
    
    /**
     * @var string|null HTML content for guests
     */
    public ?string $contentGuests = '';
    
    /**
     * @var bool Show close button
     */
    public bool $closeButton = false;
    
    /**
     * @var string Banner style: info|success|warning|danger
     */
    public string $style = 'info';
    
    /**
     * @var string Banner type from BannerType constants
     */
    public string $bannerType = BannerType::MANUAL;
    
    /**
     * Load configuration from settings
     */
    public function loadBySettings(): void;
    
    /**
     * Save configuration to settings
     * @return bool
     */
    public function save(): bool;
}
```

### BannerType Constants

```php
namespace humhub\modules\banner\models;

class BannerType
{
    const MANUAL = 'manual';
    const WEATHER_ALERT = 'weather_alert';
    const SAFETY_NOTICE = 'safety_notice';
    const HARBOR_UPDATE = 'harbor_update';
    const EVENT_ANNOUNCEMENT = 'event_announcement';
    const MAINTENANCE_WINDOW = 'maintenance_window';
    
    /**
     * Get all available types
     * @return array
     */
    public static function getTypes(): array;
}
```

### JavaScript API

```javascript
humhub.module('banner', function(module, require, $) {
    /**
     * Initialize banner module
     * @param {boolean} isPjax - Whether called during PJAX
     */
    module.init = function(isPjax) {
        // Initialization logic
    };
    
    /**
     * Check if content needs scrolling
     */
    function checkScrolling() {
        // Scrolling detection
    }
});
```

### CSS Variables

```css
:root {
    --hh-banner-height: 40px;           /* Banner height */
    --hh-banner-font-color: var(--text-color-contrast); /* Text color */
    --hh-banner-bg-color: var(--info);  /* Background color */
}
```

## Best Practices Summary

1. **Always use events** - Don't modify module files directly
2. **Cache expensive operations** - Banner loads on every page
3. **Sanitize all content** - Prevent XSS attacks
4. **Test across themes** - Ensure compatibility
5. **Use appropriate styles** - Match urgency to visual style
6. **Implement graceful degradation** - Handle errors silently
7. **Consider mobile users** - Test responsive behavior
8. **Document your integrations** - Help future developers
9. **Monitor performance** - Banner shouldn't slow page loads
10. **Respect user preferences** - Allow dismissal when appropriate

## Conclusion

The HumHub Banner Module provides a robust foundation for site-wide notifications. By leveraging its event system and following the patterns in this guide, developers can create sophisticated notification systems that enhance user engagement while maintaining performance and security standards.

For additional support:
- GitHub Issues: https://github.com/cuzy-app/banner/issues
- HumHub Community: https://community.humhub.com
- Documentation Updates: Submit PRs to improve this guide