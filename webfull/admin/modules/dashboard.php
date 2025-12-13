<div class="row">
    <div class="col">
        <div class="header__list d-flex space-between align-center">
            <h4 class="card-title" style="margin: 0;">Thống kê đơn hàng</h4>
            <div class="action_group">
                <a href="" id="btnExport" class="button button-dark">Export</a>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-lg-12 grid-margin stretch-card">
        <div class="card">
            <div class="card-body">
                <div class="main-pane-top d-flex space-between align-center" style="padding-inline: 10px;">
                    <div class="option-date d-flex space-between">
                        <select id="select-date" class="select-date-tk">
                            <option value="">Chọn thời gian</option>
                            <option value="7ngay">7 ngày qua</option>
                            <option value="28ngay">28 ngày qua</option>
                            <option value="90ngay">90 ngày qua</option>
                            <option value="365ngay">365 ngày qua</option>
                        </select>
                    </div>
                    <h4 class="card-title" style="margin: 0;">
                        Thống kê đơn hàng theo <span id="text-date"></span>
                    </h4>
                </div>

                <div class="metrics d-flex space-between">
                    <div class="metric__item">Doanh thu:
                        <span class="metric__sales"></span>
                    </div>
                    <div class="metric__item">Số đơn hàng:
                        <span class="metric__order"></span>
                    </div>
                    <div class="metric__item">Số lượng bán:
                        <span class="metric__quantity"></span>
                    </div>
                </div>

                <div id="linechart" style="height: 350px;" class="w-100"></div>
            </div>
        </div>
    </div>
</div>

<script>
    $(document).ready(function () {

        console.log('MORRIS DASHBOARD ĐANG CHẠY');

        // --- KHỞI TẠO CHART ---
        var char = new Morris.Line({
            element: 'linechart',
            xkey: 'date',
            ykeys: ['order', 'sales', 'quantity'],
            labels: ['Đơn hàng', 'Doanh thu', 'Số lượng'],
            xLabels: 'day',
            hideHover: 'auto',
            parseTime: true,
            hoverCallback: function (index, options, content, row) {
                console.log('HOVER CALLBACK RUN', row);

                var salesFormatted = Number(row.sales).toLocaleString('vi-VN');

                return ''
                    + '<div style="font-size:12px;">'
                    +   '<div><strong>' + row.date + '</strong></div>'
                    +   '<div>Đơn hàng: ' + row.order + '</div>'
                    +   '<div>Doanh thu: ' + salesFormatted + '</div>'
                    +   '<div>Số lượng: ' + row.quantity + '</div>'
                    + '</div>';
            }
        });

        // Load mặc định 365 ngày
        thongke();

        // Khi chọn thời gian
        $('#select-date').change(function () {
            var thoigian = $(this).val();
            var text = '';

            if (thoigian === '7ngay')       text = '7 ngày qua';
            else if (thoigian === '28ngay') text = '28 ngày qua';
            else if (thoigian === '90ngay') text = '90 ngày qua';
            else                            text = '365 ngày qua';

            $('#text-date').text(text);

            $.ajax({
                url: "modules/thongke.php",
                method: "POST",
                dataType: "JSON",
                data: { thoigian: thoigian },
                success: function (data) {
                    char.setData(data);

                    // Tính tổng metric
                    var totalOrder    = 0;
                    var totalSales    = 0;
                    var totalQuantity = 0;

                    for (var i = 0; i < data.length; i++) {
                        totalOrder    += parseInt(data[i].order);
                        totalSales    += parseInt(data[i].sales);
                        totalQuantity += parseInt(data[i].quantity);
                    }

                    var formattedAmount = totalSales.toLocaleString('vi-VN', {
                        style: 'currency',
                        currency: 'VND'
                    });

                    $('.metric__order').text(totalOrder);
                    $('.metric__quantity').text(totalQuantity);
                    $('.metric__sales').text(formattedAmount);
                }
            });
        });

        // Hàm load mặc định (365 ngày)
        function thongke() {
            var text = '365 ngày qua';
            $.ajax({
                url: "modules/thongke.php",
                method: "POST",
                dataType: "JSON",
                success: function (data) {
                    char.setData(data);
                    $('#text-date').text(text);

                    var totalOrder    = 0;
                    var totalSales    = 0;
                    var totalQuantity = 0;

                    for (var i = 0; i < data.length; i++) {
                        totalOrder    += parseInt(data[i].order);
                        totalSales    += parseInt(data[i].sales);
                        totalQuantity += parseInt(data[i].quantity);
                    }

                    var formattedAmount = totalSales.toLocaleString('vi-VN', {
                        style: 'currency',
                        currency: 'VND'
                    });

                    $('.metric__order').text(totalOrder);
                    $('.metric__quantity').text(totalQuantity);
                    $('.metric__sales').text(formattedAmount);
                }
            });
        }
    });
</script>
