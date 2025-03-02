# Tasks

# tables
bhn_merchants
- id
- uuid
- user_id

bhn_merchant_users
- id
- merchant_id
- user_id
- role

bhn_products
- id
- uuid
- product_id
- merchant_id
- merchant_user_id

bhn_order_lookup
- uuid
- merchant_user_id
- merchant_id
- order_id

bhn_order_table_lookup
- id
- uuid
- merchant_id
- merchant_user_id
- table_id

[] Thêm bảng quản lý merchant
[] Thêm bảng quản lý merchant_users
[] Bảng posts, có thêm cột merchant_id
[] Bảng terms, có thêm cột merchant_id
[] Thêm một user role là merchant 
  [] Nếu authen user có role là merchant, thì phải xác định role của họ trong merchant teams để xác nhận quyền
  [] User role là merchant, sẽ không có quyền truy cập vào wc/v3/api
  [] Các role còn lại không phải merchant, thì tuân theo phân quyền của wordpress

[] Xây dựng api bhn-merchant, dựa trên controller của woo
  [] CRUD products
  [] CRUD orders
  [] CRUD customers
  [] CURD order tables

# note
refresh database command

```
drop table wp_bhn_migrations;
drop table wp_bhn_products;
drop table wp_bhn_merchants;
drop table wp_bhn_merchant_users;
update wp_options set option_value = '' where option_name = 'BHN_version';
```