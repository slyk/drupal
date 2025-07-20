# Drupal 7 Codebase - AI Coding Agent Instructions

## Project Overview
This is a **Drupal 7.98** installation with custom TooPro Shop (TPS) modules. The codebase follows Drupal's hook-based architecture with a multisite configuration supporting the domain `petr.tps.my`.

## Architecture & Core Components

### Bootstrap Process
- Entry point: `index.php` → `includes/bootstrap.inc` → `drupal_bootstrap(DRUPAL_BOOTSTRAP_FULL)` → `menu_execute_active_handler()`
- Installation: `install.php` → `includes/install.core.inc` → `install_drupal()`
- Cron: `cron.php` with key-based security (`?cron_key=`)

### Directory Structure (Drupal Convention)
```
/includes/          # Core APIs (bootstrap.inc, common.inc, etc.)
/modules/           # Core modules (node, user, system, etc.)
/sites/all/         # Site-wide shared code
  /modules/         # Contributed & custom modules
  /themes/          # Contributed & custom themes  
  /libraries/       # External libraries (composer managed)
/sites/default/     # Default site configuration
/sites/petr.tps.my/ # Site-specific configuration
/themes/            # Core themes (bartik, seven, etc.)
```

### Custom Module Architecture - TooPro Shop (TPS)
Located in `sites/all/modules/TooPro/` with submodules:
- `tps_core/` - Base API, node types, roles (dependencies: node_reference, user_reference, field_group)
- `tps_amqp/` - Message queue integration
- `tps_directus/` - Directus CMS integration  
- `tps_shipping/` - Shipping fields and calculations (dimensions, weight)
- `tps_bonus/`, `tps_pay_method/`, `tps_retail/` - Business logic modules
- `tps_stats/`, `tps_sync/`, `tps_transaction/` - Data processing

## Development Patterns

### Module Structure (Follow Drupal Standards)
```php
modulename.info     # Module metadata (name, dependencies, version)
modulename.module   # Hook implementations
modulename.install  # Schema definitions, install/uninstall hooks
```

### Hook System Implementation
```php
// Menu hooks for routing
function tps_core_menu() {
  $items['admin/config/system/tps'] = array(
    'title' => 'TooPro Shop Config',
    'page callback' => 'drupal_get_form',
    'access arguments' => array('administer'),
  );
}

// Use proper Drupal constants
define('MSG_ERROR', 3); // Follow RFC3164 severity levels
```

### Database & Configuration
- Configuration: `sites/*/settings.php` with multisite support
- Database schema: Use `hook_schema()` in `.install` files
- Variables: Use `variable_get()` / `variable_set()` for configuration storage

### PHP Dependencies (Composer)
- Managed in `sites/all/libraries/composer.json`
- PSR-4 autoloading for TPS modules: `tps_amqp\`, `tps_directus\`, `tps_bonus\`
- External deps: Directus SDK, php-amqplib, phpseclib

## Critical Workflows

### Module Development
1. Create `.info` file with proper dependencies and core version
2. Implement hooks in `.module` file (menu, form, cron, etc.)
3. Add schema/install logic in `.install` file
4. Clear cache: Visit `/admin/modules` or use `drupal_flush_all_caches()`

### Security & Access
- Protected files via `.htaccess`: `.module`, `.inc`, `.info`, `.install` files blocked
- Permission system: Use `access arguments` in menu items
- Input sanitization: Use `check_plain()`, `filter_xss()` for user input

### Error Handling & Logging
```php
// Use Drupal's watchdog system
watchdog('module_name', 'Error message: @error', array('@error' => $error), WATCHDOG_ERROR);

// TPS custom message levels (RFC3164)
MSG_EMERGENCY (0) to MSG_DEBUG (7)
```

### Caching Strategy
- Use `CACHE_PERMANENT` / `CACHE_TEMPORARY` constants
- Clear specific caches: `cache_clear_all($cid, $table)`
- Clear all: `drupal_flush_all_caches()`

## Integration Points

### External Systems
- **AMQP**: Message queuing via `tps_amqp` module using php-amqplib
- **Directus**: Headless CMS integration via `tps_directus` module
- **File handling**: Private files in `sites/default/files-private/`
- **Adobe Flex**: special client software in tps_retail module written in ActionScript 3 (Adobe Flash) using additional Apache Flex SDK. So when we modify nodes fields, we need to update the Flex client code as well.
- **Adobe Flex**: special client software in tps_retail module written in ActionScript 3 (Adobe Flash) using additional Apache Flex SDK. So when we modify nodes fields, we need to update the Flex client code as well.

### Multisite Configuration
- Primary site: `sites/petr.tps.my/settings.php`
- Shared resources: `sites/all/` for modules/themes/libraries
- Domain-based routing via `sites/sites.php` (if present)

## Testing & Maintenance
- Core tests: `modules/simpletest/` 
- Custom tests: Follow `*.test` file naming in module directories
- Cron jobs: Access via `/cron.php?cron_key=[key]`
- Updates: Use `update.php` for database schema changes

## File Permissions
- Protect configuration: `sites/*/settings.php` should be read-only after setup
- Writable: `sites/*/files/` and `sites/*/files-private/` directories
- Logs: Site-specific logs in `sites/*/logs/`

## Legacy Considerations
- PHP 5.3.3+ requirement (defined in bootstrap.inc)
- No namespaces in core Drupal 7 code
- Function-based architecture (not OOP except for specific APIs)
- Custom classes in TPS modules: `TPSCore.class.php`, `TPSCoreConfig.class.php`
