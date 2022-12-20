# Content Migration using Custom WP-CLI

[NWMLS](https://www.nwmls.com/) wanted to migrate their old website content (in the form of bunch of HTML files) to their new website (in the form of WordPress posts).

## Challenge

They provided the zip containing all the HTML files flooded in multiple directories and subdirectories. 

Also, they wanted to retain directory structure in URL to align with their existing website. For example, /abc/cba/xyz.html need to convert to https://nwmls.com/abc/cba/xyz.

## Solution

I've created a custom WP-CLI script as WordPress plugin to migrate all the content on the go. The migration was blazing fast and seamless.
