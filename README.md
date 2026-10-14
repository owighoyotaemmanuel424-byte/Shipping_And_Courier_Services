![visitors](https://visitor-badge.glitch.me/badge?page_id=happiguru/Shipping_And_Courier_Services)

## Shipping and Courrier Service Application

This project is an implementation of a courier and shipping website with features like package tracking, admin-backend, and user-frontend that was built fully with PHP, HTML, and CSS3.

![screenshot](app_screenshot.png)
## Built With

- HTML
- CSS3
- JavaScript/ES6
- Webpack
- PHP

## Live Demo

[Live Demo Link](http://flight.learningo.club/index.php)
![screenshot](tracking.png)

## Database and first administrator setup

Configure the PHP service with these environment variables:

- `DB_HOST`
- `DB_USER`
- `DB_PASSWORD`
- `DB_DATABASE`
- `DB_PORT` (optional; defaults to 3306)

Railway MySQL variables (`MYSQLHOST`, `MYSQLUSER`, `MYSQLPASSWORD`, `MYSQLDATABASE`, `MYSQLPORT`) are also supported.

To create the first administrator, set `ADMIN_SETUP_KEY` in the hosting environment to a unique random secret of at least 32 characters. Open `/admin-setup.php`, enter that key, choose a username and a password of at least 12 characters, and submit. The setup page creates the compatible `admin` table if needed and stores a password hash. It refuses setup if an administrator already exists. After successful setup, remove `ADMIN_SETUP_KEY` from the hosting environment and sign in at `/admin/index.php`. Never commit database credentials or the setup key.

The setup flow needs a working MySQL database and PHP's MySQLi extension. It does not create shipment/tracking tables; import the project's existing database schema for tracking features.

## Getting Started

To get a local copy  and to set it up and running follow these simple example steps.

### Prerequisites

- Browser
- Internet

- Download the code from repository (https://github.com/happiguru/shipping_courier_services).

## Authors

👤 **Stanley Enow Lekunze**

- Github: [@happiguru](https://github.com/happiguru)
- Twitter: [@Lekunze_Nley](https://twitter.com/Lekunze_Nley)
- Linkedin: [lekunze-nley](https://www.linkedin.com/in/lekunze-nley/)


## Show your support

Give a ⭐️ if you like this project!

## Acknowledgments
-This project gave us a better understanding of the visual properties that must be used on a website.

## 📝 License

This project is [MIT](LICENSE) licensed.



