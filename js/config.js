// ============================================================
// Supabase - Configuración central
// ============================================================
const SUPABASE_URL = 'https://emqqybznpypdwfscuzdu.supabase.co';
const SUPABASE_ANON_KEY = 'eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJpc3MiOiJzdXBhYmFzZSIsInJlZiI6ImVtcXF5YnpucHlwZHdmc2N1emR1Iiwicm9sZSI6ImFub24iLCJpYXQiOjE3ODQwNDYyNzcsImV4cCI6MjA5OTYyMjI3N30._m1qaPtaPvNNIHmPWAJBhV9dke4FhCmuG92aB7g1SW0';

const supabase = window.supabase.createClient(SUPABASE_URL, SUPABASE_ANON_KEY, {
    auth: {
        autoRefreshToken: true,
        persistSession: true
    }
});

// ============================================================
// Helpers de autenticación
// ============================================================

/** Verifica que haya sesión activa. Si no, redirige al login. */
async function checkAuth() {
    const { data: { session } } = await supabase.auth.getSession();
    if (!session) {
        window.location.href = '/index.html';
        return null;
    }
    return session;
}

/** Obtiene el perfil del usuario desde la tabla usuarios */
async function getUserProfile() {
    const sess = await checkAuth();
    if (!sess) return null;

    const usuario = sess.user.user_metadata?.usuario;
    if (!usuario) return null;

    const { data, error } = await supabase
        .from('usuarios')
        .select('id, usuario, rol_id, rol')
        .eq('usuario', usuario)
        .single();

    if (error || !data) {
        console.error('Error al obtener perfil:', error);
        return null;
    }
    return data;
}

/** Cierra sesión */
async function logout() {
    await supabase.auth.signOut();
    window.location.href = '/index.html';
}
