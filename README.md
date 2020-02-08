# Old lens correction

Toolkit for correction color shifts from old lenses used on modern digital cameras.

## The idea

The idea is to take a picture of a gray card, which, in theory, should not have any color shifts. 
But, since there is a color shift on my camera, I made a simple set of scripts that converts the image 
to Lab and saves the components "a" and "b" separately as an array. After that, in the arrays "a" and "b", 
the region that is most not affected by color shift is searched. The arithmetic mean value of the color 
component in this region is taken as such a value, which, in theory, should be on the whole image, 
since this is an image of a gray card. After that, an array of deviations of the color component from 
the arithmetic mean found at the previous step is created. Next, using this array of deviations, the 
color components in the image that need to be corrected are converted. For example, consider a pixel 
in a picture of a gray card with coordinates is 0,0. Suppose its value on "a" component is a = 20, while 
we calculated that the arithmetic mean value of the color component ("a" in this case) in the region most 
not affected by color shift is equal to a = 1. We write the value 1 - 20 = -19 into the array of "a" deviations.
Then, let's say we want to fix some kind of image. Consider its pixel with coordinates 0,0. Suppose that
the value of the component "a" in this pixel is equal to a = -20, we apply our correction to
it -20 + (-19) = -39 The pixel value is fixed: there were too many Magenta in it, and now it has 
as many Magenta as it would have been if there had been no color shift.

![Example](http://helix.yars.free.net/oldlens/Selection_383.jpg)

## Getting Started

I create this tools for fast and rough implement my ideas how to fix color shifts 
when I use my good old MULTI COATING PENTACON auto 2.8/29 on my Samsung NX1000. The problem 
is (how I see it) what vintage manual lenses has no firmware so digital camera do not 
know how to adjust colors provided by lens. It's a bit nonsense for me (as a newbie) 
what camera should adjust something when read image from sensor but the fact is image 
from native Samsung lens is very well while image from vintage lens is color shifted a lot.

### Prerequisites

Uses PHP and PHP GD extension.

### Installing

TODO: How I create reference

* Assume /home/user/Pictures/6/darktable_exported/02085774-f11-Inf.png is a reference image
* Assume /home/user/Pictures/03093373.jpg is an image to be corrected

```
$ php channel.php /home/user/Pictures/6/darktable_exported/02085774-f11-Inf.png a > channel_a_f11_Inf.json
$ php channel.php /home/user/Pictures/6/darktable_exported/02085774-f11-Inf.png b > channel_b_f11_Inf.json
$ php create_correction.php channel_a_f11_Inf.json > correction_a_f11_Inf.json
$ php create_correction.php channel_b_f11_Inf.json > correction_b_f11_Inf.json
$ php correction.php /home/user/Pictures/03093373.jpg jpeg correction_a_f11_Inf.json a
$ php correction.php corrected_a.bmp bmp correction_b_f11_Inf.json b
```

To view all three images to see a correction effect (using Eye of MATE image viewer)

```
$ eom /home/user/Pictures/03093373.jpg &
$ eom corrected_a.bmp &
$ eom corrected_b.bmp &
```

## TODO

* Complete masks generator
* Publish color shift samples for Pentacon (may be Helios and Industar too)
* Fix blue pixels bug
* Use some lib from RGB to Lab (and overwise) conversion
* Rewrite to C language may be
* Do work in RAW, may be as Darktable module