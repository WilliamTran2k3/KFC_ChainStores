<div class="table-responsive table-scroll" data-mdb-perfect-scrollbar="true" style="position: relative; background-color: #fff">
	<table class="table table-hover mb-0 table-order-list-online">
		<thead style="background-color: #8470FF;">
			<tr>
				<th scope="col">STT</th>
				<th scope="col">Mã đơn</th>
				<th scope="col">Số điện thoại khách hàng</th>
				<th scope="col">Ngày lập</th>
				<th scope="col">Tổng tiền</th>
			</tr>
		</thead>
		<tbody class="tbody-order-online">

		</tbody>
	</table>
</div>
<div class="modal fade" id="modalOrderList">
	<div class="modal-dialog">
		<div class="modal-content">

			<!-- Modal Header -->
			<div class="modal-header">
				<h4 class="modal-title">Chi tiết đơn order</h4>
				<button type="button" class="btn-close" data-bs-dismiss="modal"></button>
			</div>

			<!-- Modal body -->
			<div class="modal-body">
				<table class="table table-striped">
					<thead>
						<tr>
							<th scope="col">STT</th>
							<th scope="col">Tên món ăn</th>
							<th scope="col">Số lượng</th>
							<th scope="col">Giá</th>
						</tr>
					</thead>
					<tbody class="tbody-order">

					</tbody>
				</table>
			</div>

			<!-- Modal footer -->
			<div class="modal-footer">
				<button type="button" class="btn btn-success btn-xacnhandonhang">Xác nhận đơn hàng</button>
				<button type="button" class="btn btn-danger btn-huydonhang">Hủy đơn hàng</button>
			</div>

		</div>
	</div>
</div>

<script>
	$(function() {
		let maDonHangClick = null;

		function fetchGetAllDonHangOnline() {
			let tbody = $(".tbody-order-online");
			tbody.children().remove();
			fetch(`<?php echo _WEB_ROOT ?>/donHang/getAllDonHangOnline/<?php echo json_decode($_SESSION['user'])->maCuaHang ?>`)
				.then(res => res.json())
				.then(data => {
					let tr = "";
					let i = 1;
					data.forEach(element => {
						tr += `<tr class="detail" style='cursor: pointer;'>
					<td>${i++}</td>
					<td>${element.maDonHang}</td>
					<td>${element.sdtKhachHang}</td>
					<td>${element.ngayLap}</td>
					<td>${element.tongTien}</td>
				</tr>`
					});
					tbody.append(tr);
				});
		}
		fetchGetAllDonHangOnline();
		$(".tbody-order-online").on("click", ".detail", function() {
			let modal = $("#modalOrderList");
			let madonhang = $($(this).find("td")[1]).text();
			maDonHangClick = madonhang;
			$(".tbody-order").children().remove();
			fetch(`<?php echo _WEB_ROOT ?>/chiTietDonHang/detail/${madonhang}`)
				.then(res => res.json())
				.then(data => {
					let tr = "";
					let i = 1;
					data.forEach(element => {
						tr += `<tr>
						<td>${i++}</td>
						<td>${element.tenMonAn}</td>
						<td>${element.soLuong}</td>
						<td>${element.gia}</td>
					</tr>`
					});
					$(".tbody-order").append(tr);
				})
			modal.modal("show");

		})
		$(".btn-xacnhandonhang").click(() => {
				let trangthai = "Đang xử lý";
				fetch(`<?php echo _WEB_ROOT ?>/donHang/updateTrangThaiDonHang/${maDonHangClick}/${trangthai}`)
					.then(res => res.text())
					.then(data => {
						if (data == 1) {
							alert("Đã xác nhận đơn hàng");
							fetchGetAllDonHangOnline();
							$("#modalOrderList").modal("hide");
						} else {
							alert("Đã có gì đó xảy ra");
						}
					})
					.catch(err => alert(err))
			})
			.$(".btn-huydonhang").click(() => {
				let trangthai = "Đã hủy";
				fetch(`<?php echo _WEB_ROOT ?>/donHang/updateTrangThaiDonHang/${maDonHangClick}/${trangthai}`)
					.then(res => res.text())
					.then(data => {
						if (data == 1) {
							alert("Đã hủy đơn hàng");
							fetchGetAllDonHangOnline();
							$("#modalOrderList").modal("hide");
						} else {
							alert("Đã có gì đó xảy ra");
						}
					})
					.catch(err => alert(err))
			})
	})
</script>