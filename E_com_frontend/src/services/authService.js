
import api from './api';
// setup the authService.js file to handle authentication react queries to the backend API. It should have functions for registering, logging in, logging out, and getting the current user.
export const register = (data) => {
  return api.post('/register', data);
};

export const login = (data) => {
  return api.post('/login', data);
};

export const logout = () => {
  return api.post('/logout');
};

export const getUser = () => {
  return api.get('/user');
};
