# CarMarketPlace

CarMarketPlace është një web aplikacion për prezantimin dhe menaxhimin e veturave. Projekti është ndërtuar me PHP, MySQL, HTML, CSS dhe JavaScript. Përdoruesit mund të shohin modelet e veturave, të filtrojnë rezultatet, të lexojnë postime në blog dhe të dërgojnë mesazhe kontakti. Admini ka panel të veçantë për menaxhimin e veturave dhe postimeve.



## Teknologjitë e përdorura

- PHP
- MySQL
- PDO
- HTML5
- CSS3
- JavaScript
- PHP Sessions
- Cookies
- XAMPP / Apache
- Font Awesome

## Struktura e projektit

```text
CarMarketPlace/
│
├── classes/
│   ├── carCL.php
│   └── userCl.php
│
├── database/
│   └── car_marketplace.sql
│
├── Includes/
│   ├── header.php
│   └── footer.php
│
├── Script/
│   ├── admin.js
│   ├── blog.js
│   ├── contact.js
│   └── models.js
│
├── Style/
│   ├── style.css
│   ├── admin.css
│   ├── login.css
│   ├── models.css
│   ├── contact.css
│   └── rreth-nesh.css
│
├── uploads/
│   ├── cars/
│   └── blog/
│
├── add_car.php
├── admin.php
├── blog.php
├── config.php
├── contact.php
├── delete_car.php
├── edit_car.php
├── index.php
├── login.php
├── logout.php
├── models.php
├── register.php
├── rreth-nesh.php
├── posts.json
└── README.md
```

## Databaza

Emri i databazës në `config.php` është:

```text
car_marketplace
```

Tabelat kryesore të projektit janë:

| Tabela | Qëllimi |
|---|---|
| `users` | Ruan përdoruesit, email-in, password-in e hash-uar dhe rolin `user/admin`. |
| `cars` | Ruan veturat, çmimin, vitin, tipin, karburantin, fotografinë dhe statusin. |
| `posts` | Ruan postimet e blogut, kategorinë, përmbajtjen, fotografinë dhe autorin. |
| `contact_messages` | Ruan mesazhet nga forma e kontaktit dhe statusin e dërgimit të email-it. |

`contact_messages` krijohet automatikisht nga `contact.php` nëse mungon. Për tabelat `users`, `cars` dhe `posts`, projekti pret file-in `database/car_marketplace.sql`.

## Si të bëhet run në XAMPP

1. Shkarko ose kopjo projektin në folderin:

```text
C:/xampp/htdocs/CarMarketPlace
```

2. Hape XAMPP Control Panel.
3. Starto `Apache` dhe `MySQL`.
4. Sigurohu që `config.php` ka këto vlera ose përshtati sipas kompjuterit tënd:

```php
$host = "127.0.0.1";
$dbname = "car_marketplace";
$dbUser = "root";
$dbPass = "";
$ports = [3306, 3307];
```

5. Hape projektin në browser:

```text
http://localhost/CarMarketPlace/index.php
```

6. Regjistro një përdorues të ri nga `register.php`.
7. Për qasje në admin panel, vendos rolin e user-it si `admin` në databazë:

```sql
UPDATE users SET role = 'admin' WHERE username = 'username_i_userit';
```

8. Pastaj kyçu dhe hap:

```text
http://localhost/CarMarketPlace/admin.php
```

## Testimi i projektit

| Testi | Çfarë duhet të ndodhë |
|---|---|
| Register me të dhëna valide | Krijohet user i ri në databazë. |
| Login me të dhëna të sakta | User-i ridrejtohet në faqen përkatëse. |
| Login gabim | Shfaqet mesazh gabimi. |
| Shto veturë si admin | Vetura ruhet në databazë dhe foto në `uploads/cars/`. |
| Edito veturë | Ndryshimet ruhen në databazë. |
| Çaktivizo veturë | Vetura nuk shfaqet në `models.php`. |
| Fshi veturë | Vetura dhe fotografia e saj fshihen. |
| Filtro modelet | Shfaqen vetëm veturat që përputhen me filtrin. |
| Dërgo kontakt | Mesazhi ruhet në `contact_messages`. |
| Hyr në admin si user i zakonshëm | Qasja bllokohet/ridrejtohet. |



## Autor / Grupi

Gerti Parduzi
Ermal Berisha
Arbnor Dragaj
orlind Bjaraktari
Artin Mehana

## Përfundim

CarMarketPlace demonstron një aplikacion të plotë web me PHP dhe MySQL, duke përfshirë autentikim, role, admin panel, CRUD, upload fotografish, blog, kontakt dhe validim të të dhënave. Projekti është i përshtatshëm për prezantim dhe mbrojtje në lëndën e web-it.
