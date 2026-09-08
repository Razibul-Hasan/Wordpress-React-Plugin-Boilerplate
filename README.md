# WP Boilerplate

A starter skeleton for a WordPress plugin: PSR-style autoloader, admin menu with
a React dashboard, REST API, and a webpack + gulp build pipeline. Nothing in it
does real work — every class is a working stub you replace.

## Starting a new plugin

Copy this folder into `wp-content/plugins/` under the new plugin's slug, then
find-and-replace the five identifiers below. Do them in this order; each one is
a whole-word replace across every file.

| Find | Replace with | Example |
| --- | --- | --- |
| `wp-boilerplate` | plugin slug (folder, text domain, asset names) | `my-plugin` |
| `WPB_` | constant prefix | `MYP_` |
| `WPB\` | PHP namespace root | `MYP\` |
| `wpb_` / `wpb-` | function, option, hook, CSS and JS prefixes | `myp_` / `myp-` |
| `wp_boilerplate()` | global helper function | `my_plugin()` |

Then:

1. Rename `wp-boilerplate.php` to `<slug>.php`.
2. Update the plugin header (name, description, author, URI) in that file.
3. Update `PLUGIN_SLUG` in `gulpfile.js` and the `WPB_VER` regex if the constant
   prefix changed.
4. Update `name` and `make-pot` in `package.json`.
5. Rewrite `readme.txt` and reset `changelog.txt`.

The autoloader derives file paths from the namespace, so renaming the namespace
root is safe as long as folder names stay lowercase.

## Layout

```
wp-boilerplate.php          Plugin header, constants, autoloader, boot
includes/
  class-initialization.php  Boots every class, enqueues assets
  class-activation.php      Activation/deactivation hooks, default options
  class-post-type.php       Example custom post type
  admin/class-options.php   Admin menu + React mount point
  admin/class-notice.php    Dismissible notice, wired to admin-ajax
  common/class-functions.php  Helpers behind wp_boilerplate()
  common/class-hooks.php    Filters and actions in one place
  restapi/class-request-api.php  Routes under wpb/v1
  traits/trait-singleton.php     Demonstrates the trait- naming rule
src/
  index.js                  React entry, hash routing
  context/                  Nav and app state providers
  components/               Shared UI
  dashboard/                One folder per screen
  frontend_script/          Frontend bundle entry
  scss/backend/             Dashboard styles (concatenated by gulp)
  scss/frontend.scss        Frontend styles
assets/                     Build output plus hand-written admin CSS/JS
```

### How the autoloader maps names

Namespace segments become lowercase folders; underscores become dashes. Class
files get a `class-` prefix, anything under `traits/` gets `trait-`.

```
WPB\Includes\Admin\Options      -> includes/admin/class-options.php
WPB\Includes\Restapi\RequestApi -> includes/restapi/class-request-api.php
WPB\Includes\Traits\Singleton   -> includes/traits/trait-singleton.php
```

Add a class by creating the file in the matching path and instantiating it in
`Initialization::requires()`.

## Development

```bash
npm install
npm run dev      # webpack --watch + gulp watch
npm run build    # compile SCSS and production JS bundles
npm run lint     # eslint over src/
npm run package  # build, generate .pot, zip into ./build
```

`npm run make-pot` and `npm run package` need WP-CLI on your PATH. The compiled
bundles in `assets/js/wpb.js`, `assets/js/frontend-script.js`,
`assets/css/wpb-backend.css` and `assets/css/wpb-frontend.css` are gitignored —
run a build before testing in the browser or the dashboard will not load.

## Adding things

- **A dashboard screen** — add a folder under `src/dashboard/`, then register it
  in `routeConfig` in `src/index.js` and in `TABS` in `src/components/Header.js`.
- **A REST route** — add an entry to the `$routes` array in `RequestApi::register_route()`.
- **A JS bundle** — add an `entry` key in both webpack configs and enqueue it in
  `Initialization`.
- **A settings field** — extend `Activation::default_settings()`; the REST layer
  and the settings screen pick it up without further changes.
