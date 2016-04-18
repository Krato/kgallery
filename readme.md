# KGallery for Laravel 5

KGallery is a photo gallery system for Laravel 5.

### Version
1.0

### Installation

First require this package:

\`\`\`sh
composer require infinety-es/gallery
\`\`\`

Then you need to create a custom Filesystem drive on `filesystem.php`:
\`\`\`php
	'gallery' => [
	    'driver' => 'local',
	    'root'   => base_path('public/gallery_assets'),
	]
\`\`\`

Add Service Provider:
`Infinety\Gallery\GalleryServiceProvider::class,`

Add Links for Admin routes:

`url('admin/galleries')` for Galleries  
`url('admin/galleries/categories')` for Categories


### Todos

 * Front Views

License
---- 

MIT

