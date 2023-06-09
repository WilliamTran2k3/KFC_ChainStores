<?php
class PhieuNhapHangController extends BaseController
{
	public $model_pnh, $data = [];
	public function __construct()
	{
		$this->model_pnh = $this->model("PhieuNhapHangModel");
	}
	public function getTotal($maCuaHang)
	{
		echo $this->model_pnh->getTotal($maCuaHang);
	}
	public function getAll($maCuaHang)
	{
		echo $this->model_pnh->getAll($maCuaHang);
	}
	public function getMaMoiNhat($maCuaHang)
	{
		echo $this->model_pnh->getMaMoiNhat($maCuaHang);
	}
	public function insert($maNhaCungCap, $tongTien, $maCuaHang)
	{
		echo $this->model_pnh->insert($maNhaCungCap, $tongTien, $maCuaHang);
	}
	public function updateKho($maHang, $soluong)
	{
		echo $this->model_pnh->updateKho($maHang, $soluong);
	}
}
