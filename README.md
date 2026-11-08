# Zola

ธีม WordPress แบบ **Elementor-only สำหรับออกแบบหน้าและส่วนประกอบเว็บไซต์** ใช้เริ่มเว็บได้หลายประเภท พร้อมระบบ Header/Footer, design settings, layout templates และจุดเชื่อมต่อ เนื้อหาเพจและ site templates สร้างและแก้ใน Elementor ไม่มีหน้า agency ตัวอย่างที่เขียนตายตัวใน PHP; โพสต์บล็อกยังใช้ระบบโพสต์ของ WordPress

## เวอร์ชันและข้อกำหนด

- Theme version: **1.0.0** (ระหว่างพัฒนา)
- WordPress: **6.8 ขึ้นไป**
- PHP: **8.4 ขึ้นไป**; รองรับ PHP 8.5 ตามสายการทำงานปัจจุบันของ WordPress
- Elementor ใช้สำหรับแก้หน้าและสร้าง Saved Template สำหรับ Header/Footer; เมื่อปิดปลั๊กอิน ธีมยังแสดง Header/Footer พื้นฐานได้

โค้ดใช้ type declarations ของ PHP 8, ตรวจชนิดข้อมูลก่อนนำไปสร้าง CSS/HTML, sanitize ค่าตอนรับเข้า และ escape ตอนแสดงผล ส่วนคำสั่งบันทึกใน Dashboard ตรวจ capability และ nonce ก่อนเปลี่ยนค่า การแสดง Elementor template ใช้เฉพาะ template ที่เผยแพร่แล้วและผ่านการตรวจประเภทโพสต์

ไฟล์ `assets/css/theme.css` เก็บเฉพาะโครงสร้างและรูปแบบพื้นฐานที่ใช้ร่วมกัน เช่น accessibility, fallback header/footer, Elementor Hero, blog, WooCommerce, responsive และ reduced motion; เมื่อเริ่มทำ demo ให้เพิ่ม CSS แยกตาม demo โดยไม่ย้ายสไตล์เฉพาะ demo เข้ามาปนในไฟล์นี้

## เริ่มต้นใช้งาน

1. ติดตั้งและเปิดใช้ Elementor
2. ไปที่ **Zola** ในเมนูผู้ดูแล แล้วกด **Create an Elementor page** ธีมจะสร้างหน้า Home แบบ draft และเปิดหน้าใน Elementor
3. สร้างหน้าเว็บด้วย Elementor; เพิ่ม widget **Hero** จากหมวด **Zola** เมื่อต้องการ Hero ที่ตั้งภาพ ข้อความ ปุ่ม และความสูงได้
4. เผยแพร่หน้า แล้วกำหนดเป็นหน้าแรกที่ **Settings → Reading → A static page**
5. ตั้งโลโก้และเมนูที่ **Appearance → Customize → Site Identity / Menus**

เมื่อ WordPress แสดงหน้ารวมโพสต์แทนหน้า static แถบผู้ดูแลจะแสดงลิงก์ **Set up Elementor homepage** ไปยังหน้า Zola เพื่อเริ่มตั้งค่า

## Header, Footer และ Site Builder

- จัดการ template ที่ใช้ทั่วเว็บไซต์ได้ที่ **Zola → Site Builder** เลือก Saved Template สำหรับ Header/Footer แล้วกดแก้ไขเพื่อเปิด Elementor
- ใช้ Elementor Theme Builder สำหรับ Header, Footer, Single และ Archive พร้อม display conditions (ใช้แพ็กเกจ Elementor ที่มี Theme Builder)
- หากใช้ Elementor Free ให้สร้าง Header/Footer เป็น Saved Template แล้วเลือกได้ที่ **Appearance → Customize → Header / Footer**
- ใน Header template ใช้วิดเจ็ต **Zola → Navigation Menu** เพื่อแสดงเมนู WordPress; สร้างรายการเมนูที่ **Appearance → Menus** แล้วเลือกเมนูในวิดเจ็ต
- ยังใช้ Header/Footer พื้นฐานของธีมได้ โดยแก้โลโก้ เมนู ปุ่ม CTA แถบประกาศ และตัวเลือก sticky/transparent ใน WordPress Customizer
- **Zola Full Width** เก็บ Header/Footer ของธีมไว้; **Zola Canvas** ซ่อนทั้งคู่

## Customizer sections

ที่ **Appearance → Customize** มีหมวด Header, Style, Blog, Layout, Footer, Pages, Portfolio และ Share ส่วน Menus, Widgets, Homepage Settings และ Additional CSS ใช้แผงมาตรฐานของ WordPress:

- **Header / Footer:** สร้าง layout ด้วย Elementor และมอบหมายเทมเพลตใน Site Builder; CTA/announcement/sticky settings, copyright, footer menu และ back-to-top ปรับได้จาก Customizer
- **Style / Layout:** สี ฟอนต์ มุมปุ่ม ความกว้างเว็บไซต์ และความกว้างบทความ
- **Blog:** Grid/List/Cover, 2–4 คอลัมน์, วัน/หมวดหมู่/excerpt, sidebar และจำนวนคำของ excerpt
- **Pages:** แสดง/ซ่อน title block และแก้ข้อความ label บนหน้า WordPress ปกติ; หน้า Elementor จัดการ title เอง
- **Portfolio:** รูปแบบ archive และคอลัมน์ สำหรับ post type `portfolio` หรือ `project` ที่ลงทะเบียนโดยปลั๊กอิน
- **Share:** เปิด/ปิด Facebook, X, LinkedIn และ Copy link บนบทความ
- **Widgets:** พื้นที่ Blog sidebar จะแสดงเมื่อมี widgets และเปิดตัวเลือก sidebar ในหมวด Blog

การตั้งค่าส่วนกลางของ Elementor เช่น global colors, typography และ spacing อยู่ใน Elementor Site Settings แยกต่างหาก

## จุดเชื่อมต่อ Hero / image slide

Widget Hero ตั้งภาพ ข้อความ ตำแหน่ง ความสูง สี/ตัวอักษร ความเข้ม overlay และลำดับโหลดภาพได้จาก Elementor; ปุ่มจะยังไม่แสดงจนกว่าจะใส่ label และ URL เพื่อไม่ให้มีลิงก์ตัวอย่างที่ใช้งานไม่ได้ วิดเจ็ตเรียก `zola_hero_image` filter เพื่อเลือกภาพ และ `zola_hero_media` action สำหรับแสดงเลเยอร์ image slide/video จากปลั๊กอินเสริม โดยส่งสถานะลำดับโหลดภาพเป็นอาร์กิวเมนต์ตัวที่สอง คลาส `.hero__plugin-layer` จะขยายเต็ม Hero และวางเหนือภาพสำรอง

```php
add_filter( 'zola_hero_image', function ( $image_url ) {
    return $image_url; // คืน URL ภาพที่ต้องการใช้
} );

add_action( 'zola_hero_media', function ( $fallback_image, $prioritize_image ) {
    echo '<div class="hero__plugin-layer">';
    // Render the slide plugin output here and honor the priority flag.
    echo '</div>';
}, 10, 2 );
```

มี action เพิ่มเติมรอบส่วน Header/Footer: `zola_before_header`, `zola_after_header`, `zola_before_footer` และ `zola_after_footer`

## Blog, WooCommerce, SEO และความเร็ว

- Blog: Grid, List หรือ Cover cards, ปรับจำนวนคอลัมน์, excerpt, metadata, sidebar, pagination, แชร์บทความ และ related posts
- Portfolio: รองรับ archive settings ของ portfolio plugins ที่ใช้ post type `portfolio` หรือ `project`; ตัว theme ไม่ลงทะเบียน CPT เพื่อให้ข้อมูลโครงการยังอยู่เมื่อเปลี่ยน theme
- WooCommerce: รองรับหน้าร้านและแกลเลอรีสินค้า พร้อมปรับคอลัมน์/จำนวนสินค้าต่อหน้า; Quick View, swatches, side cart และ product filters ใช้ extensions เพิ่มเติม
- SEO: ใช้ WordPress document title และ semantic landmarks; ให้ Yoast SEO, Rank Math หรือ AIOSEO จัดการ meta, canonical และ schema เพื่อไม่ให้ markup ซ้ำ
- LiteSpeed Cache: ธีมไม่เปิด page cache หรือ lazy-load ทับ รูป Hero ตั้ง `fetchpriority="high"`, `loading="eager"` และ `data-no-lazy="1"`; ตั้งค่า lazy-load ให้มีผู้จัดการเพียงระบบเดียว
- รองรับ RTL และ `prefers-reduced-motion`; ไม่มี JavaScript framework หรือเว็บฟอนต์ภายนอก

อ่านตารางความสามารถได้ที่ [FEATURES.md](FEATURES.md)

## โครงสร้างสำคัญ

- `inc/admin.php` — หน้า Zola dashboard และสร้างหน้า Home สำหรับ Elementor
- `inc/customizer.php` — Customizer sections สำหรับ Header, Style, Blog, Layout, Footer, Pages, Portfolio, Share และ WooCommerce
- `functions.php` — theme supports, Elementor integration และ Blog sidebar สำหรับ Widgets
- `inc/elementor/class-zola-hero-widget.php` — Hero widget สำหรับ Elementor
- `inc/elementor/class-zola-menu-widget.php` — Navigation Menu widget สำหรับ Header template ใน Elementor Free/Pro
- `inc/template-tags.php` — Elementor template locations และ helpers
- `assets/css/theme.css` — shared theme styles, responsive layouts, animation และ WooCommerce
- `assets/js/theme.js` — เมนูมือถือ, scroll reveal และ interaction ของธีม
