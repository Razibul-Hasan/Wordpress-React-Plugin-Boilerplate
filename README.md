# WordPress React Plugin Boilerplate

A production-ready **WordPress plugin boilerplate** with a **React admin dashboard**, a PSR-style autoloader, a REST API namespace, and a webpack + gulp build pipeline for JavaScript and SCSS.

Every class in this starter template is a working stub, not an empty shell. Install it, activate it, and you get a real admin dashboard with hash routing and a settings screen that reads and writes through the WordPress REST API — before you write a single line of your own code.

![WordPress](https://img.shields.io/badge/WordPress-6.0%2B-21759b)
![PHP](https://img.shields.io/badge/PHP-7.4%2B-777bb4)
![React](https://img.shields.io/badge/React-18.3-61dafb)
![webpack](https://img.shields.io/badge/webpack-5-8dd6f9)
![License](https://img.shields.io/badge/License-GPLv3-blue)

---

## Table of contents

- [Why use this boilerplate](#why-use-this-boilerplate)
- [Technology stack](#technology-stack)
- [Requirements](#requirements)
- [Quick start](#quick-start)
- [Renaming the boilerplate for your plugin](#renaming-the-boilerplate-for-your-plugin)
- [Folder structure](#folder-structure)
- [How the autoloader works](#how-the-autoloader-works)
- [Build commands](#build-commands)
- [Common tasks](#common-tasks)
  - [Add a dashboard screen](#add-a-dashboard-screen)
  - [Add a REST API route](#add-a-rest-api-route)
  - [Add a settings field](#add-a-settings-field)
  - [Add a JavaScript bundle](#add-a-javascript-bundle)
- [Data passed from PHP to JavaScript](#data-passed-from-php-to-javascript)
- [Hooks and filters](#hooks-and-filters)
- [Packaging a release](#packaging-a-release)
- [Internationalization](#internationalization)
- [FAQ](#faq)
- [Contributing](#contributing)
- [License](#license)

---

## Why use this boilerplate

Most WordPress plugin starter templates give you an empty folder structure and leave the wiring to you. This one is different in three ways:

- **Everything is already connected.** The React dashboard mounts, the REST routes respond, the settings save, and the admin notice dismisses over AJAX. You are editing working code, not filling in blanks.
- **One list to boot from.** Every class is instantiated in `Initialization::requires()`, so you can read the whole plugin's surface area in ten lines.
- **A modern frontend build without `@wordpress/scripts` lock-in.** Plain webpack 5 and gulp configs you can actually read and change, rather than an opaque wrapper.

It is aimed at developers building **custom WordPress plugins with a React admin interface** — settings panels, dashboards, page builders, WooCommerce extensions, or any plugin whose admin UI has outgrown PHP templates and jQuery.

## Technology stack

### Backend (PHP)

| Technology | Purpose |
| --- | --- |
| PHP 7.4+ | Minimum supported version |
| PSR-style autoloader | Maps the `WPB\` namespace onto `includes/`, no Composer required |
| WordPress REST API | Custom `wpb/v1` namespace with nonce-authenticated routes |
| WordPress Options API | Settings storage with merged defaults |
| Custom post type API | Example CPT registration |
| Activation / deactivation hooks | Seeds default options, flushes rewrite rules |
| admin-ajax | Dismissible admin notices |

### Frontend (JavaScript)

| Technology | Version | Purpose |
| --- | --- | --- |
| React | 18.3 | Admin dashboard UI, mounted with `createRoot` |
| React Context | — | `NavContext` for routing, `AppContext` for plugin state |
| webpack | 5 | Separate development and production configs |
| Babel | 7 | `@wordpress/babel-preset-default`, JSX transform |
| Fetch API | — | REST requests with the `X-WP-Nonce` header |

### Styling and tooling

| Technology | Purpose |
| --- | --- |
| SCSS (Dart Sass) | Source styles under `src/scss/` |
| gulp 4 | Compiles SCSS, autoprefixes, minifies, concatenates, zips releases |
| ESLint | `@wordpress/eslint-plugin` with the React rule set |
| Prettier | `wp-prettier`, the WordPress code style |
| Terser | Production minification that preserves i18n function names |
| WP-CLI | Generates the `.pot` translation template |

**Deliberately not included:** Composer, TypeScript, a testing framework, or a CI config. Add what your project needs rather than deleting what it doesn't.

## Requirements

- WordPress 6.0 or later
- PHP 7.4 or later
- Node.js 16 or later, with npm
- [WP-CLI](https://wp-cli.org/) on your `PATH` — only needed for `npm run make-pot` and `npm run package`

## Quick start

```bash
# 1. Clone into your WordPress plugins directory
cd wp-content/plugins
git clone https://github.com/<your-username>/Wordpress-React-Plugin-Boilerplate.git my-plugin
cd my-plugin

# 2. Install dependencies
npm install

# 3. Build the assets (required — the compiled bundles are gitignored)
npm run build

# 4. Activate the plugin in wp-admin, then open the new menu item
```

> **Important:** the compiled files (`assets/js/wpb.js`, `assets/js/frontend-script.js`, `assets/css/wpb-backend.css`, `assets/css/wpb-frontend.css`) are **not committed to git**. If you activate the plugin without running `npm run build` first, the dashboard will render an empty container.

For active development, run the watcher instead — it rebuilds JavaScript and SCSS on save:

```bash
npm run dev
```

## Renaming the boilerplate for your plugin

Do a whole-word find-and-replace across the whole project for these five identifiers, in this order:

| Find | Replace with | Example |
| --- | --- | --- |
| `wp-boilerplate` | Your plugin slug (folder, text domain, asset names) | `my-plugin` |
| `WPB_` | Your PHP constant prefix | `MYP_` |
| `WPB\` | Your PHP namespace root | `MYP\` |
| `wpb_` and `wpb-` | Function, option, hook, CSS and JS prefixes | `myp_` and `myp-` |
| `wp_boilerplate()` | The global helper function | `my_plugin()` |

Then finish up:

1. Rename `wp-boilerplate.php` to `<your-slug>.php`.
2. Update the plugin header — name, description, author, plugin URI — in that file.
3. Update `PLUGIN_SLUG` in `gulpfile.js`, and the `WPB_VER` regex there if you changed the constant prefix.
4. Update `name` and the `make-pot` script in `package.json`.
5. Rewrite `readme.txt` for WordPress.org and reset `changelog.txt`.
6. Run `npm run build` and confirm the dashboard still loads.

The autoloader derives file paths from the namespace, so renaming the namespace root is safe as long as your folder names stay lowercase.

## Folder structure

```
wp-boilerplate.php              Plugin header, constants, autoloader, bootstrap
includes/
  class-initialization.php      Boots every class, enqueues admin + frontend assets
  class-activation.php          Activation/deactivation hooks, default option values
  class-post-type.php           Example custom post type registration
  admin/
    class-options.php           Admin menu, capability check, React mount point
    class-notice.php            Dismissible admin notice wired to admin-ajax
  common/
    class-functions.php         Helpers exposed through wp_boilerplate()
    class-hooks.php             Filters and actions declared in one place
  restapi/
    class-request-api.php       Routes registered under the wpb/v1 namespace
  traits/
    trait-singleton.php         Shared instance helper
src/
  index.js                      React entry point, hash-based routing
  context/
    NavContext.js               Active screen, kept in sync with the URL hash
    AppContext.js               Plugin settings loaded once and shared
  components/
    Header.js                   Dashboard header and tab navigation
    Button.js                   Shared button with variants
  dashboard/
    overview/index.js           Example screen
    settings/index.js           Example screen with a full REST round trip
  utils/Utils.js                apiGet / apiPost helpers, class name helper
  frontend_script/
    index.js                    Frontend bundle entry point
    helper.js                   Frontend REST helper and localized data
  scss/
    backend/                    Dashboard styles, concatenated by gulp
    frontend.scss               Frontend styles
assets/
  css/wpb-admin.css             Hand-written CSS loaded on every admin screen
  js/wpb-admin.js               Hand-written JS for notice dismissal
languages/                      Translation files
```

## How the autoloader works

There is no Composer dependency. The autoloader in `wp-boilerplate.php` converts a namespace into a file path using the WordPress file naming convention: namespace segments become lowercase folders, underscores become dashes, class files get a `class-` prefix, and anything inside a `traits` folder gets a `trait-` prefix.

```
WPB\Includes\Admin\Options       →  includes/admin/class-options.php
WPB\Includes\Restapi\RequestApi  →  includes/restapi/class-request-api.php
WPB\Includes\Traits\Singleton    →  includes/traits/trait-singleton.php
```

To add a class: create the file at the matching path, then instantiate it in `Initialization::requires()`.

## Build commands

| Command | What it does |
| --- | --- |
| `npm run dev` | Watches JS and SCSS, rebuilding on save (webpack watch + gulp watch) |
| `npm start` | webpack watch only |
| `npm run build` | Compiles SCSS and production JavaScript bundles |
| `npm run lint` | Runs ESLint over `src/` |
| `npm run make-pot` | Generates `languages/wp-boilerplate.pot` (requires WP-CLI) |
| `npm run package` | Builds, generates the `.pot`, and zips a release into `build/` |

## Common tasks

### Add a dashboard screen

1. Create `src/dashboard/your-screen/index.js` exporting a React component.
2. Register it in `routeConfig` in `src/index.js`.
3. Add it to `TABS` in `src/components/Header.js` so it appears in the navigation.

The screen is then reachable at `admin.php?page=wpb-dashboard#your-screen`.

### Add a REST API route

Add an entry to the `$routes` array in `RequestApi::register_route()`:

```php
array(
    'endpoint'            => 'your-endpoint',
    'methods'             => 'POST',
    'callback'            => array( $this, 'your_callback' ),
    'permission_callback' => array( $this, 'get_endpoint_permissions' ),
),
```

Call it from React with the helpers in `src/utils/Utils.js`, which attach the `X-WP-Nonce` header automatically:

```js
import { apiGet, apiPost } from '../utils/Utils';

const result = await apiPost( 'your-endpoint', { key: 'value' } );
```

> **Security note:** `get_endpoint_permissions()` checks `current_user_can()`. Only replace it with `__return_true` on routes that are genuinely public, and validate the input yourself when you do.

### Add a settings field

Add the key and its default to `Activation::default_settings()`. The REST layer and the settings screen pick it up with no further changes, because both read the merged defaults through `wp_boilerplate()->get_settings()`.

### Add a JavaScript bundle

Add an `entry` key to **both** `webpack.config.js` and `webpack.production.config.js`, then enqueue the output file in `Initialization`.

## Data passed from PHP to JavaScript

Three objects are localized onto `window`:

| Object | Available on | Contains |
| --- | --- | --- |
| `wpbBackendData` | Plugin dashboard screen | `url`, `ajax`, `rest`, `restNonce`, `nonce`, `version`, `userInfo` |
| `wpbFrontendData` | Site frontend | `ajax`, `rest`, `nonce` |
| `wpbAdmin` | Every admin screen | `nonce` |

Import `backendData` from `src/utils/Utils.js` or `frontendData` from `src/frontend_script/helper.js` rather than reading `window` directly.

## Hooks and filters

The boilerplate ships with these extension points:

| Hook | Type | Purpose |
| --- | --- | --- |
| `wpb_menu_capability` | filter | Change the capability required to see the plugin menu |
| `wpb_allowed_html` | filter | Extend the tags allowed when rendering stored markup |
| `wpb_daily_event` | action | Cron callback, ready to schedule with `wp_schedule_event()` |

Options written to the database: `wpb_settings`, `wpb_installed_version`, `wpb_dismissed_notices`.

## Packaging a release

```bash
npm run package
```

This compiles the assets, generates the translation template, and writes a versioned zip to `build/`. The version number is read from the `WPB_VER` constant in the main plugin file, so bump it there before packaging. Source files, `node_modules`, and tooling configs are excluded from the zip.

## Internationalization

Every user-facing string uses the `wp-boilerplate` text domain and is wrapped in `esc_html__()` or a matching escaping function. Translations load from `languages/` on `init`. React strings are registered with `wp_set_script_translations()`, and Terser is configured to preserve the `__`, `_n`, `_nx`, and `_x` function names during minification.

Regenerate the template after adding strings:

```bash
npm run make-pot
```

## FAQ

**Does this boilerplate require Composer?**
No. The autoloader is built into the main plugin file and maps the namespace onto folders directly. Add Composer if your plugin needs third-party PHP packages.

**Why is the admin dashboard blank after activating?**
The compiled bundles are gitignored. Run `npm run build` and reload the page.

**Can I use this for a WooCommerce extension?**
Yes. Nothing here depends on WooCommerce, so add your own dependency check in `Initialization` and build on top.

**Does it work with the WordPress block editor?**
Not out of the box. This boilerplate targets a standalone React admin dashboard, not Gutenberg blocks. You can add `@wordpress/blocks` and a block entry point to the webpack config if you need both.

**Why plain webpack instead of `@wordpress/scripts`?**
So the build is readable and editable. `@wordpress/scripts` is excellent for blocks, but it hides its configuration, which gets in the way when a plugin needs multiple bundles with different targets.

**Is React bundled or does it use the copy WordPress ships?**
It is bundled, via the explicit `react` and `react-dom` dependencies. To use WordPress's copy instead, add webpack `externals` mapping `react` to `window.React` and add `react` to the enqueue dependency array.

**How do I remove the example custom post type?**
Delete `includes/class-post-type.php`, remove `new PostType();` from `Initialization::requires()`, and remove the `PostType::register()` call in `Activation::activate()`.

## Contributing

Issues and pull requests are welcome. Please run `npm run lint` before opening a PR, and keep PHP changes compatible with PHP 7.4.

## License

GPLv3, matching WordPress itself. See [gnu.org/licenses/gpl-3.0.html](http://www.gnu.org/licenses/gpl-3.0.html).
