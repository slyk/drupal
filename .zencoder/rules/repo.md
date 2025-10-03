# Repository Overview

## Project Summary
- **Platform**: Drupal 7-based site running under `/var/www/petr.tps.my`.
- **Custom Modules**: Located mainly in `sites/all/modules`, notably the `TooPro` bundle with domain-specific functionality (e.g., `tps_retail`).
- **Core Modules**: Standard Drupal core modules reside under `/var/www/petr.tps.my/modules`.

## Key Paths
1. **Custom Modules**
   - `sites/all/modules/TooPro/`: Primary custom feature set.
   - `sites/all/modules/services/`: Service endpoints; often paired with custom modules.
2. **Theme Assets**
   - `sites/all/themes/`: Custom and contributed theme overrides.
3. **Libraries**
   - `sites/all/libraries/`: Third-party PHP libraries (e.g., Zend, prro).

## Common Tasks
1. **Drupal Hook Implementations**
   - Custom logic often extends Drupal via hooks in `*.module` files.
2. **Service Endpoints**
   - `services` module exposes resources declared via `hook_services_resources()` in custom modules.
3. **Form Alterations & Builders**
   - Forms defined in `*.module` or included files; validated and submitted through Drupal's Form API.

## Development Notes
- **Bootstrap**: Drupal bootstrap handled via `index.php`; avoid modifying core unless necessary.
- **Configuration**: Admin interfaces accessible via `/admin/*` paths; verify permissions before exposing new features.
- **Caching & Registry**: Clear caches (`drush cc all` or admin UI) after altering module code or services definitions.

## Testing & Deployment
- **Automated Tests**: Drupal SimpleTest available but may not be configured; check `profiles` for test suites.
- **Database Updates**: Use `update.php` for schema changes.
- **Version Control**: Ensure changes align with repository standards; avoid committing generated files.

## Additional Tips
- **Coding Standards**: Follow Drupal 7 coding standards (spacing, comment style, function naming `module_name_action`).
- **Internationalization**: Many UI strings appear localized; wrap new strings in `t()` for translation support.
- **Security**: Sanitize user input and respect Drupal permission checks when exposing new functionality.