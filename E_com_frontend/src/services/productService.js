
import api from './api';
// get all product from the backend api using react query .
export const getProducts = (params = {}) => {
    return api.get('/products', { params });
};
// get a specific product to see the details of this product
export const getProduct = (id) => {
    return api.get(`/products/${id}`);
};
// create a new product by admin user 
export const createProduct = (data) => {
    return api.post('/products', data);
};
// update a new product by admin user 
export const updateProduct = (id, data) => {
    return api.put(`/products/${id}`, data);
};
// delete a new product by admin user 
export const deleteProduct = (id) => {
    return api.delete(`/products/${id}`);
};
