# PHP Nette CMS Project

## About the Project

This website serves as a simple demo content management system (CMS) built using the Nette framework.

## The application includes the following functionalities:

On the homepage, all articles are displayed – they can be sorted by date (newest first / oldest first).

Login to manage articles is available for two types of user roles:

Admin – has full permissions (i.e., can add, edit, and delete any article).

Editor – can create new articles and manage (edit or delete) only their own articles.

When a new article is added by an editor, a notification email is sent to the user(s) with the admin role.

## 📂 Project Folders & Files Tree Structure (vendor, temp folders ignored)

```
├── 📄 .htaccess
├── 📄 README.md
├── 📁 app
      ├── 📄 Bootstrap.php
      ├── 📁 Model
            ├── 📄 Database.php
            ├── 📄 Mail.php
            ├── 📄 Post.php
            └── 📄 User.php
      ├── 📁 Presenters
            ├── 📄 AdministrationPresenter.php
            ├── 📄 BasePresenter.php
            ├── 📄 Error4xxPresenter.php
            ├── 📄 ErrorPresenter.php
            ├── 📄 HomepagePresenter.php
            ├── 📄 PostPresenter.php
            ├── 📄 SignPresenter.php
            └── 📁 templates
                  ├── 📄 @emailAdmin.latte
                  ├── 📄 @layout.latte
                  ├── 📁 Administration
                        └── 📄 default.latte
                  ├── 📁 Error
                        ├── 📄 403.latte
                        ├── 📄 404.latte
                        ├── 📄 405.latte
                        ├── 📄 410.latte
                        ├── 📄 4xx.latte
                        ├── 📄 500.phtml
                        └── 📄 503.phtml
                  ├── 📁 Homepage
                        ├── 📄 about.latte
                        └── 📄 default.latte
                  ├── 📁 Post
                        ├── 📄 create.latte
                        ├── 📄 edit.latte
                        └── 📄 show.latte
                  ├── 📁 Sign
                        └── 📄 in.latte
                  ├── 📄 footer.latte
                  └── 📄 nav.latte
      └── 📁 Router
            └── 📄 RouterFactory.php
├── 📁 bin
├── 📄 composer.json
├── 📄 composer.lock
├── 📁 config
      ├── 📄 common.neon
      ├── 📄 local.neon
      └── 📄 services.neon
├── 📁 log
├── 📁 www
      ├── 📄 .htaccess
      ├── 📁 css
            ├── 📄 emailAdmin.css
            └── 📄 style.css
      ├── 📄 favicon.ico
      ├── 📁 img
            ├── 📁 icons
                  ├── 📄 delete.png
                  ├── 📄 edit.png
                  ├── 📄 first.png
                  ├── 📄 last.png
                  ├── 📄 menu.png
                  ├── 📄 newest.png
                  ├── 📄 next.png
                  ├── 📄 oldest.png
                  └── 📄 previous.png
            └── 📄 nette_logo-200.png
      ├── 📄 index.php
      ├── 📁 js
            ├── 📄 script.js
      └── 📄 robots.txt

```
