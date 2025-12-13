1. Business Context
<img width="620" height="372" alt="Screenshot 2025-12-13 at 12 48 01" src="https://github.com/user-attachments/assets/401a15a8-45cd-40fe-9f3b-01debead149a" />


- Danh mục sản phẩm (Product Catalog): Khách hàng có thể duyệt qua danh sách các sản phẩm nước hoa với chức năng lọc theo loại (Nam/Nữ), thương hiệu, khoảng giá và sắp xếp theo giá (từ thấp đến cao hoặc ngược lại). Khi nhấn vào một sản phẩm trong danh sách, hệ thống hiển thị trang chi tiết bao gồm ảnh, tên, mô tả, giá gốc, giá sau giảm và phần trăm khuyến mãi. Người dùng có thể chọn số lượng mong muốn, sau đó “Thêm giỏ hàng” để tiếp tục mua sắm hoặc “Mua ngay” để tiến hành thanh toán. Quản trị viên có thể quản lý toàn bộ sản phẩm trên hệ thống, bao gồm thêm mới, cập nhật thông tin, xóa sản phẩm, cũng như phân loại theo thương hiệu.
- Giỏ hàng (Shopping Cart): Khách hàng có thể thêm sản phẩm thông qua nút “Thêm giỏ hàng” hoặc “Mua ngay” ở trang chi tiết sản phẩm. Giỏ hàng hiển thị danh sách sản phẩm đã chọn kèm thông tin tên, ảnh, dung tích, giá, số lượng, chi phí vận chuyển và tổng tiền thanh toán. Khi khách hàng thêm, xóa hoặc điều chỉnh số lượng sản phẩm, hệ thống tự động cập nhật giỏ hàng và tổng giá trị đơn. Nếu chưa đăng nhập, nút thao tác chính hiển thị “Đăng nhập đặt hàng” để đưa người dùng đến trang đăng nhập/đăng ký; nếu đã đăng nhập, nút hiển thị “Tiến hành đặt hàng” để sang bước thanh toán.
- Quy trình thanh toán (Payment Process): Khi khách hàng nhấn “Tiến hành đặt hàng” trong giỏ, hệ thống hiển thị trang thanh toán với thông tin người nhận (tên, địa chỉ, số điện thoại, ghi chú tùy chọn) cùng tóm tắt đơn hàng. Khách hàng có thể chọn phương thức thanh toán: COD (thanh toán khi nhận hàng), MoMo (QR Code hoặc chuyển khoản) hoặc VNPAY. Sau khi khách xác nhận, hệ thống lưu đơn hàng vào cơ sở dữ liệu và cập nhật trạng thái sang “Chờ xử lý” để quản trị viên theo dõi và tiến hành giao hàng.
- Kiểm soát truy cập (Access Control): Hệ thống cung cấp chức năng đăng ký, đăng nhập và đăng xuất. Người dùng mới có thể đăng ký bằng form gồm họ tên, địa chỉ, email, số điện thoại, mật khẩu và giới tính. Khi đăng nhập, khách hàng nhập email và mật khẩu; hệ thống hỗ trợ Remember Me và Quên mật khẩu. Sau khi đăng nhập thành công, nếu là khách hàng, hệ thống điều hướng đến trang tổng quan tài khoản, nơi hiển thị thông tin cá nhân, đơn hàng đang xử lý, lịch sử mua hàng, cài đặt tài khoản và tùy chọn đăng xuất. Nếu là quản trị viên, hệ thống điều hướng đến trang quản trị, nơi họ có thể thêm/sửa/xóa sản phẩm, quản lý thương hiệu và đơn hàng.

2. Conceptual Model  
<img width="592" height="459" alt="Screenshot 2025-12-13 at 12 44 39" src="https://github.com/user-attachments/assets/cf7c4ca5-99b7-48fc-b029-34ca2ff0e3b1" />

1. Product Catalog (Danh mục sản phẩm)
Chức năng: Quản lý và cung cấp thông tin chi tiết về nước hoa (tên, thương hiệu, giá cả, hình ảnh, dung tích).
Đầu vào (Cung cấp): Cung cấp giao diện để Web Browser hiển thị danh sách sản phẩm cho người dùng xem.
Đầu ra (Yêu cầu):
Kết nối xuống Database để truy xuất dữ liệu sản phẩm.
Quan trọng: Cung cấp một giao diện nội bộ cho Cart Component sử dụng (giúp Giỏ hàng lấy được giá chính xác).
2. Cart (Giỏ hàng)
Chức năng: Quản lý trạng thái mua sắm của khách hàng (lưu trữ danh sách hàng đã chọn, tính tổng tiền tạm tính, cập nhật số lượng).
Đầu vào (Cung cấp):
Cung cấp giao diện cho Web Browser để người dùng thêm/bớt sản phẩm.
Cung cấp giao diện cho Payment Component để truy xuất số tiền cần thanh toán.
Mối quan hệ: Có đường kết nối phụ thuộc (Dependency) từ Cart sang Product Catalog.
Ý nghĩa: Cart không được tự ý định giá. Khi thêm sản phẩm, nó phải gọi sang Product Catalog để xác thực xem sản phẩm đó còn không và giá hiện hành là bao nhiêu, đảm bảo tính toàn vẹn dữ liệu.
3. Payment (Thanh toán)
Chức năng: Xử lý các giao dịch tài chính, xác nhận thanh toán và tạo đơn hàng thành công.
Đầu vào (Cung cấp): Cung cấp giao diện cho Web Browser để người dùng nhập thông tin thanh toán.
Mối quan hệ: Có đường kết nối phụ thuộc từ Payment sang Cart.
Ý nghĩa: Payment cần biết chính xác "Cần thanh toán bao nhiêu tiền?". Thay vì tin tưởng con số từ Browser (có thể bị hack), nó gọi trực tiếp sang Cart để lấy tổng tiền cuối cùng và danh sách mặt hàng để xử lý giao dịch.
4. Access Control (Kiểm soát quyền truy cập)
Chức năng: Quản lý định danh (Identity), xác thực (Đăng nhập/Đăng ký) và phân quyền (Admin/User).
Hoạt động: Cung cấp giao diện bảo mật cho Web Browser. Dù đứng độc lập trong biểu đồ để phục vụ Client, nhưng về mặt logic, nó hoạt động như một "người gác cổng" bảo vệ các component khác (ví dụ: phải đăng nhập mới được gọi Payment).
Database (Tầng Dữ liệu - Data Layer)
Vai trò: Kho lưu trữ tập trung và vĩnh viễn của toàn bộ hệ thống.
Hoạt động: Cung cấp giao diện lưu trữ (Persistence Interface). Tất cả 4 component nghiệp vụ ở trên đều kết nối chụm vào đây.
Ý nghĩa: Mọi thao tác như thêm sản phẩm, lưu giỏ hàng, ghi lại lịch sử giao dịch hay kiểm tra mật khẩu đều phải thực hiện lệnh Đọc/Ghi (Read/Write) xuống Database này.
5. Tóm tắt luồng đi (Workflow) trên biểu đồ
Quy trình mua hàng sẽ diễn ra tuần tự qua các component như sau:
Duyệt hàng: Người dùng vào web => Web Browser gọi Product Catalog. Catalog lấy dữ liệu từ Database trả về để hiển thị.
Chọn mua: Người dùng chọn món => Web Browser gọi Cart. Cart liên hệ với Product Catalog để lấy thông tin giá => Lưu trạng thái giỏ vào Database.
Thanh toán: Người dùng chốt đơn => Web Browser gọi Payment. Payment liên hệ với Cart để lấy tổng tiền => Xử lý giao dịch => Lưu hóa đơn vào Database.
Access Control (Quản lý truy cập):
-         User (Người dùng): Mỗi người dùng có thể có một hoặc nhiều vai trò (Role). Mối quan hệ là 1-1.
-         Role (Vai trò): Mỗi vai trò thuộc về một người dùng.
Order And Payment (Đơn hàng và Thanh toán):
-         Order (Đơn hàng): Một người dùng có thể đặt nhiều đơn hàng (0-), và mỗi đơn hàng thuộc về một người dùng (1-1). Một đơn hàng bao gồm nhiều mục (OrderItem) (1-), và mỗi mục thuộc về một đơn hàng (1-0..1).
-         OrderItem (Mục đơn hàng): Mỗi mục đơn hàng tham chiếu đến một sản phẩm trong danh mục sản phẩm.
-         Payment (Thanh toán): Một đơn hàng có thể có nhiều phương thức thanh toán (0-*), và mỗi phương thức thanh toán thuộc về một đơn hàng (1-1).
-         PaymentMethod (Phương thức thanh toán): Mỗi phương thức thanh toán được sử dụng cho một thanh toán.
Shopping Cart (Giỏ hàng):
-         Cart (Giỏ hàng): Một người dùng có thể có nhiều giỏ hàng (0-), và mỗi giỏ hàng thuộc về một người dùng (1-0..1). Một giỏ hàng chứa nhiều mục (Cart Item) (1-).
-         Cart Item (Mục giỏ hàng): Mỗi mục giỏ hàng tham chiếu đến một sản phẩm và thuộc về một giỏ hàng.
Product Catalog (Danh mục sản phẩm):
-         Product (Sản phẩm): Một sản phẩm có thể thuộc về nhiều mục giỏ hàng hoặc đơn hàng (), và mỗi sản phẩm thuộc về một thương hiệu (Brand) (1-) và một danh mục (Category) (1-1).
-         Category (Danh mục): Mỗi danh mục có thể chứa nhiều sản phẩm (1-*).
-         Brand (Thương hiệu): Mỗi thương hiệu có thể liên kết với nhiều sản phẩm (1-*).
