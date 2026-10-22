
import api from './api';
// CRUD Orders
export const createOrder = (data) => {
  return api.post('/orders', data);
};

export const getOrders = () => {
  return api.get('/orders');
};

export const getOrder = (id) => {
  return api.get(`/orders/${id}`);
};
