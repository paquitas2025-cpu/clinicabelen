-- ============================================================
-- RLS Policies para CRUD del Panel Administrativo
-- Ejecutar en Supabase SQL Editor
-- ============================================================

-- 1. EMPRESAS — CRUD para admin (rol_id = 1)
CREATE POLICY "Admin insert empresas"
    ON empresas FOR INSERT TO authenticated
    WITH CHECK ((auth.jwt() -> 'user_metadata' ->> 'rol_id')::int = 1);

CREATE POLICY "Admin update empresas"
    ON empresas FOR UPDATE TO authenticated
    USING ((auth.jwt() -> 'user_metadata' ->> 'rol_id')::int = 1);

CREATE POLICY "Admin delete empresas"
    ON empresas FOR DELETE TO authenticated
    USING ((auth.jwt() -> 'user_metadata' ->> 'rol_id')::int = 1);

-- 2. TRABAJADORES — CRUD para admin
CREATE POLICY "Admin insert trabajadores"
    ON trabajadores FOR INSERT TO authenticated
    WITH CHECK ((auth.jwt() -> 'user_metadata' ->> 'rol_id')::int = 1);

CREATE POLICY "Admin update trabajadores"
    ON trabajadores FOR UPDATE TO authenticated
    USING ((auth.jwt() -> 'user_metadata' ->> 'rol_id')::int = 1);

CREATE POLICY "Admin delete trabajadores"
    ON trabajadores FOR DELETE TO authenticated
    USING ((auth.jwt() -> 'user_metadata' ->> 'rol_id')::int = 1);

-- 3. USUARIOS — CRUD para admin (solo datos públicos, no auth)
CREATE POLICY "Admin insert usuarios"
    ON usuarios FOR INSERT TO authenticated
    WITH CHECK ((auth.jwt() -> 'user_metadata' ->> 'rol_id')::int = 1);

CREATE POLICY "Admin update usuarios"
    ON usuarios FOR UPDATE TO authenticated
    USING ((auth.jwt() -> 'user_metadata' ->> 'rol_id')::int = 1);

CREATE POLICY "Admin delete usuarios"
    ON usuarios FOR DELETE TO authenticated
    USING ((auth.jwt() -> 'user_metadata' ->> 'rol_id')::int = 1);

-- 4. Permitir que admin vea TODOS los usuarios (no solo el suyo)
--    Reemplaza la policy anterior si existe
DROP POLICY IF EXISTS "Usuarios ven su propio perfil" ON usuarios;
CREATE POLICY "Usuarios ven su propio perfil"
    ON usuarios FOR SELECT TO authenticated
    USING (
        usuario = (auth.jwt() -> 'user_metadata' ->> 'usuario')
        OR (auth.jwt() -> 'user_metadata' ->> 'rol_id')::int = 1
    );
