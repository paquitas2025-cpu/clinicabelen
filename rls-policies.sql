-- ============================================================
-- RLS (Row Level Security) — Permisos para el frontend
-- Ejecutar en Supabase SQL Editor después del schema
-- ============================================================

-- 1. USUARIOS — cada uno ve su propio perfil
CREATE POLICY "Usuarios ven su propio perfil"
    ON usuarios FOR SELECT
    TO authenticated
    USING (usuario = (auth.jwt() -> 'user_metadata' ->> 'usuario'));

-- 2. EMPRESAS — todos los autenticados pueden leer
CREATE POLICY "Autenticados pueden leer empresas"
    ON empresas FOR SELECT
    TO authenticated
    USING (true);

-- 3. TRABAJADORES — todos los autenticados pueden leer
CREATE POLICY "Autenticados pueden leer trabajadores"
    ON trabajadores FOR SELECT
    TO authenticated
    USING (true);

-- 4. TIPOS DE DOCUMENTO — todos los autenticados pueden leer
CREATE POLICY "Autenticados pueden leer tipos"
    ON tipos_documento FOR SELECT
    TO authenticated
    USING (true);

-- 5. ARCHIVOS MÉDICOS — insert y read para autenticados
CREATE POLICY "Autenticados pueden insertar archivos"
    ON archivos_medicos FOR INSERT
    TO authenticated
    WITH CHECK (true);

CREATE POLICY "Autenticados pueden leer archivos"
    ON archivos_medicos FOR SELECT
    TO authenticated
    USING (true);

-- ============================================================
-- STORAGE — bucket archivos-medicos
-- ============================================================
CREATE POLICY "Autenticados pueden subir archivos"
    ON storage.objects FOR INSERT
    TO authenticated
    WITH CHECK (bucket_id = 'archivos-medicos');

CREATE POLICY "Autenticados pueden leer archivos del storage"
    ON storage.objects FOR SELECT
    TO authenticated
    USING (bucket_id = 'archivos-medicos');

-- ============================================================
-- Crear usuario de ARCHIVOS para pruebas
-- (opcional: sáltate esto si ya tenés usuarios creados)
-- ============================================================
-- SELECT supabase_admin.create_user con service_role no se puede desde SQL Editor.
-- Mejor lo creo yo desde acá con la API. No necesitás correr esto.
