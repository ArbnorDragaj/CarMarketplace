# CarMarketPlace

CarMarketPlace është një web aplikacion i krijuar me PHP, HTML, CSS dhe JavaScript. Projekti shërben për shfaqjen dhe menaxhimin e veturave, ku përdoruesit mund të shohin modelet e veturave, ndërsa admini ka qasje në një panel të veçantë për menaxhimin e tyre.

## Përshkrimi

Ky projekt paraqet një platformë të thjeshtë për treg të veturave. Projekti përmban faqe publike si Ballina, Rreth Nesh, Shërbimet/Modelet, Kontakti dhe Blog. Gjithashtu përmban sistem login-i me dy role: user dhe admin.

Admini mund të hyjë në panelin e administrimit, ku mund të shtojë vetura të reja, të ngarkojë fotografi, t’i aktivizojë/çaktivizojë veturat dhe t’i fshijë ato nga lista.

## Teknologjitë e përdorura

- PHP
- HTML
- CSS
- JavaScript
- PHP Sessions
- XAMPP / Apache
- Font Awesome
- Git dhe GitHub

## Struktura e projektit

```text
CarMarketplace/
│
├── classes/
│   ├── carCL.php
│   └── userCl.php
│
├── img/
│   └── fotografitë e veturave
│
├── Includes/
│   ├── header.php
│   └── footer.php
│
├── Script/
│   └── fajllat JavaScript
│
├── Style/
│   ├── login.css
│   └── fajllat CSS
│
├── uploads/
│   └── cars/
│       └── fotografitë e ngarkuara nga admini
│
├── admin.php
├── blog.php
├── contact.php
├── index.php
├── login.php
├── logout.php
├── models.php
├── posts.json
├── rreth-nesh.php
└── README.md
```
