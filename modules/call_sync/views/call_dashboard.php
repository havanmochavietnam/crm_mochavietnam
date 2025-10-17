<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>

<div id="wrapper">
  <div class="content">
    <div class="row">
      <div class="col-md-12">
        <div class="panel_s">
          <div class="panel-body">
            
            <div class="tw-flex tw-justify-between tw-items-center tw-mb-4">
              <h4><i class="fa fa-bar-chart text-primary"></i> Biểu đồ tổng quan cuộc gọi</h4>
            </div>
            <hr>

            <div class="row">
                <div class="col-md-4">
                    <div class="card-modern">
                        <div class="card-modern-body chart-card">
                            <h5 class="chart-title">Trạng thái cuộc gọi</h5>
                            <div id="statusChart"></div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card-modern">
                        <div class="card-modern-body chart-card">
                            <h5 class="chart-title">Tỷ lệ gọi vào / gọi ra</h5>
                            <div id="directionChart"></div>
                        </div>
                    </div>
                </div>
                <div class="col-md-12">
                    <div class="card-modern" style="margin-top: 20px;">
                        <div class="card-modern-body chart-card">
                            <h5 class="chart-title">Top nhân viên có nhiều cuộc gọi nhất</h5>
                            <div id="agentChart"></div>
                        </div>
                    </div>
                </div>
            </div>
            </div>
        </div>
      </div>
    </div>
  </div>
</div>

<?php init_tail(); ?>

<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>

<style>
/* CSS để các card và biểu đồ trông đẹp hơn */
.card-modern {
    border: 1px solid #e3e6f0;
    border-radius: 8px;
    box-shadow: 0 2px 6px rgba(0,0,0,0.05);
    background-color: #fff;
    margin-bottom: 20px;
}
.card-modern-body {
    padding: 25px;
}
.chart-title {
    margin-bottom: 15px;
    font-size: 16px;
    font-weight: 600;
    color: #5a5c69;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
  
  // 1. Biểu đồ Trạng thái (Donut Chart)
  var statusChartOptions = {
    series: [185, 95, 40, 15], // Dữ liệu cứng: Thành công, Không trả lời, Máy bận, Thất bại
    labels: ['Thành công', 'Không trả lời', 'Máy bận', 'Thất bại'],
    chart: {
      type: 'donut',
      height: 300
    },
    colors: ['#28a745', '#ffc107', '#17a2b8', '#dc3545'],
    legend: {
      position: 'bottom'
    },
    responsive: [{
      breakpoint: 480,
      options: {
        chart: {
          width: 200
        },
        legend: {
          position: 'bottom'
        }
      }
    }]
  };
  var statusChart = new ApexCharts(document.querySelector("#statusChart"), statusChartOptions);
  statusChart.render();

  // 2. Biểu đồ Hướng gọi (Pie Chart)
  var directionChartOptions = {
    series: [250, 85], // Dữ liệu cứng: Gọi ra, Gọi vào
    labels: ['Gọi ra', 'Gọi vào'],
    chart: {
      type: 'pie',
      height: 300
    },
    colors: ['#007bff', '#fd7e14'],
    legend: {
      position: 'bottom'
    }
  };
  var directionChart = new ApexCharts(document.querySelector("#directionChart"), directionChartOptions);
  directionChart.render();

  // 3. Biểu đồ Top nhân viên (Horizontal Bar Chart)
  var agentChartOptions = {
    series: [{
      name: 'Số cuộc gọi',
      data: [78, 65, 52, 48, 45, 35, 33, 29, 25, 21] // Dữ liệu cứng
    }],
    chart: {
      type: 'bar',
      height: 350
    },
    plotOptions: {
      bar: {
        horizontal: true,
        barHeight: '70%',
        distributed: true, // Mỗi cột một màu
      }
    },
    dataLabels: {
      enabled: true,
      textAnchor: 'start',
      style: {
        colors: ['#fff']
      },
      formatter: function (val, opt) {
        return opt.w.globals.labels[opt.dataPointIndex] + ":  " + val
      },
      offsetX: 0,
    },
    xaxis: {
      categories: [ // Tên nhân viên
        'Nguyễn Thị A', 'Trần Văn B', 'Lê Thị C', 'Phạm Văn D', 'Hoàng Thị E', 
        'Vũ Văn F', 'Đặng Thị G', 'Bùi Văn H', 'Hồ Thị I', 'Ngô Văn K'
      ],
      labels: {
        show: false // Ẩn nhãn trục X vì đã hiển thị trên thanh
      }
    },
    yaxis: {
        labels: {
            show: false // Ẩn nhãn trục Y
        }
    },
    legend: {
        show: false // Ẩn chú thích vì mỗi cột đã có màu riêng
    },
    tooltip: {
      y: {
        formatter: function (val) {
          return val + " cuộc gọi"
        }
      }
    }
  };
  var agentChart = new ApexCharts(document.querySelector("#agentChart"), agentChartOptions);
  agentChart.render();

});
</script>
</body>
</html>