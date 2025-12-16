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

3. Use Case
<img width="571" height="610" alt="Screenshot 2025-12-14 082553" src="https://github.com/user-attachments/assets/d61a9aba-fe2f-4caa-a084-f2c4e2bf4f71" />

Sơ đồ trên là Use Case Diagram mô tả các chức năng chính của website nước hoa và mối quan hệ giữa chúng, tập trung vào 2 nhóm người dùng: khách hàng (người mua) và quản trị viên (admin). Các use case được liên kết để thể hiện luồng sử dụng cơ bản của hệ thống, đặc biệt là các chức năng mua hàng và thanh toán với nhiều phương thức.
Sơ đồ Use Case thể hiện các chức năng dành cho khách hàng (người mua) trong quá trình sử dụng website. Nhóm chức năng về tài khoản bao gồm Đăng ký, Đăng nhập, Quên mật khẩu, Đăng xuất và Cập nhật thông tin tài khoản, cho phép người dùng tạo và quản lý hồ sơ cá nhân. Bên cạnh đó, hệ thống hỗ trợ duyệt và lựa chọn sản phẩm thông qua các use case như Xem danh sách sản phẩm, Lọc và sắp xếp sản phẩm, Xem chi tiết sản phẩm, từ đó khách hàng có thể Thêm vào giỏ hàng và Xem/Cập nhật giỏ hàng để điều chỉnh số lượng hoặc loại bỏ sản phẩm không còn nhu cầu. Ngoài ra, chức năng Lịch sử mua hàng giúp khách theo dõi các đơn đã đặt trước đó.
Về mua hàng và quản trị hệ thống, use case Xử lý thanh toán đóng vai trò trung tâm và có quan hệ <<include>> với các phương thức thanh toán như COD, MoMo và VNPay, cho phép hệ thống xử lý linh hoạt theo lựa chọn của khách hàng. Ở phía quản trị viên (admin), hệ thống cung cấp các chức năng quản lý nội dung và vận hành, trong đó Quản lý sản phẩm bao gồm Thêm, Cập nhật và Xóa sản phẩm, còn Quản lý đơn hàng cho phép Cập nhật trạng thái đơn hàng trong suốt vòng đời xử lý. Cách phân tách này giúp đảm bảo khách hàng tập trung vào trải nghiệm mua sắm, trong khi admin kiểm soát dữ liệu và hoạt động của toàn bộ hệ thống.
1.3.2 Product Catalog
<img width="542" height="491" alt="Screenshot 2025-12-14 082548" src="https://github.com/user-attachments/assets/61a01344-5875-4af0-9522-bc143487d996" />

Sơ đồ Use Case Product Catalog mô tả tổng quan các chức năng liên quan đến việc hiển thị và quản lý danh mục sản phẩm trong hệ thống website nước hoa. Sơ đồ thể hiện hai nhóm tác nhân chính là người mua và quản trị viên, tương tác với các chức năng khác nhau nhằm phục vụ mục tiêu duyệt sản phẩm và quản lý dữ liệu sản phẩm một cách hiệu quả.
Về phía người mua, hệ thống cho phép Xem danh sách sản phẩm như điểm khởi đầu của quá trình mua sắm. Từ danh sách này, người dùng có thể Lọc sản phẩm theo các tiêu chí nhất định và Sắp xếp sản phẩm (phân loại sản phẩm) để dễ dàng tìm kiếm sản phẩm phù hợp. Khi quan tâm đến một sản phẩm cụ thể, người mua có thể Xem chi tiết sản phẩm để nắm đầy đủ thông tin trước khi ra quyết định mua. Các chức năng này hỗ trợ tối đa trải nghiệm duyệt và lựa chọn sản phẩm một cách trực quan và thuận tiện.
Ở phía quản trị viên, use case Quản lý sản phẩm đóng vai trò trung tâm và bao gồm các thao tác Thêm sản phẩm, Cập nhật sản phẩm và Xóa sản phẩm. Nhóm chức năng này cho phép admin kiểm soát toàn bộ danh mục sản phẩm trên hệ thống, đảm bảo thông tin sản phẩm luôn được cập nhật chính xác và đầy đủ. Sự phân tách rõ ràng giữa chức năng của người mua và quản trị viên giúp hệ thống vừa đáp ứng tốt nhu cầu mua sắm của khách hàng, vừa đảm bảo khả năng quản lý và vận hành hiệu quả từ phía quản trị.
1.3.3 Shopping Cart
<img width="534" height="404" alt="Screenshot 2025-12-14 082542" src="https://github.com/user-attachments/assets/3819e718-1b03-4563-a160-d8323e7cfb8d" />

Sơ đồ Use Case Shopping Cart mô tả tổng quan các chức năng liên quan đến quá trình quản lý giỏ hàng của khách hàng trong hệ thống. Sơ đồ tập trung thể hiện các thao tác chính mà người dùng thực hiện từ khi thêm sản phẩm vào giỏ cho đến bước thanh toán, đồng thời phản ánh sự phối hợp giữa giỏ hàng và hệ thống quản lý tồn kho.
Về chi tiết, khách hàng có thể Thêm sản phẩm vào giỏ hàng sau khi lựa chọn sản phẩm mong muốn. Tại giỏ hàng, người dùng được phép Xem giỏ hàng để kiểm tra danh sách các sản phẩm đã chọn, đồng thời có thể Cập nhật số lượng sản phẩm trong giỏ hoặc Xóa sản phẩm khỏi giỏ hàng khi không còn nhu cầu. Khi hoàn tất việc kiểm tra và điều chỉnh, khách hàng thực hiện Thanh toán giỏ hàng để chuyển sang bước xử lý đơn hàng. Song song với đó, hệ thống thực hiện Cập nhật sản phẩm với kho bằng cách giảm số lượng tồn kho tương ứng sau khi đơn hàng được xác nhận, đảm bảo dữ liệu tồn kho luôn chính xác và nhất quán với các giao dịch mua sắm.

1.3.4 Payment Process
<img width="539" height="409" alt="Screenshot 2025-12-14 082535" src="https://github.com/user-attachments/assets/319923d3-85a8-492e-a0ae-39030416c09c" />

Sơ đồ Use Case Payment Process mô tả tổng quan các chức năng liên quan đến quá trình xử lý thanh toán trong hệ thống website nước hoa. Sơ đồ tập trung vào use case trung tâm Xử lý thanh toán, từ đó mở rộng ra các bước xác thực người dùng và các phương thức thanh toán khác nhau, thể hiện cách hệ thống hỗ trợ thanh toán linh hoạt và có kiểm soát.
Về chi tiết, khi thực hiện Xử lý thanh toán, hệ thống luôn bao gồm (<<include>>) bước Xác thực người dùng nhằm đảm bảo người mua đã đăng nhập hợp lệ trước khi giao dịch được tiến hành, đồng thời bao gồm bước Tiến hành thanh toán để xử lý đơn hàng. Từ use case này, hệ thống mở rộng (<<extend>>) sang các phương thức thanh toán cụ thể gồm Thanh toán khi nhận hàng (COD), Pay with MoMo và Pay with VNPay, tùy theo lựa chọn của khách hàng. Với phương thức MoMo, hệ thống hiển thị QR Code để khách quét và thanh toán, trong khi với VNPay, khách cần nhập thông tin thẻ để hoàn tất giao dịch. Cách tổ chức use case theo quan hệ include và extend giúp hệ thống tách biệt rõ phần xử lý chung và các nhánh thanh toán riêng biệt, đảm bảo dễ mở rộng và quản lý trong tương lai.

1.3.5 Access Control
<img width="442" height="457" alt="Screenshot 2025-12-14 082459" src="https://github.com/user-attachments/assets/86063913-866f-4753-83bd-c1ea473f2b47" />

Sơ đồ Use Case Access Control mô tả tổng quan các chức năng liên quan đến quản lý tài khoản và kiểm soát quyền truy cập trong hệ thống website nước hoa. Sơ đồ thể hiện hai tác nhân chính là Người mua và Admin, tương tác với các chức năng xác thực, quản lý thông tin cá nhân và phân quyền người dùng nhằm đảm bảo an toàn và kiểm soát truy cập hệ thống.
Về chi tiết, người mua có thể thực hiện các chức năng cơ bản như Đăng ký, Đăng nhập và Đăng xuất để sử dụng hệ thống. Sau khi đăng nhập, người dùng có thể Chỉnh sửa thông tin cá nhân và Đổi mật khẩu thông qua các use case mở rộng (<<extend>>) từ chức năng Đăng nhập. Ở phía Admin, use case trung tâm Quản lý tài khoản bao gồm (<<include>>) các chức năng Xem danh sách tài khoản, Chỉnh sửa thông tin tài khoản, Phân vai trò người dùng và Khóa/Mở khóa tài khoản. Việc phân tách rõ ràng quyền hạn giữa người mua và admin giúp hệ thống đảm bảo tính bảo mật, đồng thời cho phép quản trị viên kiểm soát hiệu quả người dùng và hoạt động truy cập trên toàn bộ hệ thống.

5. User Stories

Product Catalog 

Là một Người mua, tôi muốn truy cập vào trang Danh mục sản phẩm, để tôi có thể xem danh sách các sản phẩm với tên, giá, loại và hình ảnh.
Là một Người mua, tôi muốn lọc sản phẩm theo tên hoặc khoảng giá, để tôi có thể nhanh chóng tìm được sản phẩm phù hợp với nhu cầu.
Là một Người mua, tôi muốn nhấp vào một sản phẩm cụ thể, để tôi có thể xem chi tiết sản phẩm bao gồm tên và mô tả chi tiết.
Là một Quản trị viên, tôi muốn truy cập vào giao diện quản lý sản phẩm, để tôi có thể tạo mới, xem thông tin chi tiết, cập nhật hoặc xóa sản phẩm.
Bất cứ khi nào tôi thực hiện thao tác quản lý, thì hệ thống sẽ xử lý yêu cầu, cập nhật cơ sở dữ liệu và hiển thị danh mục sản phẩm mới nhất.
Shopping Cart 

Là một Người mua, tôi có thể nhấn nút “Thêm vào giỏ hàng” hoặc “Mua ngay”, để tôi có thể lưu sản phẩm mình chọn vào giỏ hàng.
Bất cứ khi nào sản phẩm đã hết hàng, thì hệ thống sẽ hiển thị thông báo “Sản phẩm hiện đã hết hàng, vui lòng chọn sản phẩm khác” và không thêm vào giỏ.
Là một Người mua, tôi muốn truy cập vào Giỏ hàng, để tôi có thể xem danh sách sản phẩm đã chọn, bao gồm: tên, giá, số lượng, hình ảnh, và trạng thái còn hàng.
Là một Người mua, tôi muốn thấy bảng tóm tắt giỏ hàng, để tôi có thể biết tổng chi phí sản phẩm, chi phí vận chuyển và tổng số tiền thanh toán.
Là một Người mua, tôi muốn thay đổi số lượng sản phẩm trong giỏ, để hệ thống tự động tính lại tổng chi phí và tổng tiền thanh toán.
Là một Người mua, tôi muốn xóa sản phẩm khỏi giỏ.
Là một Người mua, tôi muốn nhấn nút “Thanh toán” trong giỏ hàng, để tôi có thể bắt đầu quy trình Thanh toán đơn hàng.
Payment Process

Là một người mua, sau khi tôi chọn được các sản phẩm cần thiết và cho vào giỏ hàng, tôi muốn có thể thực hiện thanh toán trực tuyến để hoàn tất quá trình mua sắm. Sau đó, tôi bấm nút “Thanh toán” để tiếp tục.
Ở bước này, tôi sẽ được lựa chọn phương thức thanh toán. Hệ thống hỗ trợ ba cách khác nhau:
-         Thanh toán khi nhận hàng (COD) – tôi chỉ cần xác nhận đơn hàng và thanh toán trực tiếp khi nhận hàng.
-         Thanh toán bằng MoMo – hệ thống hiển thị mã QR để tôi quét bằng ứng dụng MoMo.
-         Thanh toán bằng VNPay – hệ thống hiển thị form để tôi nhập thông tin thẻ ATM hoặc thẻ tín dụng.
Sau khi tôi chọn phương thức phù hợp và bấm “Xác nhận thanh toán”, hệ thống sẽ tiến hành kiểm tra tính hợp lệ của đơn hàng.
Access Control

Khách hàng:
Là một Người mua, tôi muốn đăng ký tài khoản bằng email và mật khẩu, để tôi có thể tạo tài khoản mới.
Là một Người mua, tôi muốn đăng nhập bằng email và mật khẩu, để tôi có thể truy cập vào trang chủ cửa hàng và mua sắm.
Tôi muốn chỉnh sửa thông tin cá nhân (ví dụ: họ tên, số điện thoại, địa chỉ), để cập nhật thông tin mới nhất của tôi.
Tôi muốn đổi mật khẩu cũ thành mật khẩu mới.
Là một Người mua, tôi muốn đăng xuất khỏi hệ thống, để bảo mật thông tin tài khoản sau khi sử dụng.

Quản trị viên:
Là một Quản trị viên, tôi muốn đăng nhập bằng email và mật khẩu để tôi có thể truy cập vào bảng điều khiển quản trị (Admin Dashboard).
Là một Quản trị viên, tôi muốn đăng xuất khỏi hệ thống quản trị
Là một quản trị viên tôi có thể quản lý tài khoản người dùng:
Tôi muốn phân vai trò cho người dùng (Người mua hoặc Quản trị viên hoặc nhân viên), để đảm bảo quyền truy cập được cấp đúng theo chức năng.
Tôi muốn xem danh sách tài khoản người dùng.
Tôi có thể khóa hoặc mở khóa tài khoản người dùng, để xử lý các trường hợp vi phạm hoặc kích hoạt lại tài khoản.
Tôi có thể chỉnh sửa thông tin tài khoản của người dùng (ví dụ cập nhật thông tin khi cần).

6. ERD Diagram
 <img width="714" height="596" alt="Screenshot 2025-12-15 080654" src="https://github.com/user-attachments/assets/261fd520-1f2b-40fa-8679-a3ac22c655c4" />

Hệ thống cơ sở dữ liệu được thiết kế chặt chẽ xoay quanh thực thể trung tâm là User, nơi lưu trữ thông tin định danh và được phân quyền thông qua Role dưới sự quản lý của Admin. Dữ liệu hàng hóa được tổ chức khoa học trong thực thể Product, phân loại chi tiết qua mối quan hệ với Category và Brand để tối ưu hóa việc tìm kiếm. Quy trình mua sắm bắt đầu khi người dùng thêm sản phẩm vào Cart (chứa các CartItem lưu trữ tạm thời) và hoàn tất khi chốt đơn hàng sang thực thể Order (đi kèm OrderItem để lưu trữ lịch sử mua hàng cố định). Cuối cùng, quy trình được khép kín bằng thực thể Payment có liên kết 1-1 với đơn hàng, chịu trách nhiệm ghi nhận trạng thái giao dịch và hình thức thanh toán cụ thể qua PaymentMethod, đảm bảo tính toàn vẹn cho toàn bộ luồng nghiệp vụ bán hàng.

7. Sơ đồ khối tổng quan
<img width="668" height="237" alt="Screenshot 2025-12-16 at 10 08 29" src="https://github.com/user-attachments/assets/c5277a40-a475-4d59-9ba5-125709d5a90e" />

Hình trên mô tả sơ đồ khối tổng quan của hệ thống web bán hàng, gồm bốn thành phần chính: Client, Frontend, Backend và Database. Người dùng (Client) gửi yêu cầu đến hệ thống thông qua Load Balancer để phân phối tải giữa các máy chủ. 
Frontend chịu trách nhiệm hiển thị giao diện người dùng (UI) bằng PHP, JavaScript và CSS, cho phép người dùng thao tác như xem sản phẩm, tìm kiếm, hoặc thêm vào giỏ hàng. 
Backend xử lý các nghiệp vụ chính gồm ba mô-đun: Product (hiển thị và quản lý sản phẩm), Cart (xử lý giỏ hàng), và Order (xử lý đơn hàng theo kiến trúc rõ ràng). Mỗi mô-đun gồm phần giao diện, logic nghiệp vụ và truy cập dữ liệu thông qua PHP. 
Toàn bộ dữ liệu về sản phẩm và đơn hàng được lưu trữ trong Cơ sở dữ liệu MySQL. Hệ thống hoạt động theo luồng: Client → Load Balancer → Frontend → Backend → Database, đảm bảo hiệu năng cao, tách biệt rõ ràng giữa các tầng và dễ mở rộng.

8. Sơ đồ triển khai CI/CD (Deployment View)
<img width="623" height="320" alt="Screenshot 2025-12-16 at 10 07 50" src="https://github.com/user-attachments/assets/62c53cf5-b985-4819-93e1-3adcd8507336" />

Hình sơ đồ triển khai CI/CD Deployment View trên mô tả quy trình DevOps triển khai ứng dụng Node.js gồm ba giai đoạn chính: Development, Build và Shift. Ở giai đoạn Development, lập trình viên sử dụng VS Code để viết mã Node.js và tạo Dockerfile, sau đó đẩy toàn bộ mã nguồn lên GitHub để lưu trữ và quản lý phiên bản. 
Tiếp theo, trong giai đoạn Build, GitHub Actions tự động kích hoạt quy trình CI/CD, dùng Dockerfile để build Docker image chứa ứng dụng và các cấu hình cần thiết. 
Cuối cùng, ở giai đoạn Shift, image này được triển khai lên Kubernetes, nơi ứng dụng được vận hành trong các container, hỗ trợ mở rộng linh hoạt, tự động hóa và đảm bảo tính ổn định cao. Quy trình này giúp kết nối chặt chẽ giữa phát triển và vận hành, giảm lỗi thủ công và tăng tốc độ triển khai phần mềm.
