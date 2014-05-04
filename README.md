#Magento Gravatar Service Module

This module provides quick access to the [Gravatar](http://en.gravatar.com/) service in Magento. Compatible with Magento 1.x, this module provides a simple model which generates a Gravatar URL based on a given e-mail address.

Setup
------
Download the source, add it to your Magento base directory, clear your cache, and start using it! Be sure to check the Modules Output section of your system configuration to be sure that module has been loaded.

How to Use
------
It's really easy if you're familiar with using models in Magento:

```php
$url = Mage::getSingleton('gravatar/gravatar')
	->setAvatarSize(256)			// optional
	->setMaxRating('pg')			// optional
	->setDefaultImage('retro')		// optional
	->getUrl('johndoe@gmail.com');

echo $url;
// http://www.gravatar.com/avatar/cbf789a69d79b6663ba9cefc806680b1?s=256&r=pg&d=retro
```

That's all there is to it!

License
------
Copyright (c) 2014 Rick Buczynski.

Permission is hereby granted, free of charge, to any person obtaining a copy
of this software and associated documentation files (the "Software"), to deal
in the Software without restriction, including without limitation the rights
to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
copies of the Software, and to permit persons to whom the Software is
furnished to do so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in
all copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
THE SOFTWARE.

