-- ============================================================
-- Schema PostgreSQL para Sistema Médico — Clinica Belén
-- Ejecutar en: Supabase SQL Editor > New Query
-- ============================================================

-- 1. EMPRESAS
CREATE TABLE IF NOT EXISTS empresas (
    id          SERIAL PRIMARY KEY,
    nombre      VARCHAR(255) NOT NULL,
    created_at  TIMESTAMP DEFAULT NOW()
);

-- 2. USUARIOS
CREATE TABLE IF NOT EXISTS usuarios (
    id          SERIAL PRIMARY KEY,
    usuario     VARCHAR(100) NOT NULL UNIQUE,
    password    VARCHAR(255) NOT NULL,
    rol_id      INTEGER NOT NULL CHECK (rol_id IN (1, 2, 3)),
    rol         VARCHAR(50) NOT NULL,
    estado      SMALLINT DEFAULT 1 CHECK (estado IN (0, 1)),
    created_at  TIMESTAMP DEFAULT NOW()
);

-- 3. TRABAJADORES
CREATE TABLE IF NOT EXISTS trabajadores (
    id          SERIAL PRIMARY KEY,
    nombres     VARCHAR(255) NOT NULL,
    apellidos   VARCHAR(255) NOT NULL,
    empresa_id  INTEGER NOT NULL REFERENCES empresas(id) ON DELETE RESTRICT,
    created_at  TIMESTAMP DEFAULT NOW()
);

-- 4. TIPOS DE DOCUMENTO
CREATE TABLE IF NOT EXISTS tipos_documento (
    id          SERIAL PRIMARY KEY,
    codigo      VARCHAR(50) NOT NULL UNIQUE,
    nombre      VARCHAR(255) NOT NULL,
    created_at  TIMESTAMP DEFAULT NOW()
);

-- 5. ARCHIVOS MÉDICOS
CREATE TABLE IF NOT EXISTS archivos_medicos (
    id                  SERIAL PRIMARY KEY,
    trabajador_id       INTEGER NOT NULL REFERENCES trabajadores(id) ON DELETE CASCADE,
    tipo_documento_id   INTEGER NOT NULL REFERENCES tipos_documento(id) ON DELETE RESTRICT,
    nombre_archivo      VARCHAR(255) NOT NULL,
    ruta_archivo        TEXT NOT NULL,
    usuario_id          INTEGER NOT NULL REFERENCES usuarios(id) ON DELETE RESTRICT,
    created_at          TIMESTAMP DEFAULT NOW()
);

-- ÍNDICES
CREATE INDEX IF NOT EXISTS idx_trabajadores_empresa ON trabajadores(empresa_id);
CREATE INDEX IF NOT EXISTS idx_archivos_trabajador  ON archivos_medicos(trabajador_id);
CREATE INDEX IF NOT EXISTS idx_archivos_tipo         ON archivos_medicos(tipo_documento_id);
CREATE INDEX IF NOT EXISTS idx_archivos_usuario      ON archivos_medicos(usuario_id);

-- ============================================================
-- DATOS INICIALES
-- ============================================================

-- Roles / tipos de documento de ejemplo
INSERT INTO tipos_documento (codigo, nombre) VALUES
    ('CAMO',         'Certificado de Aptitud Médico Ocupacional'),
    ('EMO',          'Examen Médico Ocupacional'),
    ('LABORATORIO',  'Exámenes de Laboratorio'),
    ('OFTALMOLOGIA', 'Examen Oftalmológico'),
    ('RESUMEN',      'Resumen de Historia Clínica'),
    ('OBSERVACIONES','Observaciones Médicas'),
    ('CONCLUSIONES', 'Conclusiones del Examen')
ON CONFLICT (codigo) DO NOTHING;

-- Usuario admin por defecto (contraseña: admin123)
-- NOTA: cambiar en producción
INSERT INTO usuarios (usuario, password, rol_id, rol, estado) VALUES
    ('admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 1, 'administrativo', 1)
ON CONFLICT (usuario) DO NOTHING;
