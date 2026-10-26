# Web Gusti - Docker Praktikum

PHP native + MySQL. Tidak menggunakan framework.

Jalankan lokal:
1. Salin `.env.example` menjadi `.env`
2. Isi password
3. `docker compose up -d --build`
4. Buka `http://localhost`

Cek:
`docker compose ps`

Uji persistence:
tambahkan data -> `docker compose down` -> `docker compose up -d` -> data tetap ada.
