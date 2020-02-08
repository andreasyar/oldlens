# Old lens correction

Toolkit for correction color shifts from old lenses used on modern digital cameras.

## The idea

TODO

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

Assume /home/user/Pictures/6/darktable_exported/02085774-f11-Inf.png is a reference image
Assume /home/user/Pictures/03093373.jpg is an image to be corrected

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