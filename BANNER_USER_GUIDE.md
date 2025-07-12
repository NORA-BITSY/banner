# HumHub Banner Module - User Guide

## Table of Contents
1. [Overview](#overview)
2. [Installation](#installation)
3. [Configuration](#configuration)
4. [Banner Types](#banner-types)
5. [Use Case Examples](#use-case-examples)
6. [Styling and Customization](#styling-and-customization)
7. [Developer Integration](#developer-integration)
8. [Best Practices](#best-practices)
9. [Troubleshooting](#troubleshooting)

## Overview

The HumHub Banner Module allows administrators to display customizable notification banners at the top of their HumHub platform. These banners can communicate important information to users, such as maintenance windows, safety notices, events, or any custom message.

### Key Features
- **Multiple Banner Types**: Pre-configured templates for common use cases
- **HTML Support**: Full HTML customization for rich content
- **User Targeting**: Different messages for logged-in users and guests
- **Dismissible Banners**: Optional close button for users
- **Style Options**: Four visual styles (info, success, warning, danger)
- **Responsive Design**: Automatic text scrolling for long messages
- **Theme Compatibility**: Works with HumHub, Enterprise, and Clean themes
- **Developer-Friendly**: Event system for programmatic control

## Installation

1. Download the module from the HumHub Marketplace or clone from GitHub
2. Place the module in your `protected/modules/banner` directory
3. Enable the module in Administration → Modules
4. Navigate to Administration → Banner to configure

## Configuration

Access the banner configuration at: **Administration → Banner**

### Basic Settings

#### Enable/Disable
- **Enabled**: Toggle the banner on/off without losing your configuration

#### Close Button
- **Close Button**: Allow users to dismiss the banner (session-based)

#### Style Options
- **Info** (Blue): General announcements, updates
- **Success** (Green): Positive messages, achievements
- **Warning** (Yellow): Important notices requiring attention
- **Danger** (Red): Critical alerts, emergencies

#### Banner Type
- **Manual**: Custom HTML content
- **Weather Alert**: Pre-formatted weather warnings
- **Safety Notice**: Safety-related announcements
- **Harbor Update**: Harbor/maritime updates
- **Event Announcement**: Event notifications
- **Maintenance Window**: System maintenance notices

### Content Fields

#### Banner Content for Logged-in Users
HTML content displayed to authenticated users. Supports:
- Text formatting (bold, italic, links)
- Images and icons
- Custom CSS classes
- JavaScript (use with caution)

#### Banner Content for Visitors/Guests
Separate content for non-authenticated users. Useful for:
- Login reminders
- Public announcements
- Registration promotions

## Banner Types

### 1. Manual Banner
Full control over content. Perfect for:
- Custom announcements
- Complex layouts
- Interactive elements

### 2. Weather Alert
Pre-formatted template for weather warnings:
```html
<div class="weather-alert">
    <strong>Weather Alert:</strong> High winds and heavy rain expected.
</div>
```

### 3. Safety Notice
Structured format for safety announcements:
```html
<div class="safety-notice">
    <strong>Safety Notice:</strong> Fire drill scheduled for 2 PM today.
</div>
```

### 4. Harbor Update
Maritime/harbor-specific notifications:
```html
<div class="harbor-update">
    <strong>Harbor Update:</strong> Dock 3 closed for maintenance.
</div>
```

### 5. Event Announcement
Event promotion template:
```html
<div class="event-announcement">
    <strong>Event:</strong> Company picnic this Friday at 3 PM.
</div>
```

### 6. Maintenance Window
System maintenance notifications:
```html
<div class="maintenance-window">
    <strong>Maintenance:</strong> System updates tonight 10 PM - 2 AM.
</div>
```

## Use Case Examples

### Example 1: Emergency Notification
**Scenario**: Fire alarm testing
```html
<strong>⚠️ ATTENTION:</strong> Fire alarm testing today from 2-3 PM. 
Please remain calm if alarms sound. 
<a href="/safety-procedures" class="btn btn-outlined">View Safety Procedures</a>
```
**Settings**: 
- Style: Danger
- Close Button: Disabled
- Type: Manual

### Example 2: New Feature Announcement
**Scenario**: Launching new collaboration tools
```html
<strong>🎉 New Feature!</strong> Check out our new project collaboration tools. 
<a href="/help/new-features" style="color: inherit; text-decoration: underline;">Learn more →</a>
```
**Settings**: 
- Style: Success
- Close Button: Enabled
- Type: Manual

### Example 3: Maintenance Window
**Scenario**: Scheduled system maintenance
```html
<strong>🔧 Scheduled Maintenance:</strong> Our platform will be unavailable on 
Saturday, March 25th from 10 PM to 2 AM EST for system upgrades.
```
**Settings**: 
- Style: Warning
- Close Button: Enabled
- Type: Maintenance Window

### Example 4: Welcome Message for Guests
**Guest Content**:
```html
Welcome to our community! 
<a href="/user/auth/login" class="btn btn-outlined">Login</a> or 
<a href="/user/registration" class="btn btn-outlined">Sign Up</a> 
to access all features.
```
**Settings**: 
- Style: Info
- Close Button: Enabled
- Type: Manual

### Example 5: Event with Countdown
**Scenario**: Company event with dynamic countdown
```html
<strong>🎊 Annual Conference:</strong> 
<span id="countdown"></span> days remaining! 
<a href="/events/conference2024" class="btn btn-outlined">Register Now</a>
<script>
var eventDate = new Date('2024-06-15');
var now = new Date();
var days = Math.floor((eventDate - now) / (1000 * 60 * 60 * 24));
document.getElementById('countdown').textContent = days;
</script>
```
**Settings**: 
- Style: Info
- Close Button: Enabled
- Type: Manual

### Example 6: Multi-language Support
```html
<span class="lang-en">Important: System update tonight</span>
<span class="lang-es" style="display:none;">Importante: Actualización del sistema esta noche</span>
<span class="lang-fr" style="display:none;">Important: Mise à jour du système ce soir</span>
```

### Example 7: COVID-19 Health Notice
```html
<strong>🏥 Health & Safety:</strong> 
Masks required in all common areas. 
<a href="/health-guidelines">View current guidelines</a> | 
<a href="/vaccination-status">Update vaccination status</a>
```
**Settings**: 
- Style: Warning
- Close Button: Disabled
- Type: Safety Notice

## Styling and Customization

### CSS Variables
Customize the banner appearance using CSS variables:

```css
<style>
:root {
    --hh-banner-height: 60px; /* Default: 40px */
    --hh-banner-font-color: #ffffff; /* Default: contrast color */
    --hh-banner-bg-color: #ff6b6b; /* Default: based on style */
}
</style>
```

### Custom Styling Examples

#### Gradient Background
```html
<style>
#banner {
    background: linear-gradient(45deg, #667eea 0%, #764ba2 100%) !important;
}
</style>
<strong>Special Announcement:</strong> Join us for the annual gala!
```

#### Animated Banner
```html
<style>
@keyframes pulse {
    0% { opacity: 1; }
    50% { opacity: 0.7; }
    100% { opacity: 1; }
}
#banner-content strong {
    animation: pulse 2s infinite;
}
</style>
<strong>⚡ LIVE:</strong> Company town hall meeting in progress
```

#### Icon Integration
```html
<i class="fa fa-bell" style="margin-right: 10px;"></i>
<strong>Reminder:</strong> Submit your timesheets by Friday 5 PM
```

### Responsive Behavior
- **Desktop**: Full banner width with centered content
- **Mobile**: Reduced font size (14px) for better readability
- **Long Content**: Automatic horizontal scrolling animation
- **Hover**: Pauses scrolling animation on mouse hover

## Developer Integration

### Event System
Other modules can programmatically control the banner:

```php
// In your module's Events.php
use humhub\modules\banner\Module;
use yii\base\Event;

Event::on(
    Module::class,
    Module::EVENT_AFTER_GET_CONFIGURATION,
    function($event) {
        $configuration = $event->result;
        
        // Check custom conditions
        if ($this->isEmergency()) {
            $configuration->enabled = true;
            $configuration->style = 'danger';
            $configuration->content = 'Emergency: ' . $this->getEmergencyMessage();
            $configuration->closeButton = false;
        }
    }
);
```

### Dynamic Content Examples

#### User-specific Messages
```php
$userName = Yii::$app->user->identity->displayName;
$configuration->content = "Welcome back, {$userName}! You have 3 new messages.";
```

#### Time-based Banners
```php
$hour = date('H');
if ($hour >= 22 || $hour <= 6) {
    $configuration->content = "🌙 Night mode active - Support available 9 AM";
}
```

## Best Practices

### Content Guidelines
1. **Keep it Concise**: Banner space is limited
2. **Clear Call-to-Action**: Use buttons for important actions
3. **Appropriate Styling**: Match style to message urgency
4. **Test Responsiveness**: Check on mobile devices
5. **Avoid Clutter**: One main message per banner

### Performance Tips
1. **Minimize JavaScript**: Use sparingly for dynamic content
2. **Optimize Images**: Use small, compressed images
3. **Cache Static Content**: Leverage browser caching
4. **Limit External Resources**: Avoid loading external scripts

### Accessibility
1. **Color Contrast**: Ensure text is readable
2. **Link Descriptions**: Use descriptive link text
3. **Keyboard Navigation**: Test with keyboard only
4. **Screen Readers**: Include appropriate ARIA labels

### Security Considerations
1. **Sanitize Input**: Be careful with user-generated content
2. **XSS Prevention**: Validate all HTML input
3. **Script Restrictions**: Limit JavaScript usage
4. **Content Review**: Regularly audit banner content

## Troubleshooting

### Banner Not Showing
1. Check if module is enabled
2. Verify banner is enabled in configuration
3. Ensure content is not empty
4. Check browser console for JavaScript errors
5. Verify user permissions

### Styling Issues
1. Clear browser cache
2. Check for CSS conflicts
3. Verify theme compatibility
4. Test in different browsers

### Close Button Not Working
1. Ensure JavaScript is enabled
2. Check for JavaScript errors
3. Verify close button is enabled in config
4. Clear session/cookies

### Content Overflow
- Long content automatically scrolls
- Reduce content length if needed
- Use line breaks for better formatting
- Consider mobile viewport

### Theme Conflicts
The module supports:
- HumHub default theme
- Enterprise theme
- Clean theme

For custom themes, you may need to adjust CSS variables.

## Advanced Examples

### Dynamic Weather Integration
```html
<div id="weather-banner">
    <strong>Today's Weather:</strong> 
    <span id="weather-info">Loading...</span>
</div>
<script>
// Fetch weather from your API
fetch('/api/weather')
    .then(r => r.json())
    .then(data => {
        document.getElementById('weather-info').textContent = 
            `${data.temp}°C, ${data.condition}`;
    });
</script>
```

### Rotating Announcements
```html
<div id="rotating-message"></div>
<script>
var messages = [
    "Tip 1: Use @mentions to notify colleagues",
    "Tip 2: Star important spaces for quick access",
    "Tip 3: Set up email notifications in your profile"
];
var index = 0;
function rotateMessage() {
    document.getElementById('rotating-message').innerHTML = 
        '<strong>💡 Did you know?</strong> ' + messages[index];
    index = (index + 1) % messages.length;
}
rotateMessage();
setInterval(rotateMessage, 5000);
</script>
```

### Progress Bar Example
```html
<style>
.progress-bar {
    width: 200px;
    height: 10px;
    background: rgba(255,255,255,0.3);
    border-radius: 5px;
    display: inline-block;
    margin: 0 10px;
}
.progress-fill {
    height: 100%;
    background: white;
    border-radius: 5px;
    width: 75%;
}
</style>
<strong>Fundraising Goal:</strong> 
$7,500 / $10,000
<div class="progress-bar">
    <div class="progress-fill"></div>
</div>
<a href="/donate" class="btn btn-outlined">Contribute</a>
```

## Conclusion

The HumHub Banner Module is a powerful tool for platform-wide communications. Whether you need to announce maintenance, celebrate achievements, or alert users to important information, this module provides the flexibility and features to effectively reach your audience.

For support, feature requests, or bug reports, please visit:
- GitHub: https://github.com/cuzy-app/banner
- HumHub Marketplace: [Banner Module Page]

Remember to test your banners thoroughly and consider your users' experience when implementing platform-wide notifications.