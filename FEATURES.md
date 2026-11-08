# Zola feature map

Zola is built as a reusable Elementor theme center. It does not ship a fixed business homepage; pages and site templates are edited in Elementor, while standard posts remain WordPress posts. The feature groups follow the general multipurpose-theme model described on [Astra's feature page](https://wpastra.com/features/).

| Feature group | Included in Zola | Optional integration |
| --- | --- | --- |
| Customizer | Separate Header, Style, Blog, Layout, Footer, Pages, Portfolio and Share sections; WordPress keeps its Menus, Widgets, Homepage Settings and Additional CSS sections | Elementor Site Settings handles Elementor global styles |
| Elementor workflow | Dashboard shortcut creates a draft Home page and opens Elementor; full-width and canvas templates; Zola Navigation Menu widget can display WordPress menus in Elementor designs | Elementor plugin is required for Elementor page editing and footer templates |
| Site Builder | Zola Site Builder assigns one global Elementor Footer template; Header Builder opens from the Zola menu and Customizer | Elementor Theme Builder remains available for Footer, Single and Archive locations |
| Header/Footer | Header Builder manages one site-wide responsive header with separate desktop and mobile layouts; Elementor Saved Templates can replace the theme footer | Elementor Theme Builder adds footer display conditions when available |
| Style and layout | Accent/surface/text colors, font family, button radius, site width and reading width | Elementor Site Settings owns Elementor global colors, fonts and spacing |
| Hero | Elementor Hero widget with editable image, copy, alignment, height, typography, colors, overlay and optional links; image priority can be set per widget | `zola_hero_image` filter and `zola_hero_media` action support slide/media plugins |
| Pages | Control default page title block and eyebrow on standard WordPress pages; Elementor pages manage their own titles | Build page content and layout with Elementor |
| Blog | Grid/List/Cover cards, 2–4 columns, date/category/excerpt controls, optional widget sidebar, pagination and related posts | AJAX filtering and complex query loops use Elementor add-ons |
| Portfolio | Archive display controls for plugin-registered `portfolio` and `project` post types; project data remains plugin-owned | Install a portfolio plugin that registers one of these post types |
| Share | Enable/disable post sharing and choose Facebook, X, LinkedIn and Copy Link | Social sharing plugins can provide additional networks |
| Layouts | Full Width, Canvas, standard page, blog/archive, search, single post and 404 templates | Custom page sections are built with Elementor widgets/containers |
| WooCommerce | Product grid styling, 2–4 columns, products per page, zoom/lightbox/slider gallery support | Quick View, swatches, wishlist, side cart, filters and checkout builder use WooCommerce extensions |
| Motion | Scroll reveal, subtle transitions and mobile menu; respects Reduce Motion | Widget-specific motion is configured in Elementor |
| Speed | Small theme script, responsive WordPress images, configurable eager loading for the lead Hero image, no external font/CDN dependency | LiteSpeed Cache handles page cache and optional CSS/JS/lazy-load optimization |
| SEO | WordPress title support and semantic landmarks; avoids adding duplicate SEO meta/schema | Yoast, Rank Math or AIOSEO for meta, canonical, structured data and sitemap |
| Extensibility | Header/Footer hooks, Hero hooks, child-theme support, WooCommerce and RTL stylesheet | Add-on plugins or child themes provide project-specific features |

Zola remains a classic PHP theme because its required page builder is Elementor. It does not use Full Site Editing templates or block-theme patterns.
