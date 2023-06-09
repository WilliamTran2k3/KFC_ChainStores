package com.example.kfc_chainstores_mobile;

import androidx.annotation.NonNull;
import androidx.appcompat.app.AppCompatActivity;
import androidx.recyclerview.widget.GridLayoutManager;
import androidx.recyclerview.widget.RecyclerView;

import android.annotation.SuppressLint;
import android.app.Dialog;
import android.content.Intent;
import android.database.Cursor;
import android.graphics.Color;
import android.graphics.drawable.ColorDrawable;
import android.os.Bundle;
import android.util.Log;
import android.view.Gravity;
import android.view.Menu;
import android.view.MenuItem;
import android.view.View;
import android.view.Window;
import android.view.WindowManager;
import android.widget.Button;
import android.widget.ImageView;
import android.widget.TextView;

import com.example.kfc_chainstores_mobile.adapters.ListMonAnAdapter;
import com.example.kfc_chainstores_mobile.database.GioHangHelper;
import com.example.kfc_chainstores_mobile.model.LoaiMon;
import com.example.kfc_chainstores_mobile.model.MonAn;
import com.google.android.material.tabs.TabLayout;
import com.squareup.picasso.Picasso;

import org.jetbrains.annotations.NotNull;
import org.json.JSONArray;
import org.json.JSONException;
import org.json.JSONObject;

import java.io.IOException;
import java.text.DecimalFormat;
import java.util.ArrayList;
import java.util.List;

import okhttp3.Call;
import okhttp3.Callback;
import okhttp3.OkHttpClient;
import okhttp3.Request;
import okhttp3.Response;

public class ListMonAnActivity extends AppCompatActivity {

    TabLayout tabLayout;
    List<LoaiMon> loaiMonList;

    RecyclerView rcv_monAn;
    ListMonAnAdapter listMonAnAdapter;
    List<MonAn> monAnList;

    GioHangHelper gioHangHelper;

    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_list_mon_an);

        getSupportActionBar().setTitle("Chọn món");

        gioHangHelper = new GioHangHelper(this, null);
        gioHangHelper.clear();

        tabLayout = findViewById(R.id.tab_loaiMon);
        loaiMonList = new ArrayList<>();
        getLoaiMonAn();

        rcv_monAn = findViewById(R.id.rcv_mon);

        monAnList = new ArrayList<>();
        listMonAnAdapter = new ListMonAnAdapter(this, monAnList, new ListMonAnAdapter.MonAnClickListener() {
            @Override
            public void onMonAnClick(MonAn monAn) {
                openDialog(monAn);
            }
        });
        GridLayoutManager gridLayoutManager = new GridLayoutManager(this, 2);
        rcv_monAn.setLayoutManager(gridLayoutManager);

        rcv_monAn.setAdapter(listMonAnAdapter);
        getListMonAn(1);

        tabLayout.setOnTabSelectedListener(new TabLayout.OnTabSelectedListener() {
            @Override
            public void onTabSelected(TabLayout.Tab tab) {
                int position = tab.getPosition();
                getListMonAn(loaiMonList.get(position).getId_loaimon());
            }

            @Override
            public void onTabUnselected(TabLayout.Tab tab) {

            }

            @Override
            public void onTabReselected(TabLayout.Tab tab) {

            }
        });
    }

    @Override
    protected void onDestroy() {
        super.onDestroy();
        gioHangHelper.close();
    }

    public void openDialog(MonAn monAn) {
        final Dialog dialog = new Dialog(this);
        dialog.requestWindowFeature(Window.FEATURE_NO_TITLE);
        dialog.setContentView(R.layout.mon_an_selected_dialog);

        Window window = dialog.getWindow();
        if (window == null) {
            return;
        }

        window.setLayout(WindowManager.LayoutParams.MATCH_PARENT, WindowManager.LayoutParams.WRAP_CONTENT);
        window.setBackgroundDrawable(new ColorDrawable(Color.TRANSPARENT));

        WindowManager.LayoutParams windowAttributes = window.getAttributes();
        windowAttributes.gravity = Gravity.BOTTOM;
        window.setAttributes(windowAttributes);
        dialog.setCancelable(true);

        ImageView anh = dialog.findViewById(R.id.iv_monAn);
        TextView tv_tenMonAn = dialog.findViewById(R.id.tv_tenMonAn_selected);
        TextView tv_gia = dialog.findViewById(R.id.tv_gia_selected);
        TextView tv_soluong = dialog.findViewById(R.id.tv_soluong);
        Button close = dialog.findViewById(R.id.btn_closeDialog);
        Button plus = dialog.findViewById(R.id.btn_plus);
        Button minus = dialog.findViewById(R.id.btn_minus);
        Button addCart = dialog.findViewById(R.id.btn_addCart);
        Button buyNow = dialog.findViewById(R.id.btn_buyNow);

        String imageUrl = "http://10.0.2.2:8080/KFC_ChainStores/public/assets/client/img/"+monAn.getImage_path();
        Picasso.get().load(imageUrl).into(anh);

        tv_tenMonAn.setText(monAn.getTenMonAn());

        double number = monAn.getGia();
        DecimalFormat formatter = new DecimalFormat("#,### đ");
        String formattedNumber = formatter.format(number);
        tv_gia.setText(formattedNumber);

        if (monAn.getSoLuongDat() == 0) {
            tv_soluong.setText("1");
            minus.setEnabled(false);
        }
        else {
            tv_soluong.setText(String.valueOf(monAn.getSoLuongDat()));
        }

        close.setOnClickListener(new View.OnClickListener() {
            @Override
            public void onClick(View view) {
                dialog.dismiss();
            }
        });

        plus.setOnClickListener(new View.OnClickListener() {
            @Override
            public void onClick(View view) {
                int sl = Integer.parseInt(tv_soluong.getText().toString()) + 1;
                tv_soluong.setText(String.valueOf(sl));
                if (sl > 1) {
                    minus.setEnabled(true);
                }
            }
        });

        minus.setOnClickListener(new View.OnClickListener() {
            @Override
            public void onClick(View view) {
                int sl = Integer.parseInt(tv_soluong.getText().toString()) - 1;
                tv_soluong.setText(String.valueOf(sl));
                if (sl == 1) {
                    minus.setEnabled(false);
                }
            }
        });

        addCart.setOnClickListener(new View.OnClickListener() {
            @Override
            public void onClick(View view) {
                Cursor cursor = gioHangHelper.selectSoLuong(monAn);
                cursor.moveToNext();
                if (cursor.getInt(0) == 1) {
                    gioHangHelper.updateSoLuong(monAn, tv_soluong.getText().toString());
                    monAn.setSoLuongDat(Integer.parseInt(tv_soluong.getText().toString()));
                }
                else {
                    gioHangHelper.insertGioHang(monAn, tv_soluong.getText().toString());
                    monAn.setSoLuongDat(Integer.parseInt(tv_soluong.getText().toString()));
                }
                dialog.dismiss();
            }
        });

        dialog.show();
    }

    public void getLoaiMonAn() {
        OkHttpClient client = new OkHttpClient();
        Request request = new Request.Builder().url("http://10.0.2.2:8080/KFC_ChainStores/loaiMon/getAll").build();

        client.newCall(request).enqueue(new Callback() {
            @Override
            public void onFailure(@NotNull Call call, @NotNull IOException e) {
                Log.d("onFailure", e.getMessage());
            }
            @Override
            public void onResponse(@NonNull Call call, @NonNull final Response response)
                    throws IOException {
                try {
                    String responseData = response.body().string();
                    JSONArray jsonRes = new JSONArray(responseData);
                    for (int i = 0; i < jsonRes.length(); i++) {
                        JSONObject json = jsonRes.getJSONObject(i);
                        int id_loaimon = json.getInt("id_loaimon");
                        String tenLoaiMon = json.getString("tenLoaiMon");

                        runOnUiThread(new Runnable() {
                            @Override
                            public void run() {
                                loaiMonList.add(new LoaiMon(id_loaimon, tenLoaiMon));
                                TabLayout.Tab tab = tabLayout.newTab();
                                tab.setText(tenLoaiMon);
                                tabLayout.addTab(tab);
                            }
                        });
                    }

                } catch (JSONException e) {
                    Log.d("onResponse", e.getMessage());
                }
            }
        });
    }

    public void getListMonAn(int id_loaiMonAn) {
        OkHttpClient client = new OkHttpClient();
        Request request = new Request.Builder().url("http://10.0.2.2:8080/KFC_ChainStores/monAn/getMonAnById/"+id_loaiMonAn).build();

        monAnList.clear();

        client.newCall(request).enqueue(new Callback() {
            @Override
            public void onFailure(@NotNull Call call, @NotNull IOException e) {
                Log.d("onFailure", e.getMessage());
            }
            @Override
            public void onResponse(@NonNull Call call, @NonNull final Response response)
                    throws IOException {
                try {
                    String responseData = response.body().string();
                    JSONArray jsonRes = new JSONArray(responseData);
                    for (int i = 0; i < jsonRes.length(); i++) {
                        JSONObject json = jsonRes.getJSONObject(i);
                        int maMonAn = json.getInt("maMonAn");
                        String tenMonAn = json.getString("tenMonAn");
                        String mota = json.getString("mota");
                        double gia = json.getDouble("gia");
                        String image_path = json.getString("image_path");
                        int id_loaimon = json.getInt("id_loaimon");

                        monAnList.add(new MonAn(
                                maMonAn,
                                tenMonAn,
                                mota,
                                gia,
                                image_path,
                                id_loaimon
                        ));
                    }

                    runOnUiThread(new Runnable() {
                        @SuppressLint("NotifyDataSetChanged")
                        @Override
                        public void run() {
                            listMonAnAdapter.notifyDataSetChanged();
                        }
                    });

                } catch (JSONException e) {
                    Log.d("onResponse", e.getMessage());
                }
            }
        });
    }

    @Override
    public boolean onCreateOptionsMenu(Menu menu) {
        getMenuInflater().inflate(R.menu.action_bar, menu);
        return super.onCreateOptionsMenu(menu);
    }

    @Override
    public boolean onOptionsItemSelected(@NonNull MenuItem item) {
        switch (item.getItemId()) {
            case R.id.cart:
                Intent intent = new Intent(this, GioHangActivity.class);
                startActivity(intent);
                return true;
        }
        return super.onOptionsItemSelected(item);
    }
}