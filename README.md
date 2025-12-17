Phase 2 – Shopping Cart
📌 Overview

Phase 2 – Shopping Cart tập trung vào việc xây dựng chức năng giỏ hàng cho website bán nước hoa.
Ở giai đoạn này, người dùng có thể thêm sản phẩm vào giỏ, quản lý số lượng, xóa sản phẩm và xem tổng tiền trước khi chuyển sang bước thanh toán (Phase 3).

🎯 Objectives

Cho phép người dùng thêm sản phẩm vào giỏ hàng

Quản lý danh sách sản phẩm trong giỏ

Cập nhật số lượng sản phẩm theo thời gian thực

Tính toán tổng tiền chính xác

Chuẩn bị dữ liệu giỏ hàng cho bước thanh toán

🧩 Main Features
1. Add to Cart

Người dùng có thể thêm sản phẩm vào giỏ từ:

Trang danh sách sản phẩm

Trang chi tiết sản phẩm

Nếu sản phẩm đã tồn tại trong giỏ:

Tăng số lượng thay vì tạo dòng mới

2. View Cart

Hiển thị danh sách sản phẩm trong giỏ hàng

Thông tin hiển thị:

Tên sản phẩm

Hình ảnh

Giá

Số lượng

Thành tiền từng sản phẩm

3. Update Quantity

Người dùng có thể:

Tăng / giảm số lượng sản phẩm

Nhập trực tiếp số lượng

Thành tiền và tổng tiền được cập nhật tự động

4. Remove Product

Xóa một sản phẩm khỏi giỏ hàng

Giỏ hàng cập nhật lại ngay sau khi xóa

5. Cart Summary

Hiển thị:

Tổng số sản phẩm

Tổng tiền giỏ hàng

Dữ liệu được dùng để chuyển sang Phase 3 – Payment Process

🏗️ Technical Implementation
Frontend

ReactJS

Context API để quản lý trạng thái giỏ hàng:

CartContext

UI cập nhật realtime khi thay đổi giỏ hàng

State Management

Lưu trữ giỏ hàng:

Trong Context

Có thể kết hợp localStorage để giữ giỏ hàng khi reload trang

🔄 User Flow

Người dùng chọn sản phẩm

Click Add to Cart

Sản phẩm xuất hiện trong giỏ hàng

Người dùng chỉnh sửa số lượng / xóa sản phẩm

Kiểm tra tổng tiền

Chuyển sang Phase 3 – Payment Access
