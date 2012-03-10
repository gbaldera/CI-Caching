CI-Caching
================

Version: 1.0

Wrapper library for CodeIgniter's Cache driver.
CI-Caching is based on the Cache Library from Phil Sturgeon (http://philsturgeon.co.uk/code/codeigniter-cache) so you can cache your models and libraries calls.


Requirements
------------

1. PHP 5.2+
2. CodeIgniter 2.0.x and upper
3. APC or Memcached PECL extensions depending on the cache system you are using.

Installation
------------

1. Copy all files to your application folder
2. Edit caching.php file in the config folder to specify your cache preferences. Default adapter is APC.

## Usage

	// load caching library
	$this->load->library('caching');

	// cached model call
	$this->caching->model('food_model', 'getPlates', array($meal_type)); // keep for 60 seconds (Default)

	// cached library call
	$this->caching->library('menu', 'menu_endtime', array($supplier_id), 120); // keep for 2 minutes

	// cached other data
	$this->caching->save('product_id', $data, 120);
	$data = $this->caching->get('product_id');

	// delete chached data
	$this->caching->delete('product_id');

	// delete all chached data
    $this->caching->clean();

    // check if especific adapter is supported
    $this->caching->is_supported();


More info
------------

Visit: http://codeigniter.com/user_guide/libraries/caching.html