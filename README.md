<h1 align="center"> Table Reservation - My Wordpress Plugin </h1> <br>
<p align="center">
  <a href="https://true-emotions.studio/">
    <img alt="Table Reservation" title="Table Reservation" src="https://true-emotions.studio/wp-content/uploads/2018/01/icon-256x256.jpg" width="250">
  </a>
</p>

<p align="center">
  Pick a place!
</p>

<p align="center">
  <a href="https://true-emotions.studio/">
    <img alt="True Emotions Studio" title="We are" src="https://true-emotions.studio/wp-content/uploads/2018/01/logo2.jpg" width="105">
  </a>
</p>

<!-- START doctoc generated TOC please keep comment here to allow auto update -->
<!-- DON'T EDIT THIS SECTION, INSTEAD RE-RUN doctoc TO UPDATE -->
## Table of Contents


- [Demo](https://true-emotions.studio/sample-page/)
- [Introduction](#introduction-wave)
- [Features](#features-eyeglasses)
- [Feedback](#feedback-raising_hand)
- [Build Process](#build-process-hammer)
- [FAQ](#faq)
- [License](#license-page_facing_up)

<!-- END doctoc generated TOC please keep comment here to allow auto update -->

## Introduction :wave:

This plugin allows users quickly book a table in your cafe or make a restaurant reservation. Also it can be used for coworking, internet cafe (cybercafe) and night club.

<p align="center">
  <img src = "https://true-emotions.studio/wp-content/uploads/2018/01/screenshot-1.gif" width=1000>
</p>

## Features :eyeglasses:

A few of the things you can do with Table Reservation:

* Rich schedule settings: *opening hours, week days, holidays, siesta...* 
* Custom email notifications for admin and guests
* Selectable tables for reservations *(one table can't be reserved by different people simultaneously, e.g. no collisions)
* Add and edit bookings from the admin panel
* Check correctness of user-input data *(date, party, time, name, email, phone, message)
* Mobile UX
* Create and manage many Venues simultaneously
* Custom setting for durations of each visit
* Customisable colors, date/time format, titles etc.
* WPBakery(Visual Composer) compatible

<p align="center">
  <img src = "https://true-emotions.studio/wp-content/uploads/2018/01/banner-1544x500.gif" width=1000>
</p>

## Feedback :raising_hand:

Feel free to [file an issue](https://github.com/vanadiuz/table-reservation/issues/new). Feature requests are always welcome.


## Build Process :hammer:

- Clone or download the repo
- Put it in wp-content/plugins/ (preferably on a locally deployed server with wordpress)
- Go to table-reservation/tremtr (here is front-end web app on Vue js)
- `npm install` to install dependencies 
- `npm run dev` to start develope app
- Activate plugin inside WordPress and [create a new table](https://data.true-emotions.studio/plugins/trem-table-reservation/table-reservation.mp4 "Video!")
- Now you can modify the Vue Js code and immediately watch how it will work in Wordpress

**Build Production Version of Plugin**
- `npm run build` to build front-end part of plugin
- Copy JS files from ./tremtr/dist/static/js to ./assets/js
- Open table-reservation.php in plagin's root and comment 
```php
  wp_register_script( 'tremtr-app', 'https://localhost:8080/app.js' , '', '', true );
```
- In the same place uncomment and rename next lines 
```php
            // wp_register_script('tremtr-manifest', TREMTR_PLUGIN_URL . '/assets/js/manifest.6a98d09897ee4023cfac.js', array(), '1.0.0', 'screen, all');
            // wp_register_script('tremtr-vendor', TREMTR_PLUGIN_URL . '/assets/js/vendor.27bdc60e48dd445d7732.js', array(), '1.0.0', 'screen, all');
            // wp_register_script('tremtr-app', TREMTR_PLUGIN_URL . '/assets/js/app.5ba64ddd460771627e94.js', array(), '1.0.0', 'screen, all');
```

## FAQ

**You will find "Cafe" and "Reservations" menu in your WordPress admin panel.**  
 1) Create scheme (Cafe -> Add new -> set featured image, select name of your cafe, draw tables markup)
 2) Add shortcode to page 
 3) Setup schedule (Cafe -> Settings)	
 [Slightly outdated video](https://data.true-emotions.studio/plugins/trem-table-reservation/table-reservation.mp4)
 
**Shortcode?**  \
 [table-reservation cafe_id="2722"]. 
 
 
**How to customize appearance of this plugin?**  
 1) There is possibility to change colors in Cafe-> Settings.
 2) Also you can override styles in assets->css->tremtr-client.css, but you can't change HTML markup, because it very hard.
 
**How to create beautiful email template? 🌻**  \
 [Here!](http://telegra.ph/How-create-beautiful-email-template-01-26 "And write code!")
 
 **Can I change the date/time format?**  \
 Yes, set the format for front-end the datepicker in Cafe > Settings. 
 
**How to translate the plugin?**  \
Everything in this plugin can be translated using the standard translation process and software like PoEdit. 
🔸 The language of the calendar is selected separately in the settings of the cafe (default English, uk = Ukrainian). 
🔸 Files with translation (for example "tremtr-ru_RU") must be placed in the …\wp-content\languages\plugins\. 

**Can I set up many venues/cafes/locations?**  \
Yes!

**GDRP?**  \
All user data is automatically deleted in 24 hours after the reservation time. In other words, the plugin doesn't store information about users after the moment when the reservation is not relevant. Data is collected solely for table reservation purposes. No additional processing of information and compilation of statistics is carried out.

**Plugin does not work!😭**  
* Select "Post name" in Settings->Permalinks!!!
* If you use W3 Total Cache(or another cache), you should turn off js minification to make everything work.
* Please note that up to TWO time intervals for one day can be specified in the schedule!
* The PHP version should be as fresh as possible (7 and above).

## License :page_facing_up:

GPL-3.0 License
