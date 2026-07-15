// ============================================================
// Supabase — Cliente mínimo (sin librería externa)
// Usa fetch directo a la API REST de Supabase
// ============================================================
const SUPABASE_URL = 'https://emqqybznpypdwfscuzdu.supabase.co';
const SUPABASE_ANON_KEY = 'eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJpc3MiOiJzdXBhYmFzZSIsInJlZiI6ImVtcXF5YnpucHlwZHdmc2N1emR1Iiwicm9sZSI6ImFub24iLCJpYXQiOjE3ODQwNDYyNzcsImV4cCI6MjA5OTYyMjI3N30._m1qaPtaPvNNIHmPWAJBhV9dke4FhCmuG92aB7g1SW0';
const PROJECT_REF = 'emqqybznpypdwfscuzdu';
const STORAGE_KEY = 'sb-' + PROJECT_REF + '-auth-token';

// ---- Session helpers ----
function getSession() {
    try {
        var raw = localStorage.getItem(STORAGE_KEY);
        return raw ? JSON.parse(raw) : null;
    } catch (e) { return null; }
}

function saveSession(session) {
    localStorage.setItem(STORAGE_KEY, JSON.stringify(session));
}

function clearSession() {
    localStorage.removeItem(STORAGE_KEY);
}

// ---- Cliente Supabase minimal ----
var supabase = {
    auth: {
        signInWithPassword: async function (_a) {
            var email = _a.email, password = _a.password;
            try {
                var res = await fetch(SUPABASE_URL + '/auth/v1/token?grant_type=password', {
                    method: 'POST',
                    headers: {
                        'apikey': SUPABASE_ANON_KEY,
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({ email: email, password: password })
                });
                var json = await res.json();
                if (!res.ok) return { data: null, error: json };
                var session = {
                    access_token: json.access_token,
                    token_type: json.token_type,
                    expires_in: json.expires_in,
                    expires_at: json.expires_at,
                    refresh_token: json.refresh_token,
                    user: json.user
                };
                saveSession(session);
                return { data: { session: session, user: json.user }, error: null };
            } catch (err) {
                return { data: null, error: err };
            }
        },
        getSession: async function () {
            return { data: { session: getSession() }, error: null };
        },
        signOut: async function () {
            var session = getSession();
            if (session && session.access_token) {
                await fetch(SUPABASE_URL + '/auth/v1/logout', {
                    method: 'POST',
                    headers: {
                        'apikey': SUPABASE_ANON_KEY,
                        'Authorization': 'Bearer ' + session.access_token
                    }
                });
            }
            clearSession();
            return { error: null };
        }
    },
    from: function (table) {
        var params = new URLSearchParams();
        var headers = {
            'apikey': SUPABASE_ANON_KEY,
            'Accept': 'application/json',
            'Content-Type': 'application/json'
        };
        var method = 'GET';
        var body = null;
        var prefer = null;

        var builder = {
            select: function (columns) {
                params.set('select', columns || '*');
                return builder;
            },
            eq: function (column, value) {
                params.append(column, 'eq.' + value);
                return builder;
            },
            order: function (column, opts) {
                var dir = (opts && opts.ascending === false) ? 'desc' : 'asc';
                params.set('order', column + '.' + dir);
                return builder;
            },
            single: function () {
                headers['Accept'] = 'application/vnd.pgrst.object+json';
                return builder;
            },
            insert: function (data) {
                method = 'POST';
                body = JSON.stringify(data);
                prefer = 'return=representation';
                return builder;
            },
            update: function (data) {
                method = 'PATCH';
                body = JSON.stringify(data);
                prefer = 'return=representation';
                return builder;
            },
            delete: function () {
                method = 'DELETE';
                return builder;
            },
            then: function (resolve, reject) {
                (async function () {
                    try {
                        var session = getSession();
                        if (session && session.access_token) {
                            headers['Authorization'] = 'Bearer ' + session.access_token;
                        }
                        if (prefer) headers['Prefer'] = prefer;

                        var qs = params.toString();
                        var url = SUPABASE_URL + '/rest/v1/' + table;
                        if (qs) url += '?' + qs;

                        var res = await fetch(url, {
                            method: method,
                            headers: headers,
                            body: body
                        });

                        var text = await res.text();

                        if (!res.ok) {
                            var err;
                            try { err = JSON.parse(text); } catch (e) { err = { message: text }; }
                            resolve({ data: null, error: err });
                            return;
                        }

                        if (!text) {
                            resolve({ data: null, error: null });
                            return;
                        }

                        var data = JSON.parse(text);

                        // single() devuelve un objeto, no array
                        if (headers['Accept'] === 'application/vnd.pgrst.object+json') {
                            if (Array.isArray(data)) {
                                if (data.length === 0) {
                                    resolve({ data: null, error: { message: 'No rows returned' } });
                                    return;
                                }
                                resolve({ data: data[0], error: null });
                                return;
                            }
                            resolve({ data: data, error: null });
                            return;
                        }

                        resolve({ data: data, error: null });
                    } catch (err) {
                        reject(err);
                    }
                })();
            }
        };

        return builder;
    },
    storage: {
        from: function (bucket) {
            return {
                upload: async function (path, file, opts) {
                    try {
                        var session = getSession();
                        var headers = {
                            'apikey': SUPABASE_ANON_KEY
                        };
                        if (session && session.access_token) {
                            headers['Authorization'] = 'Bearer ' + session.access_token;
                        }
                        if (opts && opts.contentType) headers['Content-Type'] = opts.contentType;
                        if (opts && opts.cacheControl) headers['cache-control'] = 'max-age=' + opts.cacheControl;

                        var res = await fetch(SUPABASE_URL + '/storage/v1/object/' + bucket + '/' + path, {
                            method: 'POST',
                            headers: headers,
                            body: file
                        });

                        if (!res.ok) {
                            var text = await res.text();
                            var err;
                            try { err = JSON.parse(text); } catch (e) { err = { message: text }; }
                            return { data: null, error: err };
                        }
                        return { data: { path: path }, error: null };
                    } catch (err) {
                        return { data: null, error: err };
                    }
                }
            };
        }
    }
};

// ============================================================
// Helpers de autenticación
// ============================================================

/** Verifica que haya sesión activa. Si no, redirige al login. */
async function checkAuth() {
    var _a = await supabase.auth.getSession(), session = _a.data.session;
    if (!session) {
        window.location.href = '/index.html';
        return null;
    }
    return session;
}

/** Obtiene el perfil del usuario desde la tabla usuarios */
async function getUserProfile() {
    var sess = await checkAuth();
    if (!sess) return null;

    var usuario = sess.user && sess.user.user_metadata && sess.user.user_metadata.usuario;
    if (!usuario) return null;

    var _a = await supabase
        .from('usuarios')
        .select('id, usuario, rol_id, rol')
        .eq('usuario', usuario)
        .single();
    var data = _a.data, error = _a.error;

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
