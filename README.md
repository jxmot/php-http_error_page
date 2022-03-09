# PHP HTTP Error Page

This repository contains a "generic" HTTP error page. It's written in PHP/HTML and can be used for most any error page. 

## Features

* A single file for all 40X HTTP errors
* Background image(s) are random and selected from a "pool" of error images when an error page is accessed
* All files (*except* `.htaccess`) are kept in a single folder
* Easy to copy to a website and use. You will only need to edit the `.htaccess` file
* An option to redirect automatically to a different page

# Installation

Two installation locations are possible. The first is a "local" location on an PC or NAS hosted HTTP server, and the second is on a "live" server.

## Testing Locally

For testing on a PC this will require the installation and setup of [XAMPP](https://www.apachefriends.org/index.html) or [MAMP](https://www.mamp.info). I prefer XAMPP.

Sometimes using a local web server can be frustrating because of the path where the *document root* is located. It would possible mean that you would have to place your repo folder(s) in a specific location. For example - `c:\xampp\htdocs`.

But that problem is easily fixed. Like Linux, Windows has the ability to create *folder junctions* (*i.e "symbolic links" in Linux*). So that means that your repositories (or other projects) can be located *anywhere* on the PC where your local web server is running. Just make a *junction* in your document root to access it via HTTP.

**NOTE**: Typically there are no SSL certificates in that type of installation, which is OK because this project does not require SSL.

### Folder Junctions

You might be familiar with a Linux *hard link*. The Window's equivalent is a *junction*. And they are particularly useful when keeping project folders organized in separate and possibly unrelated locations but you want to serve them with XAMPP(*or MAMP*) during development. 

Some alternatives to this method are - 

* Change XAMPP's document root path to the project's path, works for only one project at a time.
* Copy the project files into the document root after one or more edits. 

Neither of those methods are easy to work with. But junctions are a lot easier and since they look like folders you can have as many (*within the limits of Windows*) you need. 

### Example

Let's say you're working on two separate projects and want to test them locally using XAMPP. And they're found in the following paths -

**Project A** - C:\Users\a-user\Documents\Projects\some-project 

**Project B** - D:\projects\web\customer-X\new-site

The following steps will create two project junctions :

1. Open a command-line window and go to `C:\xampp\htdocs`
2. Run the following commands - 
    a) `mklink /j c:\xampp\htdocs\projecta C:\Users\a-user\Documents\Projects\some-project`
    b) `mklink /j c:\xampp\htdocs\projectb D:\projects\web\customer-X\new-site`
    
**`mklink`** - command to create the junction
**`/j`** - tells mklink to create a folder junction.
**`c:\xampp\htdocs\projectb`** - a **nonexistent** folder, this is the junction
**`D:\projects\web\customer-X\new-site`** - this is the target of the junction

3. Then in your browser go to - 

    `http://`**`localhost`**`/projecta/index.html`
**--OR--**
    `http://`**`localhost`**`/projectb/index.html`

NOTE: The "junctions" are permanent until deleted from the `c:\xampp\htdocs` folder. You **must** use the `rmdir`(*Windows*) to remove the junction and **leave the files behind**.

## Going Live"

Most internet web servers have a *common* location for website files. It's typically located at `/home/$USER/public_html`. Where **`$USER`** is the *user* that owns the `public_html` folder. Depending on your server's particular configuration that folder may be named differently or in a different location.

1) Copy the `/errpages` folder and its **contents** to `/home/$USER/public_html` (or its equivalent). You now have `/home/$USER/public_html/errpages`.
2) Open the `.htaccess` file in the repository.
3) **Copy** this section out of it - 
```
# PHP error page, edit as needed for a "live" site
###ErrorDocument 400 /errpages/httperror.php
###ErrorDocument 401 /errpages/httperror.php
###ErrorDocument 403 /errpages/httperror.php
###ErrorDocument 404 /errpages/httperror.php
###ErrorDocument 405 /errpages/httperror.php
```
4) Open your server's `.htaccess` file. It will be located in the `public_html` folder. It is possible that your server does **not** have an `.htaccess`. If that happens create a new one.

5) **Paste** the lines from step 3 into your server's `htaccess` file. It should go near the top of the file, but it's not necessary to make it the first thing. Then edit what you pasted to look like this (comment `###` removed) - 
```
# PHP error page, edit as needed for a "live" site
ErrorDocument 400 /errpages/httperror.php
ErrorDocument 401 /errpages/httperror.php
ErrorDocument 403 /errpages/httperror.php
ErrorDocument 404 /errpages/httperror.php
ErrorDocument 405 /errpages/httperror.php
```

To see the error page working open your browser and go to - 
`http://your_server/`**`not_here`**

You should see a "400" error page.

# Development Notes

* Development Environment:
  * Host OS: Windows
  * Local HTTP Server: XAMPP
    * PHP 5.6 

