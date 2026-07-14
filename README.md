# Sistema Médico — Clinica Belén

Sistema de gestión de archivos médicos ocupacionales con autenticación por roles y almacenamiento en la nube.

## Stack

- **Frontend:** HTML + CSS + JavaScript (estático)
- **Auth:** Supabase Auth
- **Base de datos:** PostgreSQL (Supabase)
- **Storage:** Supabase Storage (archivos PDF)
- **Hosting:** Vercel (CDN global)

## Roles

| Rol | Acceso |
|-----|--------|
| Administrativo (`/admin/`) | Gestión de usuarios, empresas y trabajadores |
| Médico (`/medico/`) | Visualización de exámenes |
| Archivos (`/archivos/`) | Subida de documentos médicos |

## Usuarios de prueba

| Usuario | Contraseña | Rol |
|---------|-----------|-----|
| `admin` | `admin123` | Administrativo |
| `archivos` | `archivos123` | Encargado de Archivos |

## Despliegue

[![Deploy with Vercel](https://vercel.com/button)](https://vercel.com/new)
# clinica-belen
