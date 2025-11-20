// src/services/auth.js

export function setToken(token) {
  localStorage.setItem('scs_token', token);
}

export function getToken() {
  return localStorage.getItem('scs_token');
}

export function removeToken() {
  localStorage.removeItem('scs_token');
}

// decode JWT payload (basic)
export function parseJwt(token) {
  if (!token) return null;
  try {
    const payload = token.split('.')[1];
    const json = atob(payload.replace(/-/g, '+').replace(/_/g, '/'));
    return JSON.parse(decodeURIComponent(escape(json)));
  } catch (e) {
    return null;
  }
}

export function getUserFromToken() {
  const t = getToken();
  if (!t) return null;
  return parseJwt(t);
}
