# ITSTYR - trin for trin opsætning.

## Setup 
*Hvis der ikke er data, på 'admin/system' eller 'admin/group', eller oprettet en bruger' skal punkterne i README.md først følges*


## Generelt
Itstyr er et Smiley-oversigts-site over systemer og anmeldelser.

Websitet skal konfigurers i den rigtige rækkefølge for at få vist smileys. 

Først skal der oprettets en Gruppe, og efterfølgende: Kategori, og Tema. 

Til sidst skal man gå ind på dashboarded hvorfra man tilføje status smileys.

## 1. Gruppe
- Klik på Easyadmin Menupunktet Gruppe
    - du er nu på gruppes index-side
- Klik på "tilføj gruppe"
  - du er nu på gruppes new-side
    - udfyld textfeltet "Navn"
    - udfyld "Roller" checkboxes
    - klik "Opret" eller "Opret og tilføj ny"

## 2. Kategori
- Klik på Easyadmin Menupunktet Kategorier
  - du er nu på kategories index-side
- Klik på "tilføj kategori"
  - du er nu på gruppes new-side
    - udfyld textfeltet "Navn"
    - klik på 'Tilføj ny emne'
      - udfyld "Vægt" med et tal (required)
      - udfyld textfeltet "Spørgsmål"
    - klik "Opret" eller "Opret og tilføj ny"

## 3. Tema
- Klik på Easyadmin Menupunktet Tema
  - du er nu på Temas index-side
- Klik på "tilføj Tema"
  - du er nu på Tema new-side
    - udfyld textfeltet "Navn"
    - klik på 'Systemer' og tilføj et eller flere systemer
    - klik på ' Anmeldelsr' og tilføj et eller flere Anmeldelser
      - udfyld "Vægt" med et tal (required)
      - klik på 'Kategori' og tilføj en kategori
  


## 3. Dashboard Systemer 
*(Samme fremgangsmåde i Dashboard Anmeldelse)*
- Klik på Easyadmin Menupunktet Dashboard Systemer
  - du er nu på DashboardSystemer index-side
- Klik på et system i den skrå bjælke.
  - du er nu på et systems detail-side
- Klik Ret
  - du er nu på et systems edit-side
    - udfyld textfeltet "Noter"
    - klik på 'Grupper' og tilføj en eller flere grupper
    - udfyld "Link til systemdokumentation"
    - Klik "Gem ændringer"
      - du er nu på System index-side
- Klik på Easyadmin Menupunktet Dashboard Systemer
  - du er nu på DashboardSystemer index-side
    - en Tabel med Spørgsmål vil nu være synlig.
      - Klik på det samme system du gjorde før i den skrå bjælke.
        - du er nu på et systems detail-side
        - der er nu kommet "spørgsmål" under "Smileys"
          - Klik "Ret" Under Smiley-punktet "Handlinger"
          - klik på 'Smiley' og tilføj en smiley
          - udfyld textfeltet "Notat"
          - Klik "Gem ændringer"
            - du er nu på Dashboard System index-side
            - Der skulle nu gerne være en smiley, i Spørgsmål Tabelen.
