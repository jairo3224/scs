// src/services/api.js
import axios from 'axios';

// backend URL
const BASE = 'http://localhost/scs/backend/api';

const api = axios.create({
  baseURL: BASE,
  headers: { 'Content-Type': 'application/json' }
});

export default api;
