Phase 3 – Payment Access
📌 Overview

Phase 3 – Payment Access tập trung vào việc xây dựng quy trình thanh toán cho website bán nước hoa.
Ở giai đoạn này, hệ thống cho phép người dùng lựa chọn phương thức thanh toán và mô phỏng luồng thanh toán thành công / thất bại, làm tiền đề cho việc hoàn thiện hệ thống đặt hàng.

⚠️ Lưu ý: Các cổng thanh toán trong Phase 3 (VNPAY, MoMo) là fake (simulation), không kết nối thanh toán thật.

🎯 Objectives

Xây dựng luồng thanh toán hoàn chỉnh

Cho phép lựa chọn phương thức thanh toán

Mô phỏng kết quả thanh toán (success / failed)

Ghi nhận trạng thái thanh toán cho đơn hàng

Đảm bảo sẵn sàng tích hợp cổng thanh toán thật trong tương lai

🧩 Main Features
1. Checkout Process

Người dùng tiến hành checkout từ giỏ hàng (Phase 2)

Hiển thị thông tin:

Danh sách sản phẩm

Tổng tiền

Thông tin người nhận

Xác nhận đơn hàng trước khi thanh toán

2. Payment Method Selection

Hỗ trợ các phương thức:

VNPAY (Fake)

MoMo (Fake)

Người dùng chọn 1 phương thức thanh toán trước khi tiếp tục

3. Simulated Payment Flow
VNPAY (Fake)

Chuyển sang trang thanh toán mô phỏng

Người dùng xác nhận thanh toán

Trả về kết quả:

Success

Failed

MoMo (Fake)

Mô phỏng quy trình tương tự VNPAY

Kết quả được xử lý nội bộ trong hệ thống

4. Payment Result Handling

Sau khi thanh toán:

Cập nhật trạng thái đơn hàng

Hiển thị trang kết quả thanh toán

Trạng thái đơn hàng:

Pending

Paid

Failed

5. Order Confirmation

Khi thanh toán thành công:

Đơn hàng được xác nhận

Người dùng nhận thông báo hoàn tất

Khi thất bại:

Cho phép quay lại và thanh toán lại

🏗️ Technical Implementation
Frontend

ReactJS

Quản lý luồng thanh toán bằng Context / State

Tách riêng logic cho từng phương thức thanh toán

Backend / Logic (Simulation)

Xử lý trạng thái thanh toán giả lập

Không gọi API cổng thanh toán thật

Dễ dàng thay thế bằng API thật trong Phase mở rộng

🔄 Payment Flow

Người dùng vào trang Checkout

Chọn phương thức thanh toán

Chuyển sang luồng thanh toán (Fake)

Nhận kết quả thanh toán

Cập nhật trạng thái đơn hàng

Hiển thị kết quả cho người dùng

📁 Related Phases

Phase 1: Product Catalog

Phase 2: Shopping Cart

Phase 3: Payment Access ✅

Phase 4: Access Control

🚀 How to Run Project
docker compose up -d


Sau khi chạy, thực hiện:

Thêm sản phẩm vào giỏ hàng

Checkout

Chọn VNPAY hoặc MoMo (Fake)

Kiểm tra kết quả thanh toán
