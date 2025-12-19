# Perfume Shop Website – CI & Unit Testing

[![WebFull Unit Tests](https://github.com/nhu2004/SGU25_KTPM/actions/workflows/webfull-unit-test.yml/badge.svg?branch=ci-webfull)](https://github.com/nhu2004/SGU25_KTPM/actions/workflows/webfull-unit-test.yml)

## Giới thiệu
Đây là đồ án website bán nước hoa được phát triển theo **4 phase**:
- Phase 1: Product Catalog - Hiển thị danh sách sản phẩm, lọc theo danh mục / thương hiệu / giá.
- Phase 2: Shopping Cart - Thêm sản phẩm vào giỏ hàng, cập nhật số lượng, kiểm tra tồn kho.
- Phase 3: Payment (VNPay / MoMo giả lập) - Thanh toán giả lập. 
- Phase 4: Access Control (Authentication) - Đăng ký, đăng nhập, xác thực người dùng.

Trong đồ án này, nhóm triển khai **Unit Testing** và **CI** cho phần **WebFull** nhằm:
- Đảm bảo tính đúng đắn của business logic.
- Tự động kiểm tra khi có thay đổi source code.
- Phục vụ việc đánh giá quá trình phát triển.


---

## Unit Tests (PHPUnit)

Các unit test được viết bằng **PHPUnit**, tập trung vào các nghiệp vụ cốt lõi của website:

### 1. Cart Service (Giỏ hàng) - ()
Kiểm tra toàn bộ nghiệp xử lý giỏ hàng:
- Thêm sản phẩm vào giỏ
- Tăng / giảm số lượng
- Cập nhật số lượng bằng tay
- Xử lý số lượng âm hoặc bằng 0 (xóa sản phẩm)
- Không cho vượt quá tồn kho
Test file: webfull/tests/CartServiceTest.php

### 2. Pricing Service (Tính giá & tổng tiền)
Kiểm tra logic tính toán giá tiền:
- Tính giá sau khi áp dụng giảm giá (%)
- Tính tổng tiền giỏ hàng
- Kiểm tra số lượng sản phẩm hợp lệ khi tính tổng
Test file: webfull/tests/PricingServiceTest.php

### 3. Authentication
- Kiểm tra xác thực mật khẩu
- Hỗ trợ nhiều kiểu hash (bcrypt, md5, plaintext)
Test file: webfull/tests/PasswordVerifierTest.php

### 4. Catalog Filter (Bộ lọc danh sách sản phẩm)
Kiểm tra toàn bộ nghiệp vụ chuẩn hoá input filter từ query string trước khi query DB:
+ Chuẩn hoá phân trang: default page, clamp min, tính begin theo page size (ngầm xác nhận page size = 9)
+ Chuẩn hoá khoảng giá: default (0 → 15,000,000), clamp min/max, swap khi from > to, bật/tắt show_tag khi user có filter khác default
+ Chuẩn hoá sort giá: whitelist asc/desc (case-insensitive), reject input invalid/null để tránh sort sai hoặc injection
Test file: webfull/tests/CatalogFilterTest.php

### 5. Product Count Query Builder (Query đếm sản phẩm theo filter)
Kiểm tra logic build SQL + params cho câu query đếm sản phẩm theo filter:
- Default luôn có WHERE product_status = 1 và params rỗng
- Thêm điều kiện theo category / brand (đúng clause + đúng params)
- Ưu tiên category hơn brand khi cả hai cùng có
- Thêm điều kiện theo khoảng giá BETWEEN ? AND ?, tự swap from/to nếu nhập ngược
- Kết hợp nhiều điều kiện (vd: category + price) và kiểm tra thứ tự params khớp đúng vị trí dấu ?
Test file: webfull/tests/ProductCountQueryBuilderTest.php

## Tổng quan số lượng test
Hiện tại pipeline đang chạy:
- **26 tests**
- **64 assertions**

Chi tiết: 
- CartServiceTest.php 6 tests 
- PricingServiceTest.php 3 tests 
- PasswordVerifierTest.php 4 test 
- CatalogFilterTest.php 6 tests 
- ProductCountQueryBuilderTest.php 7 tests

---

## Chạy Unit Test local bằng Docker

### 1. Build & chạy các service
docker compose up -d --build db full-web

### 2. Cài đặt dependencies và autoload
docker compose exec -T full-web composer install
docker compose exec -T full-web composer dump-autoload

### 3. Chạy PHPUnit
docker compose exec -T full-web ./vendor/bin/phpunit

### Kết quả mong đợi:  OK (X tests, Y assertions)

## CI với GitHub Actions

Dự án sử dụng GitHub Actions để tự động kiểm tra khi có thay đổi source code.

Workflow file 1: .github/workflows/webfull-unit-test.yml 
- Tự động chạy PHPUnit
- Kiểm tra toàn bộ business logic của WebFull

Workflow file 2: .github/workflows/webfull-smoke-test.yml
- Tự động chạy PHPUnit
- Kiểm tra toàn bộ business logic của WebFull

Branch áp dụng: ci-webfull

## Quy trình CI

1. Khi push code lên branch ci-webfull

2. GitHub Actions tự động: 
- Cài Composer dependencies
- Chạy PHPUnit

3. Nếu tất cả test pass -> workflow hiển thị -> xanh

#### Ghi chú
- CI chỉ áp dụng cho phần WebFull
- Các Phase 1 / 2 / 3 vẫn được giữ nguyên, không bị ảnh hưởng
- Unit test tập trung vào business logic, không phụ thuộc giao diện

#### Công nghệ sử dụng

- PHP 8.2
- PHPUnit 10
- Docker & Docker Compose
- GitHub Actions