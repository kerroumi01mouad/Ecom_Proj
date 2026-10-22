
import api from './api';
// CRUD Cart
export const getCart = () => {
  return api.get('/cart');
};

export const addToCart = (data) => {
  return api.post('/cart/items', data);
};

export const updateCartItem = (itemId, data) => {
  return api.patch(`/cart/items/${itemId}`, data);
};

export const removeCartItem = (itemId) => {
  return api.delete(`/cart/items/${itemId}`);
};

export const clearCart = () => {
  return api.delete('/cart');
};
